<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Concerns\RespondsWithJson;
use App\Http\Requests\Admin\Course\IndexCoursesRequest;
use App\Http\Requests\Admin\Course\UpdateCourseRequest;
use App\Http\Requests\Admin\Course\SetCourseStatusRequest;
use App\Http\Requests\Admin\Course\ApproveCourseRequest;
use App\Http\Requests\Admin\Course\ArchiveCourseRequest;
use App\Http\Requests\Admin\Course\DestroyCourseRequest;
use App\Models\Course;
use App\Services\Admin\CourseAdminService;

class CoursesController extends Controller
{
    use RespondsWithJson;

    public function __construct(private CourseAdminService $service) {}

    public function index(IndexCoursesRequest $request)
    {
        $result = $this->service->list($request->validated());
        return $this->success($result['data'], 'Courses fetched', 200, $result['meta']);
    }

    public function show(Course $course)
    {
        $result = $this->service->show($course);
        return $this->success($result, 'Course fetched');
    }

    public function update(UpdateCourseRequest $request, Course $course)
    {
        $updated = $this->service->update($course, $request->validated());
        return $this->success($updated, 'Course updated');
    }

    public function setStatus(SetCourseStatusRequest $request, Course $course)
    {
        $status = $request->validated('status');

        $this->service->setStatus($course, $status);
        return $this->success(['status' => $status], 'Status updated');
    }

    public function approve(ApproveCourseRequest $request, Course $course)
    {
        $this->service->approve($course);
        return $this->success(null, 'Course approved');
    }

    public function archive(ArchiveCourseRequest $request, Course $course)
    {
        $this->service->archive($course);
        return $this->success(null, 'Course archived');
    }

    public function destroy(DestroyCourseRequest $request, Course $course)
    {
        $this->service->destroy($course, $request->validated());
        return $this->success(null, 'Course deleted', 204);
    }
}
