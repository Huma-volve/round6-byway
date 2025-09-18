<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payout extends Model
{
    use HasFactory;

    protected $fillable = [
        'instructor_profile_id',
        'payout_method_id',
        'amount',
        'currency',
        'status',        
        'transaction_id',
    ];

    public function instructorProfile()
    {
        return $this->belongsTo(InstructorProfile::class);
    }

    public function payoutMethod()
    {
        return $this->belongsTo(PayoutMethod::class);
    }
}
