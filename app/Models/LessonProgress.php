<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LessonProgress extends Model
{

    use HasFactory;
    protected $fillable = [
        'enrollment_id',
        'lesson_id',
        'is_completed',
    ];

    // Progress belongs to ONE enrollment
    public function enrollment()
    {
        return $this->belongsTo(Enrollment::class);
    }

    // Progress belongs to ONE lesson
    public function lesson()
    {
        return $this->belongsTo(Lesson::class);
    }
}
