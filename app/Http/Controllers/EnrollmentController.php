<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class EnrollmentController extends Controller
{
    public function index(Request $request){
        $user = $request->user();
        if ($user->role !== 'student') {
            return response()->json([
                'status' => 'failed',
                'message' => 'Only students can access enrolled courses.'
            ], 403);
        }

        
        $enrollments = $user->enrollments()
            ->with(['course.instructor:id,first_name,last_name'])
            ->paginate(5); 

        
        $courses = $enrollments->map(function ($enrollment) {
            $course = $enrollment->course;
            $instructor = $course?->instructor;

            return [
                'course_id'   => $course?->id ?? null,
                'course_name' => $course?->title ?? 'Deleted course',
                'instructor'  => $instructor
                    ? $instructor->first_name . ' ' . $instructor->last_name
                    : 'Unknown',
                'progress'    => $enrollment->progress_percentage . '%',
                'enrolled_at' => $enrollment->enrolled_at,
            ];
        });

        
        return response()->json([
            'status'  => 'success',
            'courses' => $courses,
            'meta'    => [
                'current_page' => $enrollments->currentPage(),
                'last_page'    => $enrollments->lastPage(),
                'per_page'     => $enrollments->perPage(),
                'total'        => $enrollments->total(),
            ]
        ]);

    }
}
