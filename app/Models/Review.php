<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_id',
        'user_id',
        'rating',
        'comment',
        'status'
    ];

    // Review belongs to ONE course
    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    // Review belongs to ONE user
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
