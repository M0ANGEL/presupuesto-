<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::where('user_id', auth()->id())
            ->orderBy('name')
            ->get();
            
        return view('categories.index', compact('categories'));
    }
    
    public function create()
    {
        return view('categories.create');
    }
    
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);
        
        Category::create([
            'user_id' => auth()->id(),
            'name' => $request->name,
        ]);
        
        return redirect()->route('categories.index')
            ->with('success', 'Categoría creada exitosamente.');
    }
    
    public function show(Category $category)
    {
        abort_if($category->user_id !== auth()->id(), 403);
        
        $expenses = $category->expenses()
            ->with('user')
            ->latest()
            ->paginate(10);
            
        return view('categories.show', compact('category', 'expenses'));
    }
    
    public function edit(Category $category)
    {
        abort_if($category->user_id !== auth()->id(), 403);
        
        return view('categories.edit', compact('category'));
    }
    
    public function update(Request $request, Category $category)
    {
        abort_if($category->user_id !== auth()->id(), 403);
        
        $request->validate([
            'name' => 'required|string|max:255',
        ]);
        
        $category->update($request->only('name'));
        
        return redirect()->route('categories.index')
            ->with('success', 'Categoría actualizada exitosamente.');
    }
    
    public function destroy(Category $category)
    {
        abort_if($category->user_id !== auth()->id(), 403);
        
        if ($category->expenses()->count() > 0) {
            return back()->with('error', 'No puedes eliminar una categoría con gastos asociados.');
        }
        
        $category->delete();
        
        return redirect()->route('categories.index')
            ->with('success', 'Categoría eliminada exitosamente.');
    }
}