<?php

namespace App\Http\Controllers\Instructor;

use Illuminate\Http\Request;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Concerns\RespondsWithJson;
use App\Http\Requests\Instructor\Lesson\CreateLessonRequest;
use App\Http\Requests\Instructor\Lesson\UpdateLessonRequest;
use App\Http\Requests\Instructor\Lesson\ReorderLessonsRequest;
use App\Models\Course;
use App\Models\Lesson;
use App\Services\Instructor\InstructorLessonService;
use Exception;

class InstructorLessonsController extends Controller
{
        use RespondsWithJson;

    public function __construct(
        private InstructorLessonService $lessonService
    ) {}

    /**
     * Create a new lesson for course
     */
    public function store(CreateLessonRequest $request, Course $course)
    {
        try {
            // Authorization is handled in CreateLessonRequest
            $lesson = $this->lessonService->create($course, $request->validated());

            return $this->success($lesson, 'Lesson created successfully', 201);
        } catch (Exception $e) {
            return $this->error('Failed to create lesson: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Update lesson
     */
    public function update(UpdateLessonRequest $request, Course $course, Lesson $lesson)
    {
        try {
            // Authorization is handled in UpdateLessonRequest

            // Additional check to ensure lesson belongs to course
            $this->lessonService->ensureLessonBelongsToCourse($lesson, $course);

            $lessonData = $this->lessonService->update($lesson, $request->validated());

            return $this->success($lessonData, 'Lesson updated successfully');
        } catch (Exception $e) {
            $statusCode = str_contains($e->getMessage(), 'permission') ||
                         str_contains($e->getMessage(), 'published') ||
                         str_contains($e->getMessage(), 'belong') ? 403 : 500;
            return $this->error($e->getMessage(), $statusCode);
        }
    }

    /**
     * Delete lesson
     */
    public function destroy(Course $course, Lesson $lesson)
    {
        try {
            $instructor = auth()->user();

            // Check ownership and permissions
            $this->lessonService->ensureInstructorOwnership($course, $instructor);
            $this->lessonService->ensureCourseEditable($course);
            $this->lessonService->ensureLessonBelongsToCourse($lesson, $course);

            $this->lessonService->destroy($lesson);

            return $this->success(null, 'Lesson deleted successfully');
        } catch (Exception $e) {
            $statusCode = str_contains($e->getMessage(), 'permission') ||
                         str_contains($e->getMessage(), 'published') ||
                         str_contains($e->getMessage(), 'belong') ? 403 : 500;
            return $this->error($e->getMessage(), $statusCode);
        }
    }

    /**
     * Reorder lessons
     */
    public function reorder(ReorderLessonsRequest $request, Course $course)
    {
    dd($request->all(), $course->id); // Debug: shows incoming data and course ID


        // try {
        //     // Authorization is handled in ReorderLessonsRequest

        //     $lessons = $this->lessonService->reorder($course, $request->validated('lessons'));

        //     return $this->success($lessons, 'Lessons reordered successfully');
        // } catch (Exception $e) {
        //     $statusCode = str_contains($e->getMessage(), 'permission') ||
        //                  str_contains($e->getMessage(), 'published') ? 403 : 500;
        //     return $this->error($e->getMessage(), $statusCode);
        // }
    }
}






