<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InstructorEarnings extends Model
{
    protected $fillable = [
        'instructor_profile_id',
        'Course_earnings',
        'currency',
        'payment_id',
    ];
    public function instructorProfile()
    {
        return $this->belongsTo(InstructorProfile::class);
    }
    public function payment()
    {
        return $this->belongsTo(Payment::class);
    }
}
