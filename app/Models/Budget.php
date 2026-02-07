<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Budget extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'category_id',
        'amount',
        'month',
        'spent'
    ];

    protected $casts = [
        'month' => 'date:Y-m',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function getRemainingAttribute()
    {
        return $this->amount - $this->spent;
    }

    public function getProgressPercentageAttribute()
    {
        return $this->amount > 0 ? ($this->spent / $this->amount) * 100 : 0;
    }
}