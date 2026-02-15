<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;
use Carbon\Carbon;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        
        // Obtener el mes seleccionado (por defecto mes actual)
        $selectedMonth = $request->input('month', now()->format('Y-m'));
        
        // Filtrar por mes
        $query = Transaction::where('user_id', $user->id)
            ->whereYear('created_at', Carbon::parse($selectedMonth)->year)
            ->whereMonth('created_at', Carbon::parse($selectedMonth)->month);
        
        // Filtrar por tipo si se selecciona
        if ($request->filled('type') && $request->type != '') {
            $query->where('type', $request->type);
        }
        
        // Obtener transacciones paginadas
        $transactions = $query->latest()->paginate(100);
        
        // Calcular totales
        $totalIncome = $transactions->where('type', 'income')->sum('amount');
        $totalExpense = $transactions->where('type', 'expense')->sum('amount');
        $totalTransfer = $transactions->where('type', 'transfer')->sum('amount');
        
        // Generar lista de meses disponibles (últimos 12 meses)
        $months = [];
        for ($i = 0; $i < 12; $i++) {
            $date = now()->subMonths($i);
            $months[] = [
                'value' => $date->format('Y-m'),
                'label' => $date->translatedFormat('F Y')
            ];
        }
        
        return view('transactions.index', compact(
            'transactions', 
            'totalIncome', 
            'totalExpense',
            'totalTransfer',
            'months',
            'selectedMonth'
        ));
    }
    
    public function show(Transaction $transaction)
    {
        abort_if($transaction->user_id !== auth()->id(), 403);
        
        return view('transactions.show', compact('transaction'));
    }
    
    public function export()
    {
        return back()->with('info', 'Función de exportación en desarrollo.');
    }
}