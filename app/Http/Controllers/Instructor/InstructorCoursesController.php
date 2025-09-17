<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\Concerns\RespondsWithJson;
use App\Http\Requests\Instructor\Course\CreateCourseRequest;
use App\Http\Requests\Instructor\Course\UpdateCourseRequest;
use App\Models\Course;
use App\Services\Instructor\InstructorCourseService;
use Exception;

class InstructorCoursesController extends Controller
{
        use RespondsWithJson;

    public function __construct(
        private InstructorCourseService $courseService
    ) {}

    /**
     * Create a new course
     */
    public function store(CreateCourseRequest $request)
    {
        try {
            $instructor = auth()->user();
            $course = $this->courseService->create($instructor, $request->validated());

            return $this->success($course, 'Course created successfully', 201);
        } catch (Exception $e) {
            return $this->error('Failed to create course: ' . $e->getMessage(), 500);
        }
    }

    /**
     * List instructor's courses
     */
    public function index(Request $request)
    {
        try {
            $instructor = auth()->user();

            // Validate query parameters
            $params = $request->validate([
                'status' => 'sometimes|in:draft,published,archived',
                'category_id' => 'sometimes|integer|exists:categories,id',
                'q' => 'sometimes|string|max:255',
                'sort' => 'sometimes|in:created_asc,created_desc,title_asc,title_desc,price_asc,price_desc',
                'per_page' => 'sometimes|integer|min:1|max:100',
            ]);

            $result = $this->courseService->list($instructor, $params);

            return $this->success($result['data'], 'Courses fetched successfully', 200, $result['meta']);
        } catch (Exception $e) {
            return $this->error('Failed to fetch courses: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Show course details
     */
    public function show(Course $course)
    {
        try {
            $instructor = auth()->user();

            // Check ownership
            $this->courseService->ensureOwnership($course, $instructor);

            $courseData = $this->courseService->show($course);

            return $this->success($courseData, 'Course fetched successfully');
        } catch (Exception $e) {
            $statusCode = str_contains($e->getMessage(), 'permission') ? 403 : 500;
            return $this->error($e->getMessage(), $statusCode);
        }
    }

    /**
     * Update course
     */
    public function update(UpdateCourseRequest $request, Course $course)
    {
        try {
            $instructor = auth()->user();

            // Authorization is handled in UpdateCourseRequest
            $courseData = $this->courseService->update($course, $request->validated());

            return $this->success($courseData, 'Course updated successfully');
        } catch (Exception $e) {
            $statusCode = str_contains($e->getMessage(), 'permission') ||
                         str_contains($e->getMessage(), 'published') ? 403 : 500;
            return $this->error($e->getMessage(), $statusCode);
        }
    }

    /**
     * Archive course
     */
    public function archive(Course $course)
    {
        try {
            $instructor = auth()->user();

            // Check ownership
            $this->courseService->ensureOwnership($course, $instructor);

            // Check if can be archived
            if ($course->status === 'published') {
                return $this->error('Cannot archive published course. Please contact admin to unpublish first.', 403);
            }

            if ($course->status === 'archived') {
                return $this->error('Course is already archived.', 422);
            }

            $this->courseService->archive($course);

            return $this->success(['status' => 'archived'], 'Course archived successfully');
        } catch (Exception $e) {
            $statusCode = str_contains($e->getMessage(), 'permission') ? 403 : 500;
            return $this->error($e->getMessage(), $statusCode);
        }
    }

    /**
     * Delete course
     */
    public function destroy(Course $course)
    {
        try {
            $instructor = auth()->user();

            // Check ownership
            $this->courseService->ensureOwnership($course, $instructor);

            // Check if can be deleted
            if ($course->status === 'published') {
                return $this->error('Cannot delete published course. Please contact admin to unpublish first.', 403);
            }

            $this->courseService->destroy($course);

            return $this->success(null, 'Course deleted successfully');
        } catch (Exception $e) {
            if (str_contains($e->getMessage(), 'enrollments')) {
                return $this->error($e->getMessage(), 422);
            }

            $statusCode = str_contains($e->getMessage(), 'permission') ? 403 : 500;
            return $this->error($e->getMessage(), $statusCode);
        }
    }
public function details(Course $course)
{
    try {
        $instructor = auth()->user();

        $this->courseService->ensureOwnership($course, $instructor);
        $courseDetails = $this->courseService->getCourseDetails($course);

        return $this->success($courseDetails, 'Course details fetched successfully');
    } catch (Exception $e) {
        $statusCode = str_contains($e->getMessage(), 'permission') ? 403 : 500;
        return $this->error($e->getMessage(), $statusCode);
    }
}

public function reviews(Request $request)
{
    try {
        $instructor = auth()->user();

        $params = $request->validate([
            'per_page' => 'sometimes|integer|min:1|max:50',
        ]);

        $result = $this->courseService->getInstructorReviews($instructor, $params);

        return $this->success($result['data'], 'Reviews fetched successfully', 200, $result['meta']);
    } catch (Exception $e) {
        return $this->error('Failed to fetch reviews: ' . $e->getMessage(), 500);
    }
}





}
