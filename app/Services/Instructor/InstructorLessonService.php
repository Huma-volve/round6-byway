<?php

namespace App\Services\Instructor;

use App\Models\Course;
use App\Models\Lesson;
use App\Models\User;
use App\Services\MediaUploadService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

class InstructorLessonService
{
    public function __construct(
        private MediaUploadService $mediaService
    ) {}

    /**
     * Create a new lesson for a course
     */
    public function create(Course $course, array $data): array
    {
        try {

            return DB::transaction(function () use ($course, $data) {

                // Determine lesson order
                $order = $data['order'] ?? ($course->lessons()->max('order') + 1);

                $uploadResult = $this->mediaService->uploadVideo($data['video'], $course->id, 0);

                // Create the lesson
                $lesson = new Lesson();
                $lesson->course_id = $course->id;
                $lesson->title = $data['title'];
                $lesson->order = $order;
                $lesson->duration_minutes = $data['duration_minutes'] ?? 0;

                // Assign uploaded video info
                $lesson->video_public_id = $uploadResult['public_id'];
                $lesson->video_url = $uploadResult['url'];
                $lesson->save();



                $this->updateCourseStats($course);

                return $this->show($lesson);
            });


        } catch (Exception $e) {
            Log::error('Failed to create lesson', [
                'course_id' => $course->id,
                'error' => $e->getMessage(),
                'data' => $data
            ]);
            throw $e;
        }
    }

    /**
     * Show lesson details
     */
    public function show(Lesson $lesson): array
    {
        return [
            'id' => $lesson->id,
            'course_id' => $lesson->course_id,
            'title' => $lesson->title,
            'order' => $lesson->order,
            'video_url' => $lesson->video_url,
            'video_public_id' => $lesson->video_public_id,
            'duration_minutes' => $lesson->duration_minutes,
            'created_at' => $lesson->created_at->format('Y-m-d H:i:s'),
        ];
    }

    /**
     * Update lesson
     */
    public function update(Lesson $lesson, array $data): array
    {
        try {
            return DB::transaction(function () use ($lesson, $data) {
                if (isset($data['title'])) $lesson->title = $data['title'];
                if (isset($data['order'])) $lesson->order = $data['order'];
                if (isset($data['duration_minutes'])) $lesson->duration_minutes = $data['duration_minutes'];

                if (!empty($data['video'])) {
                    $uploadResult = $this->mediaService->replaceVideo(
                        $data['video'],
                        $lesson->course_id,
                        $lesson->id,
                        $lesson->video_file_path
                    );
                    $lesson->video_public_id = $uploadResult['public_id'];
                    $lesson->video_file_path = $uploadResult['file_path'];
                    $lesson->video_url = $uploadResult['url'];

                    if (!isset($data['duration_minutes']) && !empty($uploadResult['duration'])) {
                        $lesson->duration_minutes = ceil($uploadResult['duration'] / 60);
                    }
                }

                $lesson->save();
                $this->updateCourseStats($lesson->course);

                return $this->show($lesson->fresh());
            });
        } catch (Exception $e) {
            Log::error('Failed to update lesson', [
                'lesson_id' => $lesson->id,
                'error' => $e->getMessage(),
                'data' => $data
            ]);
            throw $e;
        }
    }

    /**
     * Delete lesson
     */
    public function destroy(Lesson $lesson): void
    {
        try {
            DB::transaction(function () use ($lesson) {
                $course = $lesson->course;

                if ($lesson->video_file_path) {
                    $this->mediaService->deleteAsset($lesson->video_file_path);
                }

                $lesson->delete();
                $this->updateCourseStats($course);
            });

            Log::info('Lesson deleted', [
                'lesson_id' => $lesson->id,
                'course_id' => $lesson->course_id
            ]);
        } catch (Exception $e) {
            Log::error('Failed to delete lesson', [
                'lesson_id' => $lesson->id,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Reorder lessons
     */
    public function reorder(Course $course, array $lessonsData): array
    {
            try {
        return DB::transaction(function () use ($course, $lessonsData) {
            $updatedLessons = [];



            foreach ($lessonsData as $lessonData) {


                // Validate lesson exists and belongs to course
                $lesson = Lesson::where('id', $lessonData['id'])
                    ->where('course_id', $course->id)
                    ->first();



                if (!$lesson) {
                    throw new \Exception("Lesson ID {$lessonData['id']} not found in this course.");
                }

                // Update lesson order
                $lesson->update([
                    'order' => $lessonData['order']
                ]);

                $updatedLessons[] = [
                    'id' => $lesson->id,
                    'title' => $lesson->title,
                    'order' => $lesson->order,
                    'duration_minutes' => $lesson->duration_minutes,
                    'video_url' => $lesson->video_url,
                ];
            }

            Log::info('Lessons reordered', [
                'course_id' => $course->id,
                'lessons_count' => count($lessonsData)
            ]);

            return $updatedLessons;
        });
    } catch (\Exception $e) {
        Log::error('Failed to reorder lessons', [
            'course_id' => $course->id,
            'error' => $e->getMessage(),
            'payload' => $lessonsData
        ]);
        throw $e;
    }
        // try {
        //     return DB::transaction(function () use ($course, $lessonsData) {
        //         foreach ($lessonsData as $lessonData) {
        //             Lesson::where('id', $lessonData['id'])
        //                 ->where('course_id', $course->id)
        //                 ->update(['order' => $lessonData['order']]);
        //         }

        //         $lessons = $course->lessons()
        //             ->select(['id', 'title', 'order', 'duration_minutes', 'video_url'])
        //             ->orderBy('order')
        //             ->get()
        //             ->map(fn($lesson) => [
        //                 'id' => $lesson->id,
        //                 'title' => $lesson->title,
        //                 'order' => $lesson->order,
        //                 'duration_minutes' => $lesson->duration_minutes,
        //                 'video_url' => $lesson->video_url,
        //             ]);

        //         Log::info('Lessons reordered', [
        //             'course_id' => $course->id,
        //             'lessons_count' => count($lessonsData)
        //         ]);

        //         return $lessons->toArray();
        //     });
        // } catch (Exception $e) {
        //     Log::error('Failed to reorder lessons', [
        //         'course_id' => $course->id,
        //         'error' => $e->getMessage()
        //     ]);
        //     throw $e;
        // }
    }

    /**
     * Update course statistics (lesson count, total duration)
     */
    private function updateCourseStats(Course $course): void
    {
        try {
            $stats = $course->lessons()
                ->selectRaw('COUNT(*) as lessons_count, SUM(duration_minutes) as total_minutes')
                ->first();

            $course->update([
                'lessons_count' => $stats->lessons_count ?? 0,
                'total_minutes' => $stats->total_minutes ?? 0,
                'duration_hours' => ceil(($stats->total_minutes ?? 0) / 60),
            ]);
        } catch (Exception $e) {
            Log::warning('Failed to update course stats', [
                'course_id' => $course->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Ensure lesson belongs to course
     */
    public function ensureLessonBelongsToCourse(Lesson $lesson, Course $course): void
    {
        if ($lesson->course_id !== $course->id) {
            throw new Exception('Lesson does not belong to this course.');
        }
    }

    /**
     * Ensure instructor owns the course
     */
    public function ensureInstructorOwnership(Course $course, User $instructor): void
    {
        if ($course->instructor_id !== $instructor->id) {
            throw new Exception('You do not have permission to modify lessons for this course.');
        }
    }

    /**
     * Ensure course is editable
     */
    public function ensureCourseEditable(Course $course): void
    {
        if ($course->status === 'published') {
            throw new Exception('Cannot modify lessons in published course. Please contact admin to unpublish first.');
        }
    }
}
