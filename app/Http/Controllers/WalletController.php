<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WalletController extends Controller
{
    public function moveToSaving(Request $request)
    {
        $request->validate(['amount' => 'required|numeric|min:1']);
        
        $wallet = auth()->user()->wallet;
        
        if ($wallet->stock < $request->amount) {
            return back()->with('error', 'Fondos insuficientes en stock.');
        }
        
        DB::transaction(function () use ($wallet, $request) {
            $wallet->decrement('stock', $request->amount);
            $wallet->increment('saving', $request->amount);
            
            Transaction::create([
                'user_id' => auth()->id(),
                'type' => 'transfer',
                'amount' => $request->amount,
                'from' => 'stock',
                'to' => 'saving',
            ]);
        });
        
        return back()->with('success', 'Dinero transferido a ahorros exitosamente.');
    }
    
    public function moveToPersonal(Request $request)
    {
        $request->validate(['amount' => 'required|numeric|min:1']);
        
        $wallet = auth()->user()->wallet;
        
        if ($wallet->stock < $request->amount) {
            return back()->with('error', 'Fondos insuficientes en stock.');
        }
        
        DB::transaction(function () use ($wallet, $request) {
            $wallet->decrement('stock', $request->amount);
            $wallet->increment('personal', $request->amount);
            
            Transaction::create([
                'user_id' => auth()->id(),
                'type' => 'transfer',
                'amount' => $request->amount,
                'from' => 'stock',
                'to' => 'personal',
            ]);
        });
        
        return back()->with('success', 'Dinero transferido a personal exitosamente.');
    }
    
    public function moveToStock(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:1',
            'from' => 'required|in:saving,personal',
        ]);
        
        $wallet = auth()->user()->wallet;
        
        if ($request->from === 'saving' && $wallet->saving < $request->amount) {
            return back()->with('error', 'Fondos insuficientes en ahorros.');
        }
        
        if ($request->from === 'personal' && $wallet->personal < $request->amount) {
            return back()->with('error', 'Fondos insuficientes en personal.');
        }
        
        DB::transaction(function () use ($wallet, $request) {
            $wallet->decrement($request->from, $request->amount);
            $wallet->increment('stock', $request->amount);
            
            Transaction::create([
                'user_id' => auth()->id(),
                'type' => 'transfer',
                'amount' => $request->amount,
                'from' => $request->from,
                'to' => 'stock',
            ]);
        });
        
        return back()->with('success', 'Dinero transferido a stock exitosamente.');
    }
    
    public function transferBetweenAccounts(Request $request)
    {
        $request->validate([
            'from' => 'required|in:stock,saving,personal',
            'to' => 'required|in:stock,saving,personal|different:from',
            'amount' => 'required|numeric|min:1',
        ]);
        
        $wallet = auth()->user()->wallet;
        
        if ($wallet->{$request->from} < $request->amount) {
            return back()->with('error', 'Fondos insuficientes en la cuenta de origen.');
        }
        
        DB::transaction(function () use ($wallet, $request) {
            $wallet->decrement($request->from, $request->amount);
            $wallet->increment($request->to, $request->amount);
            
            Transaction::create([
                'user_id' => auth()->id(),
                'type' => 'transfer',
                'amount' => $request->amount,
                'from' => $request->from,
                'to' => $request->to,
            ]);
        });
        
        return back()->with('success', 'Transferencia realizada exitosamente.');
    }
    
    public function history()
    {
        $transactions = Transaction::where('user_id', auth()->id())
            ->latest()
            ->paginate(20);
            
        return view('wallet.history', compact('transactions'));
    }
    
    public function showAdjustForm()
    {
        $wallet = auth()->user()->wallet;
        
        return view('wallet.adjust', compact('wallet'));
    }
    
    public function adjustBalance(Request $request)
    {
        $request->validate([
            'account' => 'required|in:stock,saving,personal',
            'type' => 'required|in:add,subtract,set',
            'amount' => 'required|numeric|min:0',
            'reason' => 'required|string|max:255',
        ]);
        
        DB::transaction(function () use ($request) {
            $wallet = auth()->user()->wallet;
            $oldAmount = $wallet->{$request->account};
            
            switch ($request->type) {
                case 'add':
                    $newAmount = $oldAmount + $request->amount;
                    $wallet->increment($request->account, $request->amount);
                    break;
                    
                case 'subtract':
                    if ($oldAmount < $request->amount) {
                        throw new \Exception('No hay suficiente saldo para restar.');
                    }
                    $newAmount = $oldAmount - $request->amount;
                    $wallet->decrement($request->account, $request->amount);
                    break;
                    
                case 'set':
                    $newAmount = $request->amount;
                    $wallet->update([$request->account => $request->amount]);
                    break;
            }
            
            Transaction::create([
                'user_id' => auth()->id(),
                'type' => 'adjustment',
                'amount' => $request->amount,
                'from' => $request->type === 'subtract' ? $request->account : null,
                'to' => $request->type === 'add' ? $request->account : null,
                'description' => $request->reason . ' (Ajuste manual)',
            ]);
        });
        
        return redirect()->route('dashboard')
            ->with('success', 'Saldo ajustado exitosamente.');
    }
}