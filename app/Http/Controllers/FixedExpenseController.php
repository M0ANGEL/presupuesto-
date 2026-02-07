<?php

namespace App\Http\Controllers;

use App\Models\FixedExpense;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FixedExpenseController extends Controller
{
    public function index()
    {
        $currentMonth = now()->format('Y-m');
        $fixedExpenses = FixedExpense::where('user_id', auth()->id())
            ->where('month', $currentMonth)
            ->orderBy('due_day')
            ->get();
            
        $pending = $fixedExpenses->where('paid_at', null)->sum('amount');
        $paid = $fixedExpenses->where('paid_at', '!=', null)->sum('amount');
        
        return view('fixed-expenses.index', compact('fixedExpenses', 'pending', 'paid', 'currentMonth'));
    }
    
    public function create()
    {
        return view('fixed-expenses.create');
    }
    
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'amount' => 'required|numeric|min:1',
            'due_day' => 'required|integer|min:1|max:31',
            'month' => 'required|date_format:Y-m',
            'description' => 'nullable|string|max:500',
        ]);
        
        FixedExpense::create([
            'user_id' => auth()->id(),
            'name' => $request->name,
            'amount' => $request->amount,
            'due_day' => $request->due_day,
            'month' => $request->month,
            'description' => $request->description,
        ]);
        
        return redirect()->route('dashboard')
            ->with('success', 'Gasto fijo creado exitosamente.');
    }
    
    public function markAsPaid(FixedExpense $fixedExpense)
    {
        abort_if($fixedExpense->user_id !== auth()->id(), 403);
        
        if ($fixedExpense->paid_at) {
            return back()->with('info', 'Este gasto ya estaba marcado como pagado.');
        }
        
        $wallet = auth()->user()->wallet;
        
        if ($wallet->stock < $fixedExpense->amount) {
            return back()->with('error', 'No tienes suficiente dinero en stock para pagar este gasto.');
        }
        
        DB::transaction(function () use ($fixedExpense, $wallet) {
            $fixedExpense->update(['paid_at' => now()]);
            $wallet->decrement('stock', $fixedExpense->amount);
            
            Transaction::create([
                'user_id' => auth()->id(),
                'type' => 'expense',
                'amount' => $fixedExpense->amount,
                'from' => 'stock',
                'description' => "Pago de gasto fijo: {$fixedExpense->name}",
                'reference_type' => FixedExpense::class,
                'reference_id' => $fixedExpense->id,
            ]);
        });
        
        return back()->with('success', 'Gasto fijo pagado exitosamente.');
    }
    
    public function markAsUnpaid(FixedExpense $fixedExpense)
    {
        abort_if($fixedExpense->user_id !== auth()->id(), 403);
        
        if (!$fixedExpense->paid_at) {
            return back()->with('info', 'Este gasto ya estaba marcado como no pagado.');
        }
        
        DB::transaction(function () use ($fixedExpense) {
            $wallet = auth()->user()->wallet;
            $fixedExpense->update(['paid_at' => null]);
            $wallet->increment('stock', $fixedExpense->amount);
            
            Transaction::where('reference_type', FixedExpense::class)
                ->where('reference_id', $fixedExpense->id)
                ->delete();
        });
        
        return back()->with('success', 'Gasto fijo marcado como no pagado.');
    }
    
    public function show(FixedExpense $fixedExpense)
    {
        abort_if($fixedExpense->user_id !== auth()->id(), 403);
        
        return view('fixed-expenses.show', compact('fixedExpense'));
    }
    
    public function edit(FixedExpense $fixedExpense)
    {
        abort_if($fixedExpense->user_id !== auth()->id(), 403);
        
        return view('fixed-expenses.edit', compact('fixedExpense'));
    }
    
    public function update(Request $request, FixedExpense $fixedExpense)
    {
        abort_if($fixedExpense->user_id !== auth()->id(), 403);
        
        $request->validate([
            'name' => 'required|string|max:255',
            'amount' => 'required|numeric|min:1',
            'due_day' => 'required|integer|min:1|max:31',
            'month' => 'required|date_format:Y-m',
            'description' => 'nullable|string|max:500',
        ]);
        
        DB::transaction(function () use ($fixedExpense, $request) {
            if ($fixedExpense->paid_at && $fixedExpense->amount != $request->amount) {
                $difference = $request->amount - $fixedExpense->amount;
                $wallet = auth()->user()->wallet;
                
                if ($difference > 0) {
                    if ($wallet->stock < $difference) {
                        throw new \Exception('No hay suficiente stock para este ajuste.');
                    }
                    $wallet->decrement('stock', $difference);
                } else {
                    $wallet->increment('stock', abs($difference));
                }
            }
            
            $fixedExpense->update($request->all());
        });
        
        return redirect()->route('fixed-expenses.index')
            ->with('success', 'Gasto fijo actualizado exitosamente.');
    }
    
    public function destroy(FixedExpense $fixedExpense)
    {
        abort_if($fixedExpense->user_id !== auth()->id(), 403);
        
        DB::transaction(function () use ($fixedExpense) {
            if ($fixedExpense->paid_at) {
                $wallet = auth()->user()->wallet;
                $wallet->increment('stock', $fixedExpense->amount);
            }
            
            Transaction::where('reference_type', FixedExpense::class)
                ->where('reference_id', $fixedExpense->id)
                ->delete();
            
            $fixedExpense->delete();
        });
        
        return redirect()->route('fixed-expenses.index')
            ->with('success', 'Gasto fijo eliminado exitosamente.');
    }
}