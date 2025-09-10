<?php

namespace App\Http\Requests\Admin\Instructor;

use Illuminate\Foundation\Http\FormRequest;

class UpdateInstructorProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'headline' => ['sometimes', 'string', 'max:255'],
            'about' => ['sometimes', 'string', 'max:2000'],
            'skills' => ['sometimes', 'string', 'max:1000'],
            'social_links' => ['sometimes', 'array'],
            'image' => ['sometimes', 'string', 'nullable'],
        ];
    }
}
