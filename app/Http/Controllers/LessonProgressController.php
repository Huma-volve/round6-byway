<?php

namespace App\Http\Controllers;

use App\Models\Enrollment;
use App\Models\Lesson;
use App\Models\LessonProgress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LessonProgressController extends Controller
{
        public function markAsCompleted(Request $request, Lesson $lesson)
    {
        $user = Auth::user();
        // تأكد إن الطالب مسجل في الكورس
        $enrollment = Enrollment::where('course_id', $lesson->course_id)
            ->where('student_id', $user->id)
            ->first();
            if (!$enrollment) {
                return response()->json([
                    'message' => 'User is not enrolled in this course.'
                ], 400);
            }    

        // سجل التقدم لو مش موجود
        LessonProgress::updateOrCreate(
            [
                'enrollment_id' => $enrollment->id,
                'lesson_id' => $lesson->id,
            ],
            ['is_completed' => true]
        );

        // احسب نسبة التقدم
        $totalLessons = Lesson::where('course_id','=', $lesson->course_id)->count();
        $completedLessons = LessonProgress::where('enrollment_id', $enrollment->id)
            ->where('is_completed', true)
            ->count();

        $progressPercentage = $totalLessons > 0
            ? round(($completedLessons / $totalLessons) * 100)
            : 0;

        // حدث enrollment
        $enrollment->update([
            'progress_percentage' => $progressPercentage,
        ]);

        return response()->json([
            'message' => 'Lesson marked as completed.',
            'lesson_id' => $lesson->id,
            'course_id' => $lesson->course_id,
            'progress' => [
                'completed_lessons' => $completedLessons,
                'total_lessons' => $totalLessons,
                'percentage' => $progressPercentage,
            ]
        ]);
    }
}
