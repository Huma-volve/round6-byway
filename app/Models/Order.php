<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{

    protected $fillable = [
        'user_id',
        'status',
        'currency',
        'subtotal_cents',
        'discount_cents',
        'tax_cents',
        'total_cents',
        'placed_at',
    ];

    // Order belongs to ONE user
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Order has MANY items
    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    // Order has MANY payments
    public function payments()
    {
        return $this->hasMany(Payment::class);
    }
}
