<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Enrollment extends Model
{

    protected $fillable = [
        'course_id',
        'student_id',
        'enrolled_at',
        'progress_percentage',
    ];


    // Enrollment belongs to ONE student (User)
    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    // Enrollment belongs to ONE course
    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    // Enrollment has MANY lesson progresses
    public function lessonProgresses()
    {
        return $this->hasMany(LessonProgress::class);
    }
}
