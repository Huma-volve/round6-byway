<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PayoutMethod extends Model
{
    use HasFactory;

    protected $fillable = [
        'instructor_profile_id',
        'provider',       // bank_transfer
        'account_name',
        'account_number',
        'bank_name',
        'is_default',
    ];

    public function instructorProfile()
    {
        return $this->belongsTo(InstructorProfile::class);
    }

    public function payouts()
    {
        return $this->hasMany(Payout::class);
    }
}
