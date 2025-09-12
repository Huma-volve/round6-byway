<?php

namespace App\Http\Requests\Admin\Instructor;

use Illuminate\Foundation\Http\FormRequest;

class SetInstructorStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'status' => ['required', 'in:active,blocked'],
        ];
    }
}
