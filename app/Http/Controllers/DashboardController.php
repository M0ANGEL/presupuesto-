<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\FixedExpense;
use App\Models\Income;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $currentMonth = now()->format('Y-m');
        
        // Gastos fijos del mes actual
        $fixedExpenses = FixedExpense::where('user_id', $user->id)
            ->where('month', $currentMonth)
            ->orderBy('due_day')
            ->get();
            
        // Ingresos pendientes (tanto fijos como variables)
        $incomes = Income::where('user_id', $user->id)
            ->where('paid_at', null)
            ->orderBy('expected_date', 'asc')
            ->get();
        
        // Categorías para gastos rápidos
        $categories = Category::where('user_id', $user->id)->get();
        
        // Últimos gastos
        $expenses = $user->expenses()
            ->with('category')
            ->latest()
            ->take(5)
            ->get();
        
        // ✅ CORRECCIÓN: Obtener wallet con valor por defecto si es null
        $wallet = $user->wallet ?? 0;
        
        // ✅ CORRECCIÓN: Verificar que las relaciones no sean null
        $totalExpenses = $user->expenses()->whereMonth('created_at', now()->month)->sum('amount') ?? 0;
        $totalIncomes = $user->incomes()->where('paid_at', '!=', null)->whereMonth('paid_at', now()->month)->sum('amount') ?? 0;
        
        return view('dashboard', [
            'wallet' => $wallet, // ✅ Usar variable corregida
            'fixedExpenses' => $fixedExpenses,
            'incomes' => $incomes,
            'categories' => $categories,
            'expenses' => $expenses,
            'currentMonth' => $currentMonth,
            'totalExpenses' => $totalExpenses, // ✅ Valor por defecto
            'totalIncomes' => $totalIncomes,   // ✅ Valor por defecto
        ]);
    }

    public function monthlySummary()
    {
        $user = auth()->user();
        $currentMonth = now()->format('Y-m');
        
        $data = [
            'totalExpenses' => $user->expenses()
                ->whereMonth('created_at', now()->month)
                ->sum('amount') ?? 0, // ✅ Valor por defecto
            'totalIncomes' => $user->incomes()
                ->where('paid_at', '!=', null)
                ->whereMonth('paid_at', now()->month)
                ->sum('amount') ?? 0, // ✅ Valor por defecto
            'fixedExpensesPaid' => $user->fixedExpenses()
                ->where('month', $currentMonth)
                ->where('paid_at', '!=', null)
                ->sum('amount') ?? 0, // ✅ Valor por defecto
            'fixedExpensesPending' => $user->fixedExpenses()
                ->where('month', $currentMonth)
                ->where('paid_at', null)
                ->sum('amount') ?? 0, // ✅ Valor por defecto
        ];
        
        return response()->json($data);
    }
    
    public function expensesByCategory()
    {
        $user = auth()->user();
        
        $expensesByCategory = DB::table('expenses')
            ->join('categories', 'expenses.category_id', '=', 'categories.id')
            ->select('categories.name', DB::raw('SUM(expenses.amount) as total'))
            ->where('expenses.user_id', $user->id)
            ->whereMonth('expenses.created_at', now()->month)
            ->groupBy('categories.name')
            ->get();
            
        return response()->json($expensesByCategory);
    }
    
    public function incomeExpenseChart()
    {
        $user = auth()->user();
        
        $data = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $monthStr = $month->format('Y-m');
            
            $data[] = [
                'month' => $month->translatedFormat('M'),
                'income' => $user->incomes()
                    ->where('paid_at', '!=', null)
                    ->whereMonth('paid_at', $month->month)
                    ->whereYear('paid_at', $month->year)
                    ->sum('amount') ?? 0, // ✅ Valor por defecto
                'expense' => $user->expenses()
                    ->whereMonth('created_at', $month->month)
                    ->whereYear('created_at', $month->year)
                    ->sum('amount') ?? 0, // ✅ Valor por defecto
            ];
        }
        
        return response()->json($data);
    }
    
    public function quickStats()
    {
        $user = auth()->user();
        $currentMonth = now()->format('Y-m');
        
        $stats = [
            'wallet' => $user->wallet ?? 0, // ✅ CORRECCIÓN: Valor por defecto
            'pendingIncomes' => $user->incomes()
                ->where('paid_at', null)
                ->sum('amount') ?? 0, // ✅ Valor por defecto
            'pendingFixedExpenses' => $user->fixedExpenses()
                ->where('month', $currentMonth)
                ->where('paid_at', null)
                ->sum('amount') ?? 0, // ✅ Valor por defecto
            'monthlyExpenses' => $user->expenses()
                ->whereMonth('created_at', now()->month)
                ->sum('amount') ?? 0, // ✅ Valor por defecto
        ];
        
        return response()->json($stats);
    }
}