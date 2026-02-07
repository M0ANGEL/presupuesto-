<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'type',
        'amount',
        'from',
        'to',
        'description',
        'reference_type',
        'reference_id'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relación polimórfica
    public function reference()
    {
        return $this->morphTo();
    }
}