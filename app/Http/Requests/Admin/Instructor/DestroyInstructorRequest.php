<?php

namespace App\Http\Requests\Admin\Instructor;

use Illuminate\Foundation\Http\FormRequest;

class DestroyInstructorRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'confirm' => ['sometimes', 'accepted'],
            'email' => ['required_with:confirm', 'email'],
        ];
    }
}
