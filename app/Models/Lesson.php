<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Lesson extends Model
{
    protected $fillable = [
        'course_id',
        'title',
        'order',
        'video_url',
        'video_provider',
        'duration_minutes',
    ];


    // Lesson belongs to ONE course
    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    // Lesson has MANY lesson progress records
    public function progresses()
    {
        return $this->hasMany(LessonProgress::class);
    }
}
