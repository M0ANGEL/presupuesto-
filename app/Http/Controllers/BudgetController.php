<?php

namespace App\Http\Controllers;

use App\Models\Budget;
use App\Models\Category;
use Illuminate\Http\Request;

class BudgetController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'amount' => 'required|numeric|min:1',
            'month' => 'required|date_format:Y-m',
        ]);

        Budget::updateOrCreate(
            [
                'user_id' => auth()->id(),
                'category_id' => $request->category_id,
                'month' => $request->month,
            ],
            [
                'amount' => $request->amount,
                'spent' => 0,
            ]
        );

        return back()->with('success', 'Presupuesto actualizado');
    }

    public function destroy(Budget $budget)
    {
        abort_if($budget->user_id !== auth()->id(), 403);
        $budget->delete();
        
        return back()->with('success', 'Presupuesto eliminado');
    }
}