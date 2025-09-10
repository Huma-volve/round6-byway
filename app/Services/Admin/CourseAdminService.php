<?php

namespace App\Services\Admin;

use App\Models\Course;
use App\Models\Category;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class CourseAdminService
{
    public function list(array $params): array
    {
        $query = Course::query()
            ->select(['id', 'title', 'instructor_id', 'category_id', 'status', 'price', 'compare_price', 'level', 'lessons_count', 'created_at'])
            ->with(['instructor:id,first_name,last_name', 'category:id,name']);

        if (!empty($params['q'])) {
            $q = trim($params['q']);
            $query->where(function ($qBuilder) use ($q) {
                $qBuilder->where('title', 'like', "%{$q}%")
                    ->orWhere('description', 'like', "%{$q}%");
            });
        }

        if (!empty($params['category_id'])) {
            $query->where('category_id', $params['category_id']);
        }

        if (!empty($params['status'])) {
            $query->where('status', $params['status']);
        }

        if (!empty($params['instructor_id'])) {
            $query->where('instructor_id', $params['instructor_id']);
        }

        $sort = $params['sort'] ?? 'reg_date_desc';
        match ($sort) {
            'reg_date_asc' => $query->orderBy('created_at', 'asc'),
            'title_asc' => $query->orderBy('title', 'asc'),
            'title_desc' => $query->orderBy('title', 'desc'),
            'price_asc' => $query->orderBy('price', 'asc'),
            'price_desc' => $query->orderBy('price', 'desc'),
            default => $query->orderBy('created_at', 'desc'),
        };

        $perPage = max(1, min(100, intval($params['per_page'] ?? 15)));
        $paginator = $query->paginate($perPage);

        $data = collect($paginator->items())->map(function ($course) {
            return [
                'id' => $course->id,
                'title' => $course->title,
                'instructor' => $course->instructor ? $course->instructor->first_name . ' ' . $course->instructor->last_name : null,
                'category' => $course->category ? $course->category->name : null,
                'status' => $course->status,
                'price' => $course->price,
                'compare_price' => $course->compare_price,
                'level' => $course->level,
                'lessons_count' => $course->lessons_count,
                'created_at' => $course->created_at->format('Y-m-d'),
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

    public function show(Course $course): array
    {
        $course->load([
            'instructor:id,first_name,last_name,email',
            'category:id,name,image',
            'lessons:id,course_id,title,order,duration_minutes',
            'reviews:id,course_id,user_id,rating,comment,created_at'
        ])->loadCount(['enrollments', 'reviews']);

        return [
            'id' => $course->id,
            'title' => $course->title,
            'description' => $course->description,
            'price' => $course->price,
            'compare_price' => $course->compare_price,
            'image' => $course->image,
            'level' => $course->level,
            'status' => $course->status,
            'duration_hours' => $course->duration_hours,
            'total_minutes' => $course->total_minutes,
            'lessons_count' => $course->lessons_count,
            'created_at' => $course->created_at->format('Y-m-d H:i:s'),
            'instructor' => $course->instructor ? [
                'id' => $course->instructor->id,
                'name' => $course->instructor->first_name . ' ' . $course->instructor->last_name,
                'email' => $course->instructor->email,
            ] : null,
            'category' => $course->category ? [
                'id' => $course->category->id,
                'name' => $course->category->name,
                'image' => $course->category->image,
            ] : null,
            'lessons' => $course->lessons->map(function ($lesson) {
                return [
                    'id' => $lesson->id,
                    'title' => $lesson->title,
                    'order' => $lesson->order,
                    'duration_minutes' => $lesson->duration_minutes,
                ];
            })->sortBy('order')->values(),
            'reviews' => $course->reviews->map(function ($review) {
                return [
                    'id' => $review->id,
                    'user_name' => $review->user ? $review->user->first_name . ' ' . $review->user->last_name : 'Anonymous',
                    'rating' => $review->rating,
                    'comment' => $review->comment,
                    'created_at' => $review->created_at->format('Y-m-d'),
                ];
            })->take(5)->values(),
            'counts' => [
                'enrollments' => $course->enrollments_count,
                'reviews' => $course->reviews_count,
            ],
        ];
    }

    public function update(Course $course, array $data): array
    {
        $course->fill($data);
        $course->save();
        return $this->show($course->fresh());
    }

    public function setStatus(Course $course, string $status): void
    {
        $course->status = $status;
        $course->save();
    }

    public function approve(Course $course): void
    {
        $this->setStatus($course, 'published');
    }

    public function archive(Course $course): void
    {
        $this->setStatus($course, 'archived');
    }

    public function destroy(Course $course, array $data): void
    {
        // Check if course has enrollments
        if ($course->enrollments()->count() > 0) {
            abort(422, 'Cannot delete course with existing enrollments. Consider archiving instead.');
        }

        $course->delete();
    }
}
