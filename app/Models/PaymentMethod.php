<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentMethod extends Model
{
    protected $fillable = [
        'user_id',
        'stripe_payment_method_id',
        'provider',
        'brand',
        'last_four',
        'is_default',
    ];
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
