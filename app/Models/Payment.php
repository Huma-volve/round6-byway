<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
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
        'paid_at' => 'datetime',

    ];

    // Payment belongs to ONE order
    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id');
    }


    public function enrollment()
    {
        return $this->belongsTo(Enrollment::class, 'enrollment_id');
    }


    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }
    public function instructorEarnings()
    {
        return $this->hasOne(InstructorEarnings::class);
    }
}
