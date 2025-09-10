<?php

namespace App\Services\Admin;

use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class UserAdminService
{
    public function list(array $params): array
    {
        $query = User::query()->select(['id', 'first_name', 'last_name', 'email', 'username', 'role', 'status']);

        if (!empty($params['q'])) {
            $q = trim($params['q']);
            $query->where(function ($qBuilder) use ($q) {
                $qBuilder->where(DB::raw("CONCAT(first_name, ' ', last_name)"), 'like', "%{$q}%")
                    ->orWhere('username', 'like', "%{$q}%")
                    ->orWhere('email', 'like', "%{$q}%");
            });
        }

        if (!empty($params['role'])) {
            $query->where('role', $params['role']);
        }

        if (!empty($params['status'])) {
            $query->where('status', $params['status']);
        }

        $sort = $params['sort'] ?? 'reg_date_desc';
        match ($sort) {
            'reg_date_asc' => $query->orderBy('created_at', 'asc'),
            'name_asc' => $query->orderBy('last_name')->orderBy('first_name'),
            'name_desc' => $query->orderByDesc('last_name')->orderByDesc('first_name'),
            default => $query->orderBy('created_at', 'desc'),
        };

        $perPage = intval($params['per_page'] ?? 15);
        $perPage = max(1, min(100, $perPage));

        /** @var LengthAwarePaginator $paginator */
        $paginator = $query->paginate($perPage);

        $data = collect($paginator->items())
            ->map(function ($user) {
                return [
                    'id' => $user->id,
                    'first_name' => $user->first_name,
                    'last_name' => $user->last_name,
                    'email' => $user->email,
                    'username' => $user->username,
                    'role' => $user->role,
                    'status' => $user->status,
                ];
            })
            ->all();

        return [
            'data' => $data,
            'meta' => [
                'current_page' => $paginator->currentPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
            ],
        ];
    }

    public function show(User $user): array
    {
        $user->load(['studentProfile:id,user_id,headline,image', 'instructorProfile:id,user_id,headline,image'])
            ->loadCount(['courses', 'enrollments', 'reviews']);

        return [
            'id' => $user->id,
            'first_name' => $user->first_name,
            'last_name' => $user->last_name,
            'email' => $user->email,
            'username' => $user->username,
            'role' => $user->role,
            'status' => $user->status,
            'profiles' => [
                'student' => $user->studentProfile ? [
                    'headline' => $user->studentProfile->headline,
                    'image' => $user->studentProfile->image ?? null,
                ] : null,
                'instructor' => $user->instructorProfile ? [
                    'headline' => $user->instructorProfile->headline,
                    'image' => $user->instructorProfile->image ?? null,
                ] : null,
            ],
            'counts' => [
                'courses' => $user->courses_count,
                'enrollments' => $user->enrollments_count,
                'reviews' => $user->reviews_count,
            ],
        ];
    }

    public function update(User $user, array $data): array
    {
        $user->fill($data);
        $user->save();
        return $user->fresh()->toArray();
    }

    public function setStatus(User $user, string $status): void
    {
        // Guard: do not let admin change their own status via this endpoint (policy later)
        // if (auth()->id() === $user->id) {
        //     abort(403, 'You cannot change your own status.');
        // }
        $user->status = $status;
        $user->save();
        if ($status === 'blocked') {
            DB::table('personal_access_tokens')->where('tokenable_id', $user->id)->delete();
            DB::table('sessions')->where('user_id', $user->id)->delete();
        }
    }

    public function disable(User $user): void
    {
        // If using soft deletes in future: $user->delete();
        $this->setStatus($user, 'blocked');
    }

    public function destroy(User $user, array $data): void
    {
        // if (auth()->id() === $user->id) {
        //     abort(403, 'You cannot delete your own account from admin interface.');
        // }
        // Optionally verify email confirmation match before hard delete
        $user->delete();
    }
}
