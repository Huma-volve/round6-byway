<?php

namespace App\Http\Requests\Admin\User;

use Illuminate\Foundation\Http\FormRequest;

class IndexUsersRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // guarded by middleware; policies later if needed
    }

    public function rules(): array
    {
        return [
            'q' => ['sometimes', 'string', 'max:100'],
            'role' => ['sometimes', 'in:student,instructor,admin'],
            'status' => ['sometimes', 'in:active,blocked'],
            'sort' => ['sometimes', 'in:reg_date_desc,reg_date_asc,name_asc,name_desc'],
            'page' => ['sometimes', 'integer', 'min:1'],
            'per_page' => ['sometimes', 'integer', 'min:1', 'max:100'],
        ];
    }
}
