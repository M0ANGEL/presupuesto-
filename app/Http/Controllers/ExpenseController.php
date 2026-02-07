<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\Category;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ExpenseController extends Controller
{
    public function index()
    {
        $expenses = Expense::where('user_id', auth()->id())
            ->with('category')
            ->latest()
            ->paginate(15);
            
        $total = $expenses->sum('amount');
        
        return view('expenses.index', compact('expenses', 'total'));
    }
    
    public function create()
    {
        $categories = Category::where('user_id', auth()->id())->get();
        
        return view('expenses.create', compact('categories'));
    }
    
    public function store(Request $request)
    {
        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'amount' => 'required|numeric|min:1',
            'source' => 'required|in:stock,personal',
            'description' => 'nullable|string|max:500',
        ]);
        
        $wallet = auth()->user()->wallet;
        
        if ($request->source === 'stock' && $wallet->stock < $request->amount) {
            return back()->with('error', 'Stock insuficiente');
        }
        
        if ($request->source === 'personal' && $wallet->personal < $request->amount) {
            return back()->with('error', 'Dinero personal insuficiente');
        }
        
        DB::transaction(function () use ($request, $wallet) {
            $expense = Expense::create([
                'user_id' => auth()->id(),
                'category_id' => $request->category_id,
                'amount' => $request->amount,
                'source' => $request->source,
                'description' => $request->description,
            ]);
            
            $wallet->decrement($request->source, $request->amount);
            
            Transaction::create([
                'user_id' => auth()->id(),
                'type' => 'expense',
                'amount' => $request->amount,
                'from' => $request->source,
                'reference_type' => Expense::class,
                'reference_id' => $expense->id,
            ]);
        });
        
        return redirect()->route('dashboard')
            ->with('success', 'Gasto registrado exitosamente.');
    }
    
    public function show(Expense $expense)
    {
        abort_if($expense->user_id !== auth()->id(), 403);
        
        return view('expenses.show', compact('expense'));
    }
    
    public function edit(Expense $expense)
    {
        abort_if($expense->user_id !== auth()->id(), 403);
        
        $categories = Category::where('user_id', auth()->id())->get();
        
        return view('expenses.edit', compact('expense', 'categories'));
    }
    
    public function update(Request $request, Expense $expense)
    {
        abort_if($expense->user_id !== auth()->id(), 403);
        
        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'amount' => 'required|numeric|min:1',
            'description' => 'nullable|string|max:500',
        ]);
        
        DB::transaction(function () use ($expense, $request) {
            $oldAmount = $expense->amount;
            $newAmount = $request->amount;
            
            if ($oldAmount != $newAmount) {
                $difference = $newAmount - $oldAmount;
                $wallet = auth()->user()->wallet;
                
                if ($difference > 0) {
                    // Aumentó el monto, restar diferencia
                    if ($wallet->{$expense->source} < $difference) {
                        throw new \Exception('Fondos insuficientes para ajustar el gasto.');
                    }
                    $wallet->decrement($expense->source, $difference);
                } else {
                    // Disminuyó el monto, sumar diferencia
                    $wallet->increment($expense->source, abs($difference));
                }
            }
            
            $expense->update($request->only(['category_id', 'amount', 'description']));
        });
        
        return redirect()->route('expenses.index')
            ->with('success', 'Gasto actualizado exitosamente.');
    }
    
    public function destroy(Expense $expense)
    {
        abort_if($expense->user_id !== auth()->id(), 403);
        
        DB::transaction(function () use ($expense) {
            $wallet = auth()->user()->wallet;
            $wallet->increment($expense->source, $expense->amount);
            
            Transaction::where('reference_type', Expense::class)
                ->where('reference_id', $expense->id)
                ->delete();
            
            $expense->delete();
        });
        
        return redirect()->route('expenses.index')
            ->with('success', 'Gasto eliminado exitosamente.');
    }
}