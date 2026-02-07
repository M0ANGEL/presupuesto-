<?php

namespace App\Http\Controllers;

use App\Models\Income;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class IncomeController extends Controller
{
    public function index()
    {
        $incomes = Income::where('user_id', auth()->id())
            ->latest()
            ->paginate(15);
            
        $total = $incomes->where('paid_at', '!=', null)->sum('amount');
        $pending = $incomes->where('paid_at', null)->sum('amount');
        
        return view('incomes.index', compact('incomes', 'total', 'pending'));
    }
    
    public function create()
    {
        return view('incomes.create');
    }
    
    public function store(Request $request)
    {
        $request->validate([
            'type' => 'required|in:fixed,variable',
            'name' => 'required|string|max:255',
            'amount' => 'required|numeric|min:1',
            'payment_method' => 'nullable|in:nequi,efectivo,banco',
            'expected_date' => 'nullable|date',
            'description' => 'nullable|string|max:500',
        ]);
        
        DB::transaction(function () use ($request) {
            Income::create([
                'user_id' => auth()->id(),
                'type' => $request->type,
                'name' => $request->name,
                'amount' => $request->amount,
                'payment_method' => $request->payment_method,
                'expected_date' => $request->expected_date,
                'description' => $request->description,
            ]);
        });
        
        return redirect()->route('dashboard')
            ->with('success', 'Ingreso registrado exitosamente.');
    }
    
    public function markAsPaid(Income $income)
    {
        abort_if($income->user_id !== auth()->id(), 403);
        
        if ($income->paid_at) {
            return back()->with('info', 'Este ingreso ya estaba marcado como pagado.');
        }
        
        DB::transaction(function () use ($income) {
            $income->update(['paid_at' => now()]);
            
            $wallet = auth()->user()->wallet;
            $wallet->increment('stock', $income->amount);
            
            Transaction::create([
                'user_id' => auth()->id(),
                'type' => 'income',
                'amount' => $income->amount,
                'to' => 'stock',
                'reference_type' => Income::class,
                'reference_id' => $income->id,
            ]);
        });
        
        return back()->with('success', 'Ingreso marcado como pagado y añadido al stock.');
    }
    
    public function markAsUnpaid(Income $income)
    {
        abort_if($income->user_id !== auth()->id(), 403);
        
        if (!$income->paid_at) {
            return back()->with('info', 'Este ingreso ya estaba marcado como no pagado.');
        }
        
        DB::transaction(function () use ($income) {
            $wallet = auth()->user()->wallet;
            
            if ($wallet->stock < $income->amount) {
                throw new \Exception('No hay suficiente stock para revertir este ingreso.');
            }
            
            $income->update(['paid_at' => null]);
            $wallet->decrement('stock', $income->amount);
            
            Transaction::where('reference_type', Income::class)
                ->where('reference_id', $income->id)
                ->delete();
        });
        
        return back()->with('success', 'Ingreso marcado como no pagado.');
    }
    
    public function show(Income $income)
    {
        abort_if($income->user_id !== auth()->id(), 403);
        
        return view('incomes.show', compact('income'));
    }
    
    public function edit(Income $income)
    {
        abort_if($income->user_id !== auth()->id(), 403);
        
        return view('incomes.edit', compact('income'));
    }
    
    public function update(Request $request, Income $income)
    {
        abort_if($income->user_id !== auth()->id(), 403);
        
        $request->validate([
            'type' => 'required|in:fixed,variable',
            'name' => 'required|string|max:255',
            'amount' => 'required|numeric|min:1',
            'payment_method' => 'nullable|in:nequi,efectivo,banco',
            'expected_date' => 'nullable|date',
            'description' => 'nullable|string|max:500',
        ]);
        
        DB::transaction(function () use ($income, $request) {
            if ($income->paid_at && $income->amount != $request->amount) {
                $difference = $request->amount - $income->amount;
                $wallet = auth()->user()->wallet;
                
                if ($difference > 0) {
                    $wallet->increment('stock', $difference);
                } else {
                    if ($wallet->stock < abs($difference)) {
                        throw new \Exception('No hay suficiente stock para este ajuste.');
                    }
                    $wallet->decrement('stock', abs($difference));
                }
            }
            
            $income->update($request->all());
        });
        
        return redirect()->route('incomes.index')
            ->with('success', 'Ingreso actualizado exitosamente.');
    }
    
    public function destroy(Income $income)
    {
        abort_if($income->user_id !== auth()->id(), 403);
        
        DB::transaction(function () use ($income) {
            if ($income->paid_at) {
                $wallet = auth()->user()->wallet;
                if ($wallet->stock < $income->amount) {
                    throw new \Exception('No hay suficiente stock para eliminar este ingreso.');
                }
                $wallet->decrement('stock', $income->amount);
            }
            
            Transaction::where('reference_type', Income::class)
                ->where('reference_id', $income->id)
                ->delete();
            
            $income->delete();
        });
        
        return redirect()->route('incomes.index')
            ->with('success', 'Ingreso eliminado exitosamente.');
    }
}