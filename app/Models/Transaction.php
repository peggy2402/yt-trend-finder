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
        'transaction_code',
        'description',
        'status'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}