<?php

namespace App\Http\Requests\Instructor\Lesson;

use Illuminate\Foundation\Http\FormRequest;

class UpdateLessonRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $course = $this->route('course');
        $lesson = $this->route('lesson');

        return auth()->check() &&
               auth()->user()->role === 'instructor' &&
               $course->instructor_id === auth()->id() &&
               $lesson->course_id === $course->id &&
               $course->status !== 'published'; // Can't edit lessons in published courses
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => 'sometimes|string|min:3|max:255',
            'order' => 'sometimes|integer|min:1',
            'video' => 'nullable|file|mimes:mp4,mov,avi,wmv,flv,webm|max:204800', // 200MB max
            'duration_minutes' => 'nullable|integer|min:1|max:600', // Max 10 hours per lesson
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'title.min' => 'Lesson title must be at least 3 characters',
            'video.mimes' => 'Video must be in MP4, MOV, AVI, WMV, FLV, or WebM format',
            'video.max' => 'Video file size cannot exceed 200MB',
            'duration_minutes.min' => 'Duration must be at least 1 minute',
            'duration_minutes.max' => 'Duration cannot exceed 600 minutes (10 hours)',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'duration_minutes' => 'duration',
        ];
    }
}
