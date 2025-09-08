<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentProfile extends Model
{

    protected $fillable = [
        'user_id',
        'headline',
        'about',
        'social_links',
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
