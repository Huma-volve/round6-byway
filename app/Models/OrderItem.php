<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{

    protected $fillable = [
        'order_id',
        'purchasable_type',
        'purchasable_id',
        'unit_price_cents',
        'quantity',
        'total_cents',
    ];


    // OrderItem belongs to ONE order
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    // OrderItem can be a Course (or future Bundle)
    public function purchasable()
    {
        return $this->morphTo();
    }
}
