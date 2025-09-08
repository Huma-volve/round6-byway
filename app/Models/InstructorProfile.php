<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InstructorProfile extends Model
{

    protected $fillable = [
        'user_id',
        'headline',
        'about',
        'skills',       // stored as string (comma separated) or JSON
        'experience',   // full experience description
        'company_name',
        'start_date',
        'end_date',
        'social_links', // JSON
        'image_path',
    ];

    protected $casts = [
        'social_links' => 'array', // store social links as JSON
    ];

    // Profile belongs to ONE user
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
