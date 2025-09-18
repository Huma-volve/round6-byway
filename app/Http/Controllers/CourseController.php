<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Enrollment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CourseController extends Controller
{
    public function show($id)
    {
        $course = Course::with([
            'instructor:id,first_name,last_name',
            'lessons:id,course_id,title,video_url,duration_minutes',
            'reviews.user:id,first_name,last_name'
        ])->findOrFail($id);

        // لو اليوزر مسجل في الكورس نحسب progress
        $enrollment = Enrollment::where('course_id', $course->id)
            ->where('student_id', Auth::id())
            ->with('lessonProgress')
            ->first();

        $progress = null;
        if ($enrollment) {
            $completedLessons = $enrollment->lessonProgress()
                ->where('is_completed', true)
                ->count();

            $totalLessons = $course->lessons()->count();

            $progress = [
                'completed_lessons'   => $completedLessons,
                'total_lessons'       => $totalLessons,
                'progress_percentage' => $totalLessons > 0
                    ? round(($completedLessons / $totalLessons) * 100, 2)
                    : 0,
            ];
        }

        return response()->json([
            'id'          => $course->id,
            'title'       => $course->title,
            'description' => $course->description,
            'image'       => $course->image,
            'level'       => $course->level,
            'duration_hours' => $course->duration_hours,

            // Instructor info
            'instructor' => [
                'id'   => $course->instructor->id,
                'name' => $course->instructor->first_name . ' ' . $course->instructor->last_name,
            ],

            // Lessons
            'lessons' => $course->lessons->map(fn($lesson) => [
                'id'       => $lesson->id,
                'title'    => $lesson->title,
                'video_url'=> $lesson->video_url,
                'duration' => $lesson->duration_minutes,
            ]),

            // Reviews
            'reviews' => $course->reviews->map(fn($review) => [
                'id'      => $review->id,
                'rating'  => $review->rating,
                'comment' => $review->comment,
                'user'    => [
                    'id'   => $review->user->id,
                    'name' => $review->user->first_name . ' ' . $review->user->last_name,
                ],
            ]),

            // Progress (dynamic)
            'progress' => $progress,
        ]);
    }

}
