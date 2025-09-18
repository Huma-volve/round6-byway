<?php

namespace App\Services\Instructor;

use App\Models\Course;
use App\Models\User;
use App\Services\MediaUploadService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

class InstructorCourseService
{
    public function __construct(
        private MediaUploadService $mediaService
    ) {}

    /**
     * Create a new course for instructor
     */
    public function create(User $instructor, array $data): array
    {
        try {
            return DB::transaction(function () use ($instructor, $data) {
                $course = new Course();
                $course->title = $data['title'];
                $course->description = $data['description'];
                $course->category_id = $data['category_id'];
                $course->instructor_id = $instructor->id;
                $course->price = $data['price'];
                $course->compare_price = $data['compare_price'] ?? null;
                $course->level = $data['level'] ?? 'beginner';
                $course->status = 'draft';
                $course->save();

                if (!empty($data['cover_image'])) {
                    $uploadResult = $this->mediaService->uploadCover($data['cover_image'], $course->id);
                    $course->cover_public_id = $uploadResult['public_id'];
                    $course->image = $uploadResult['file_path'];
                    $course->image = $uploadResult['url'];
                    $course->save();
                }

                return $this->show($course);
            });
        } catch (Exception $e) {
            Log::error('Failed to create course', [
                'instructor_id' => $instructor->id,
                'error' => $e->getMessage(),
                'data' => $data,
            ]);
            throw $e;
        }
    }

    /**
     * List instructor's courses with filters
     */
    public function list(User $instructor, array $params): array
    {
        try {
            $query = Course::query()
                ->select([
                    'id', 'title', 'category_id', 'status', 'price',
                    'compare_price', 'level', 'lessons_count', 'image', 'created_at'
                ])
                ->where('instructor_id', $instructor->id)
                ->with(['category:id,name']);

            if (!empty($params['status'])) {
                $query->where('status', $params['status']);
            }

            if (!empty($params['category_id'])) {
                $query->where('category_id', $params['category_id']);
            }

            if (!empty($params['q'])) {
                $q = trim($params['q']);
                $query->where(function ($qBuilder) use ($q) {
                    $qBuilder->where('title', 'like', "%{$q}%")
                    ->orWhere('description', 'like', "%{$q}%");
                });
            }

            $sort = $params['sort'] ?? 'created_desc';
            match ($sort) {
                'created_asc' => $query->orderBy('created_at', 'asc'),
                'title_asc' => $query->orderBy('title', 'asc'),
                'title_desc' => $query->orderBy('title', 'desc'),
                'price_asc' => $query->orderBy('price', 'asc'),
                'price_desc' => $query->orderBy('price', 'desc'),
                default => $query->orderBy('created_at', 'desc'),
            };

            $perPage = max(1, min(100, intval($params['per_page'] ?? 15)));
            $paginator = $query->paginate($perPage);

            $data = collect($paginator->items())->map(fn($course) => [
                'id' => $course->id,
                'title' => $course->title,
                'category' => $course->category?->name,
                'status' => $course->status,
                'price' => $course->price,
                'compare_price' => $course->compare_price,
                'level' => $course->level,
                'lessons_count' => $course->lessons_count,
                'image' => $course->image,
                'created_at' => $course->created_at->format('Y-m-d'),
            ])->all();

            return [
                'data' => $data,
                'meta' => [
                    'current_page' => $paginator->currentPage(),
                    'per_page' => $paginator->perPage(),
                    'total' => $paginator->total(),
                ],
            ];
        } catch (Exception $e) {
            Log::error('Failed to list instructor courses', [
                'instructor_id' => $instructor->id,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Show course details
     */
    public function show(Course $course): array
    {
        try {
            $course->load([
                'category:id,name,image',
                'lessons:id,course_id,title,order,duration_minutes,video_url'
            ])->loadCount(['enrollments', 'reviews']);

            return [
                'id' => $course->id,
                'title' => $course->title,
                'description' => $course->description,
                'price' => $course->price,
                'compare_price' => $course->compare_price,
                'image' => $course->image,
                'cover_public_id' => $course->cover_public_id,
                'level' => $course->level,
                'status' => $course->status,
                'duration_hours' => $course->duration_hours,
                'total_minutes' => $course->total_minutes,
                'lessons_count' => $course->lessons_count,
                'created_at' => $course->created_at->format('Y-m-d H:i:s'),
                'category' => $course->category ? [
                    'id' => $course->category->id,
                    'name' => $course->category->name,
                    'image' => $course->category->image,
                ] : null,
                'lessons' => $course->lessons
                    ->sortBy('order')
                    ->values()
                    ->map(fn($lesson) => [
                        'id' => $lesson->id,
                        'title' => $lesson->title,
                        'order' => $lesson->order,
                        'duration_minutes' => $lesson->duration_minutes,
                        'video_url' => $lesson->video_url,
                    ]),
                'counts' => [
                    'enrollments' => $course->enrollments_count,
                    'reviews' => $course->reviews_count,
                ],
            ];
        } catch (Exception $e) {
            Log::error('Failed to show course', [
                'course_id' => $course->id,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    public function update(Course $course, array $data): array
{
    try {
        return DB::transaction(function () use ($course, $data) {
            foreach (['title', 'description', 'category_id', 'price', 'compare_price', 'level'] as $field) {
                if (isset($data[$field])) {
                    $course->$field = $data[$field];
                }
            }

            if (!empty($data['cover_image'])) {
                $uploadResult = $this->mediaService->replaceCover(
                    $data['cover_image'],
                    $course->id,
                    $course->image // Use `image` as the old path
                );
                $course->cover_public_id = $uploadResult['public_id'];
                $course->image = $uploadResult['url'];
            }

            $course->save();


            Log::info('Updating course', [
    'course_id' => $course->id,
    'data' => $data,
    'course_before_save' => $course->getChanges()
]);

            return $this->show($course->fresh());
        });
    } catch (Exception $e) {
        Log::error('Failed to update course', [
            'course_id' => $course->id,
            'error' => $e->getMessage(),
            'data' => $data,
        ]);
        throw $e;
    }
}

    /**
     * Archive course
     */
    public function archive(Course $course): void
    {
        try {
            $course->status = 'archived';
            $course->save();

            Log::info('Course archived', [
                'course_id' => $course->id,
                'instructor_id' => $course->instructor_id,
            ]);
        } catch (Exception $e) {
            Log::error('Failed to archive course', [
                'course_id' => $course->id,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Delete course
     */
    public function destroy(Course $course): void
    {
        try {
            if ($course->enrollments()->count() > 0) {
                throw new Exception('Cannot delete course with existing enrollments. Consider archiving instead.');
            }

            DB::transaction(function () use ($course) {
                $lessonFilePaths = $course->lessons()
                    ->whereNotNull('video_url')
                    ->pluck('video_url')
                    ->toArray();

                $this->mediaService->cleanupCourseMedia(
                    $course->id,
                    $course->cover_public_id,
                    $lessonFilePaths
                );

                $course->delete();
            });

            Log::info('Course deleted', [
                'course_id' => $course->id,
                'instructor_id' => $course->instructor_id,
            ]);
        } catch (Exception $e) {
            Log::error('Failed to delete course', [
                'course_id' => $course->id,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Check if instructor owns the course
     */
    public function ensureOwnership(Course $course, User $instructor): void
    {
        if ($course->instructor_id !== $instructor->id) {
            throw new Exception('You do not have permission to access this course.');
        }
    }

    /**
     * Check if course can be edited
     */
    public function ensureEditable(Course $course): void
    {
        if ($course->status === 'published') {
            throw new Exception('Cannot edit published course. Please contact admin to unpublish first.');
        }
    }
}
