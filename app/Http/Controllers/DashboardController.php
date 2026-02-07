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
            
        return view('dashboard', [
            'wallet' => $user->wallet,
            'fixedExpenses' => $fixedExpenses,
            'incomes' => $incomes,
            'categories' => $categories,
            'expenses' => $expenses,
            'currentMonth' => $currentMonth,
        ]);
    }

    public function monthlySummary()
    {
        $user = auth()->user();
        $currentMonth = now()->format('Y-m');
        
        $data = [
            'totalExpenses' => $user->expenses()
                ->whereMonth('created_at', now()->month)
                ->sum('amount'),
            'totalIncomes' => $user->incomes()
                ->where('paid_at', '!=', null)
                ->whereMonth('paid_at', now()->month)
                ->sum('amount'),
            'fixedExpensesPaid' => $user->fixedExpenses()
                ->where('month', $currentMonth)
                ->where('paid_at', '!=', null)
                ->sum('amount'),
            'fixedExpensesPending' => $user->fixedExpenses()
                ->where('month', $currentMonth)
                ->where('paid_at', null)
                ->sum('amount'),
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
                    ->sum('amount'),
                'expense' => $user->expenses()
                    ->whereMonth('created_at', $month->month)
                    ->whereYear('created_at', $month->year)
                    ->sum('amount'),
            ];
        }
        
        return response()->json($data);
    }
    
    public function quickStats()
    {
        $user = auth()->user();
        $currentMonth = now()->format('Y-m');
        
        $stats = [
            'wallet' => $user->wallet,
            'pendingIncomes' => $user->incomes()
                ->where('paid_at', null)
                ->sum('amount'),
            'pendingFixedExpenses' => $user->fixedExpenses()
                ->where('month', $currentMonth)
                ->where('paid_at', null)
                ->sum('amount'),
            'monthlyExpenses' => $user->expenses()
                ->whereMonth('created_at', now()->month)
                ->sum('amount'),
        ];
        
        return response()->json($stats);
    }
}