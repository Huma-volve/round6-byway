<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Concerns\RespondsWithJson;
use App\Http\Requests\Admin\Instructor\CreateInstructorRequest;
use App\Http\Requests\Admin\Instructor\IndexInstructorsRequest;
use App\Http\Requests\Admin\Instructor\UpdateInstructorRequest;
use App\Http\Requests\Admin\Instructor\UpdateInstructorProfileRequest;
use App\Http\Requests\Admin\Instructor\SetInstructorStatusRequest;
use App\Http\Requests\Admin\Instructor\DisableInstructorRequest;
use App\Http\Requests\Admin\Instructor\DestroyInstructorRequest;
use App\Models\User;
use App\Services\Admin\InstructorAdminService;

class InstructorsController extends Controller
{
    use RespondsWithJson;

    public function __construct(private InstructorAdminService $service) {}

    public function store(CreateInstructorRequest $request)
    {
        $created = $this->service->create($request->validated());
        return $this->success($created, 'Instructor created', 201);
    }

    public function index(IndexInstructorsRequest $request)
    {
        $result = $this->service->list($request->validated());
        return $this->success($result['data'], 'Instructors fetched', 200, $result['meta']);
    }

    public function show(User $instructor)
    {
        $result = $this->service->show($instructor);
        return $this->success($result, 'Instructor fetched');
    }

    public function update(UpdateInstructorRequest $request, User $instructor)
    {
        $updated = $this->service->update($instructor, $request->validated());
        return $this->success($updated, 'Instructor updated');
    }

    public function updateProfile(UpdateInstructorProfileRequest $request, User $instructor)
    {
        $profile = $this->service->updateProfile($instructor, $request->validated());
        return $this->success($profile, 'Instructor profile updated');
    }

    public function setStatus(SetInstructorStatusRequest $request, User $instructor)
    {
        $status = $request->validated('status');
        $this->service->setStatus($instructor, $status);
        return $this->success(['status' => $status], 'Status updated');
    }

    public function disable(DisableInstructorRequest $request, User $instructor)
    {
        $this->service->disable($instructor);
        return $this->success(null, 'Instructor disabled');
    }

    public function destroy(DestroyInstructorRequest $request, User $instructor)
    {
        $this->service->destroy($instructor, $request->validated());
        return response()->json([
            'success' => true,
            'message' => 'Instructor deleted successfully'
        ], 200);
    }
}
