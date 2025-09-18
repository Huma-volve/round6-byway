<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Exception;

class MediaUploadService
{
    /**
     * Upload a course cover image to Cloudinary
     *
     * @param UploadedFile $file
     * @param int $courseId
     * @return array ['public_id' => string, 'url' => string, 'file_path' => string]
     * @throws Exception
     */
    public function uploadCover(UploadedFile $file, int $courseId): array
    {
        try {
            $folder = $this->getCoverFolder($courseId);

            // Upload using Storage disk like your example
            $path = Storage::disk('cloudinary')->put($folder, $file);
            $url = Storage::disk('cloudinary')->url($path);

            // Extract public_id from path (remove file extension for Cloudinary public_id)
            $publicId = pathinfo($path, PATHINFO_DIRNAME) . '/' . pathinfo($path, PATHINFO_FILENAME);

            return [
                'public_id' => $publicId,
                'url' => $url,
                'file_path' => $path
            ];
        } catch (Exception $e) {
            Log::error('Failed to upload course cover', [
                'course_id' => $courseId,
                'error' => $e->getMessage()
            ]);
            throw new Exception('Failed to upload course cover: ' . $e->getMessage());
        }
    }

    /**
     * Upload a lesson video to Cloudinary
     *
     * @param UploadedFile $file
     * @param int $courseId
     * @param int $lessonId
     * @return array ['public_id' => string, 'url' => string, 'file_path' => string, 'duration' => int|null]
     * @throws Exception
     */
    public function uploadVideo(UploadedFile $file, int $courseId, int $lessonId): array
    {
        try {
            $folder = $this->getVideoFolder($courseId, $lessonId);

            // Upload using Storage disk like your example
            $path = Storage::disk('cloudinary')->put($folder, $file);
            $url = Storage::disk('cloudinary')->url($path);

            // Extract public_id from path (remove file extension for Cloudinary public_id)
            $publicId = pathinfo($path, PATHINFO_DIRNAME) . '/' . pathinfo($path, PATHINFO_FILENAME);

            return [
                'public_id' => $publicId,
                'url' => $url,
                'file_path' => $path,
                'duration' => null // Duration will need to be extracted separately if needed
            ];
        } catch (Exception $e) {
            Log::error('Failed to upload lesson video', [
                'course_id' => $courseId,
                'lesson_id' => $lessonId,
                'error' => $e->getMessage()
            ]);
            throw new Exception('Failed to upload lesson video: ' . $e->getMessage());
        }
    }

    /**
     * Delete an asset from Cloudinary
     *
     * @param string $filePath The full file path stored in database
     * @return bool
     */
    public function deleteAsset(string $filePath): bool
    {
        try {
            $deleted = Storage::disk('cloudinary')->delete($filePath);
            return $deleted;
        } catch (Exception $e) {
            Log::warning('Failed to delete Cloudinary asset', [
                'file_path' => $filePath,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Replace an existing cover image
     *
     * @param UploadedFile $file
     * @param int $courseId
     * @param string|null $oldFilePath
     * @return array
     * @throws Exception
     */
    public function replaceCover(UploadedFile $file, int $courseId, ?string $oldFilePath = null): array
    {
        // Upload new cover first
        $uploadResult = $this->uploadCover($file, $courseId);

        // Delete old cover if it exists
        if ($oldFilePath) {
            $this->deleteAsset($oldFilePath);
        }

        return $uploadResult;
    }

    /**
     * Replace an existing lesson video
     *
     * @param UploadedFile $file
     * @param int $courseId
     * @param int $lessonId
     * @param string|null $oldFilePath
     * @return array
     * @throws Exception
     */
    public function replaceVideo(UploadedFile $file, int $courseId, int $lessonId, ?string $oldFilePath = null): array
    {
        // Upload new video first
        $uploadResult = $this->uploadVideo($file, $courseId, $lessonId);

        // Delete old video if it exists
        if ($oldFilePath) {
            $this->deleteAsset($oldFilePath);
        }

        return $uploadResult;
    }

    /**
     * Get folder path for course covers
     *
     * @param int $courseId
     * @return string
     */
    private function getCoverFolder(int $courseId): string
    {
        return "courses/{$courseId}/cover";
    }

    /**
     * Get folder path for lesson videos
     *
     * @param int $courseId
     * @param int $lessonId
     * @return string
     */
    private function getVideoFolder(int $courseId, int $lessonId): string
    {
        return "courses/{$courseId}/lessons/{$lessonId}";
    }

    /**
     * Clean up all media for a course (when deleting course)
     *
     * @param int $courseId
     * @param string|null $coverFilePath
     * @param array $lessonFilePaths
     * @return void
     */
    public function cleanupCourseMedia(int $courseId, ?string $coverFilePath = null, array $lessonFilePaths = []): void
    {
        // Delete cover image
        if ($coverFilePath) {
            $this->deleteAsset($coverFilePath);
        }

        // Delete lesson videos
        foreach ($lessonFilePaths as $filePath) {
            if ($filePath) {
                $this->deleteAsset($filePath);
            }
        }

        Log::info('Cleaned up course media', [
            'course_id' => $courseId,
            'cover_deleted' => !is_null($coverFilePath),
            'videos_deleted' => count(array_filter($lessonFilePaths))
        ]);
    }

    /**
     * Validate file for course cover upload
     *
     * @param UploadedFile $file
     * @return bool
     */
    public function validateCoverFile(UploadedFile $file): bool
    {
        $allowedMimes = ['image/jpeg', 'image/png', 'image/webp'];
        $maxSize = 5 * 1024 * 1024; // 5MB

        return in_array($file->getMimeType(), $allowedMimes) && $file->getSize() <= $maxSize;
    }

    /**
     * Validate file for lesson video upload
     *
     * @param UploadedFile $file
     * @return bool
     */
    public function validateVideoFile(UploadedFile $file): bool
    {
        $allowedMimes = ['video/mp4', 'video/mpeg', 'video/quicktime', 'video/x-msvideo'];
        $maxSize = 200 * 1024 * 1024; // 200MB

        return in_array($file->getMimeType(), $allowedMimes) && $file->getSize() <= $maxSize;
    }
}
