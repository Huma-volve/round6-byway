<?php

namespace App\Http\Requests\Admin\Course;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCourseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => ['sometimes', 'string', 'max:255'],
            'description' => ['sometimes', 'string', 'max:2000'],
            // 'price' => ['sometimes', 'decimal', 'min:0'],
            // 'compare_price' => ['sometimes', 'decimal', 'min:0', 'nullable'],
            'price' => ['sometimes', 'numeric', 'min:0'],
            'compare_price' => ['sometimes', 'numeric', 'min:0', 'nullable'],
            'category_id' => ['sometimes', 'exists:categories,id', 'nullable'],
            'status' => ['sometimes', 'in:draft,published,archived'],
            'level' => ['sometimes', 'in:beginner,intermediate,advanced'],
            'image' => ['sometimes', 'string', 'nullable'],
        ];
    }
}
