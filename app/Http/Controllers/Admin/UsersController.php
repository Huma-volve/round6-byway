<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\User\IndexUsersRequest;
use App\Http\Requests\Admin\User\UpdateUserRequest;
use App\Http\Requests\Admin\User\SetUserStatusRequest;
use App\Http\Requests\Admin\User\DisableUserRequest;
use App\Http\Requests\Admin\User\DestroyUserRequest;
use App\Models\User;
use App\Services\Admin\UserAdminService;
use App\Http\Controllers\Concerns\RespondsWithJson;
use Illuminate\Http\JsonResponse;

class UsersController extends Controller
{
    use RespondsWithJson;
    public function __construct(private UserAdminService $service) {}

    public function index(IndexUsersRequest $request): JsonResponse
    {
        $result = $this->service->list($request->validated());
        return $this->success($result['data'], 'Users fetched', 200, $result['meta']);
    }

    public function show(User $user): JsonResponse
    {
        $result = $this->service->show($user);
        return $this->success($result, 'User fetched');
    }

    public function update(UpdateUserRequest $request, User $user): JsonResponse
    {
        $updated = $this->service->update($user, $request->validated());
        return $this->success($updated, 'User updated');
    }

    public function setStatus(SetUserStatusRequest $request, User $user): JsonResponse
    {
        $status = $request->validated('status');
        $this->service->setStatus($user, $status);
        return $this->success(['status' => $status], 'Status updated');
    }

    public function disable(DisableUserRequest $request, User $user): JsonResponse
    {
        $this->service->disable($user);
        return $this->success(null, 'User disabled');
    }

    public function destroy(DestroyUserRequest $request, User $user): JsonResponse
    {
        $this->service->destroy($user, $request->validated());
        return response()->json([
            'success' => true,
            'message' => 'User deleted successfully'
        ], 200);
    }
}
