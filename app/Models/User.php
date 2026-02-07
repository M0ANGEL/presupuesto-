<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = ['name', 'email', 'password'];

    protected $hidden = ['password', 'remember_token'];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    // Relaciones
    public function wallet()
    {
        return $this->hasOne(Wallet::class);
    }

    public function incomes()
    {
        return $this->hasMany(Income::class);
    }

    public function fixedExpenses()
    {
        return $this->hasMany(FixedExpense::class);
    }

    public function expenses()
    {
        return $this->hasMany(Expense::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function categories()
    {
        return $this->hasMany(Category::class);
    }

    // Métodos helper
    public function getMonthlySummaryAttribute()
    {
        $month = now()->format('Y-m');
        
        return [
            'total_income' => $this->incomes()
                ->whereYear('paid_at', now()->year)
                ->whereMonth('paid_at', now()->month)
                ->sum('amount'),
            'total_expenses' => $this->expenses()
                ->whereYear('created_at', now()->year)
                ->whereMonth('created_at', now()->month)
                ->sum('amount'),
            'pending_fixed_expenses' => $this->fixedExpenses()
                ->where('month', $month)
                ->whereNull('paid_at')
                ->sum('amount'),
        ];
    }
}