<?php

namespace App\Services\Admin;

use App\Models\User;
use App\Models\InstructorProfile;
use Illuminate\Support\Facades\DB;

class InstructorAdminService
{
    protected function ensureInstructor(User $user): void
    {
        if ($user->role !== 'instructor') {
            abort(422, 'Target user is not an instructor.');
        }
    }

    public function create(array $data): array
    {
        return DB::transaction(function () use ($data) {
            $user = new User();
            $user->first_name = $data['first_name'];
            $user->last_name = $data['last_name'];
            $user->email = $data['email'];
            $user->username = $data['username'];
            $user->password = $data['password'];
            $user->role = 'instructor';
            $user->status = $data['status'] ?? 'active';
            $user->save();

            if (!empty($data['profile']) && is_array($data['profile'])) {
                $profile = new InstructorProfile();
                $profile->user_id = $user->id;
                $profile->headline = $data['profile']['headline'] ?? null;
                $profile->about = $data['profile']['about'] ?? null;
                $profile->skills = $data['profile']['skills'] ?? null;
                $profile->social_links = $data['profile']['social_links'] ?? null;
                $profile->image = $data['profile']['image'] ?? null;
                $profile->save();
            }

            return $this->show($user);
        });
    }

    public function list(array $params): array
    {
        $query = User::query()
            ->select(['id', 'first_name', 'last_name', 'email', 'username', 'status'])
            ->where('role', 'instructor');

        if (!empty($params['q'])) {
            $q = trim($params['q']);
            $query->where(function ($qBuilder) use ($q) {
                $qBuilder->where(DB::raw("CONCAT(first_name, ' ', last_name)"), 'like', "%{$q}%")
                    ->orWhere('username', 'like', "%{$q}%")
                    ->orWhere('email', 'like', "%{$q}%");
            });
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

        $perPage = max(1, min(100, intval($params['per_page'] ?? 15)));
        $paginator = $query->paginate($perPage);

        $data = collect($paginator->items())->map(function ($u) {
            return [
                'id' => $u->id,
                'first_name' => $u->first_name,
                'last_name' => $u->last_name,
                'email' => $u->email,
                'username' => $u->username,
                'status' => $u->status,
            ];
        })->all();

        return [
            'data' => $data,
            'meta' => [
                'current_page' => $paginator->currentPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
            ],
        ];
    }

    public function show(User $instructor): array
    {
        $this->ensureInstructor($instructor);
        $instructor->load(['instructorProfile:id,user_id,headline,about,skills,social_links,image'])
            ->loadCount(['courses', 'reviews']);

        return [
            'id' => $instructor->id,
            'first_name' => $instructor->first_name,
            'last_name' => $instructor->last_name,
            'email' => $instructor->email,
            'username' => $instructor->username,
            'status' => $instructor->status,
            'profile' => $instructor->instructorProfile ? [
                'headline' => $instructor->instructorProfile->headline,
                'about' => $instructor->instructorProfile->about,
                'skills' => $instructor->instructorProfile->skills,
                'social_links' => $instructor->instructorProfile->social_links,
                'image' => $instructor->instructorProfile->image,
            ] : null,
            'counts' => [
                'courses' => $instructor->courses_count,
                'reviews' => $instructor->reviews_count,
            ],
        ];
    }

    public function update(User $instructor, array $data): array
    {
        $this->ensureInstructor($instructor);
        if (isset($data['password'])) {
            $instructor->password = $data['password'];
            unset($data['password']);
        }
        $instructor->fill($data);
        $instructor->save();
        return $this->show($instructor->fresh());
    }

    public function updateProfile(User $instructor, array $data): array
    {
        $this->ensureInstructor($instructor);
        $profile = InstructorProfile::firstOrNew(['user_id' => $instructor->id]);
        $profile->fill($data);
        $profile->save();
        return $this->show($instructor->fresh());
    }

    public function setStatus(User $instructor, string $status): void
    {
        $this->ensureInstructor($instructor);
        // if (auth()->id() === $instructor->id) {
        //     abort(403, 'You cannot change your own status.');
        // }
        $instructor->status = $status;
        $instructor->save();
        if ($status === 'blocked') {
            DB::table('personal_access_tokens')->where('tokenable_id', $instructor->id)->delete();
            DB::table('sessions')->where('user_id', $instructor->id)->delete();
        }
    }

    public function disable(User $instructor): void
    {
        $this->setStatus($instructor, 'blocked');
    }

    public function destroy(User $instructor, array $data): void
    {
        $this->ensureInstructor($instructor);
        // if (auth()->id() === $instructor->id) {
        //     abort(403, 'You cannot delete your own account from admin interface.');
        // }
        $instructor->delete();
    }
}
