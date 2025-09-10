<?php

namespace App\Http\Requests\Admin\Instructor;

use Illuminate\Foundation\Http\FormRequest;

class IndexInstructorsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'q' => ['sometimes', 'string', 'max:100'],
            'status' => ['sometimes', 'in:active,blocked'],
            'sort' => ['sometimes', 'in:reg_date_desc,reg_date_asc,name_asc,name_desc'],
            'page' => ['sometimes', 'integer', 'min:1'],
            'per_page' => ['sometimes', 'integer', 'min:1', 'max:100'],
        ];
    }
}
