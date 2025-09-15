<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ReviewRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            //
            'course_id' => 'required|exists:courses,id',
            'rating'    => 'required|integer|min:1|max:5',
            'comment'   => 'nullable|string|max:1000',
        ];
    }

    public function messages(): array
     {
        return[

            'course_id.required' => 'Course is required.',
            'course_id.exists'   => 'Selected course does not exist.',
            'rating.required'    => 'Rating is required.',
            'rating.integer'     => 'Rating must be a number.',
            'rating.min'         => 'Rating must be at least 1.',
            'rating.max'         => 'Rating cannot be more than 5.',
        ];
    }
}
