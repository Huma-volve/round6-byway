<?php

namespace App\Http\Requests\Instructor\Lesson;

use Illuminate\Foundation\Http\FormRequest;

class ReorderLessonsRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $course = $this->route('course');
        return auth()->check() &&
               auth()->user()->role === 'instructor' &&
               $course->instructor_id === auth()->id() &&
               $course->status !== 'published'; // Can't reorder lessons in published courses
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        // return [
        //     'lessons' => 'required|array|min:1',
        //     'lessons.*.id' => 'required|integer|exists:lessons,id',
        //     'lessons.*.order' => 'required|integer|min:1',
        // ];
            $course = $this->route('course');

    return [
        'lessons' => 'required|array|min:1',
        'lessons.*.id' => [
            'required',
            'integer',
            function($attribute, $value, $fail) use ($course) {
                if (!$course->lessons()->where('id', $value)->exists()) {
                    $fail("Lesson ID $value does not belong to this course.");
                }
            }
        ],
        'lessons.*.order' => 'required|integer|min:1',
    ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'lessons.required' => 'Lessons array is required',
            'lessons.array' => 'Lessons must be an array',
            'lessons.*.id.required' => 'Each lesson must have an ID',
            'lessons.*.id.exists' => 'One or more lesson IDs are invalid',
            'lessons.*.order.required' => 'Each lesson must have an order',
            'lessons.*.order.min' => 'Lesson order must be at least 1',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Ensure lessons belong to the course
        $course = $this->route('course');
        $lessonIds = collect($this->lessons)->pluck('id')->toArray();

        $validLessonIds = $course->lessons()->whereIn('id', $lessonIds)->pluck('id')->toArray();

        if (count($lessonIds) !== count($validLessonIds)) {
            $this->failedValidation(
                validator([], [])->errors()->add('lessons', 'All lessons must belong to this course')
            );
        }
    }
}
