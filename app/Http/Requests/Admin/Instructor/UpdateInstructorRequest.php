<?php

namespace App\Http\Requests\Admin\Instructor;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateInstructorRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $instructorId = $this->route('instructor')?->id ?? null;

        return [
            'first_name' => ['sometimes', 'string', 'max:255'],
            'last_name' => ['sometimes', 'string', 'max:255'],
            'email' => ['sometimes', 'email', 'max:255', Rule::unique('users', 'email')->ignore($instructorId)],
            'username' => ['sometimes', 'string', 'max:255', Rule::unique('users', 'username')->ignore($instructorId)],
            'password' => ['sometimes', 'string', 'min:6', 'confirmed'],
            'status' => ['sometimes', 'in:active,blocked'],
        ];
    }
}
