<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'type',
        'amount',
        'status',
        'payment_method_id',
        'meta',
    ];

    protected $casts = [
        'meta' => 'array',
    ];

    /**
     * Each transaction belongs to one user (usually instructor).
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Student who paid in case of payment transaction.
     */
    public function paidBy()
    {
        if (!isset($this->meta['paid_by'])) {
            return null;
        }
        return User::find($this->meta['paid_by']);
    }

    /**
     * Payment method (Stripe card, PayPal, etc.)
     */
    public function paymentMethod()
    {
        return $this->belongsTo(PaymentMethod::class);
    }
}
