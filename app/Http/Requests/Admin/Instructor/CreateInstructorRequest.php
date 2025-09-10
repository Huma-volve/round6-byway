<?php

namespace App\Http\Requests\Admin\Instructor;

use Illuminate\Foundation\Http\FormRequest;

class CreateInstructorRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'username' => ['required', 'string', 'max:255', 'unique:users,username'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
            'status' => ['sometimes', 'in:active,blocked'],
            'profile' => ['sometimes', 'array'],
            'profile.headline' => ['sometimes', 'string', 'max:255'],
            'profile.about' => ['sometimes', 'string', 'max:2000'],
            'profile.skills' => ['sometimes', 'string', 'max:1000'],
            'profile.social_links' => ['sometimes', 'array'],
            'profile.image' => ['sometimes', 'string', 'nullable'],
        ];
    }
}
