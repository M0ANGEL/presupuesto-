<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Category;

class SettingsController extends Controller
{
    // Página principal de configuración
    public function index()
    {
        $user = auth()->user();
        $categories = Category::where('user_id', $user->id)
            ->orderBy('name')
            ->get();
            
        return view('settings.index', compact('user', 'categories'));
    }
    
    // Página de categorías
    public function categories()
    {
        $user = auth()->user();
        $categories = Category::where('user_id', $user->id)
            ->orderBy('name')
            ->get();
            
        return view('settings.categories', compact('categories'));
    }
    
    // Guardar nueva categoría
    public function storeCategory(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:categories,name,NULL,id,user_id,' . auth()->id(),
        ]);
        
        Category::create([
            'user_id' => auth()->id(),
            'name' => $request->name,
        ]);
        
        return back()->with('success', 'Categoría creada exitosamente.');
    }
    
    // Actualizar categoría
    public function updateCategory(Request $request, Category $category)
    {
        abort_if($category->user_id !== auth()->id(), 403);
        
        $request->validate([
            'name' => 'required|string|max:255|unique:categories,name,' . $category->id . ',id,user_id,' . auth()->id(),
        ]);
        
        $category->update(['name' => $request->name]);
        
        return back()->with('success', 'Categoría actualizada exitosamente.');
    }
    
    // Eliminar categoría
    public function destroyCategory(Category $category)
    {
        abort_if($category->user_id !== auth()->id(), 403);
        
        // Verificar que no tenga gastos asociados
        if ($category->expenses()->count() > 0) {
            return back()->with('error', 'No puedes eliminar una categoría con gastos asociados.');
        }
        
        $category->delete();
        
        return back()->with('success', 'Categoría eliminada exitosamente.');
    }
    
    // Página de notificaciones
    public function notifications()
    {
        $user = auth()->user();
        
        return view('settings.notifications', compact('user'));
    }
    
    // Actualizar configuración de notificaciones
    public function updateNotifications(Request $request)
    {
        $user = auth()->user();
        
        $request->validate([
            'notify_expenses' => 'boolean',
            'notify_incomes' => 'boolean',
            'notify_low_balance' => 'boolean',
            'low_balance_threshold' => 'nullable|numeric|min:0',
        ]);
        
        // Aquí podrías guardar estas preferencias en la base de datos
        // Por ahora solo mostramos un mensaje
        return back()->with('success', 'Configuración de notificaciones guardada.');
    }
    
    // Página de copias de seguridad
    public function backup()
    {
        return view('settings.backup');
    }
    
    // Actualizar perfil de usuario
    public function updateProfile(Request $request)
    {
        $user = auth()->user();
        
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'current_password' => 'nullable|required_with:new_password',
            'new_password' => 'nullable|min:8|confirmed',
        ]);
        
        // Actualizar nombre y email
        $user->update([
            'name' => $request->name,
            'email' => $request->email,
        ]);
        
        // Actualizar contraseña si se proporcionó
        if ($request->filled('new_password')) {
            if (!Hash::check($request->current_password, $user->password)) {
                return back()->with('error', 'La contraseña actual es incorrecta.');
            }
            
            $user->update([
                'password' => Hash::make($request->new_password),
            ]);
        }
        
        return back()->with('success', 'Perfil actualizado exitosamente.');
    }
}