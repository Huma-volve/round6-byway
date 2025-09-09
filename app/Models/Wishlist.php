<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Wishlist extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'course_id',
    ];

    // Wishlist belongs to ONE user
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Wishlist belongs to ONE course
    public function course()
    {
        return $this->belongsTo(Course::class);
    }
}
