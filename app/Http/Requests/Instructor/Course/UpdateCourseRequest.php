<?php

namespace App\Http\Requests\Instructor\Course;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCourseRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $course = $this->route('course');
        return auth()->check() && auth()->user()->role === 'instructor' && $course->instructor_id === auth()->id() &&
               in_array($course->status, ['draft', 'archived']); // Can't edit published courses
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
            'description' => 'sometimes|string|min:20|max:5000',
            'category_id' => 'sometimes|exists:categories,id',
            'price' => 'sometimes|numeric|min:0|max:999999.99',
            'compare_price' => 'nullable|numeric|min:0|max:999999.99|gte:price',
            'level' => 'sometimes|in:beginner,intermediate,advanced',
            'cover_image' => 'nullable|file|mimes:jpeg,jpg,png,webp|max:5120', // 5MB max
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'title.min' => 'Course title must be at least 3 characters',
            'description.min' => 'Course description must be at least 20 characters',
            'category_id.exists' => 'Selected category is invalid',
            'price.min' => 'Course price cannot be negative',
            'compare_price.gte' => 'Compare price must be greater than or equal to the course price',
            'cover_image.mimes' => 'Cover image must be a JPEG, JPG, PNG, or WebP file',
            'cover_image.max' => 'Cover image size cannot exceed 5MB',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'category_id' => 'category',
            'compare_price' => 'original price',
            'cover_image' => 'cover image',
        ];
    }
}
