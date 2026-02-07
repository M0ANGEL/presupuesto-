<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Income extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'type',
        'name',
        'amount',
        'payment_method',
        'expected_date',
        'description',
        'paid_at'
    ];

    protected $casts = [
        'paid_at' => 'datetime',
        'expected_date' => 'date'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}