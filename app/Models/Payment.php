<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{

    protected $fillable = [
        'order_id',
        'provider',
        'method',
        'amount_cents',
        'currency',
        'status',
        'external_id',
        'error_code',
        'error_message',
        'paid_at',
        'meta',
    ];

    protected $casts = [
        'meta' => 'array', // store gateway payloads as JSON
    ];

    // Payment belongs to ONE order
    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
