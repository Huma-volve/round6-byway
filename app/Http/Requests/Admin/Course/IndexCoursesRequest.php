<?php

namespace App\Http\Requests\Admin\Course;

use Illuminate\Foundation\Http\FormRequest;

class IndexCoursesRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'q' => ['sometimes', 'string', 'max:100'],
            'category_id' => ['sometimes', 'exists:categories,id'],
            'status' => ['sometimes', 'in:draft,published,archived'],
            'instructor_id' => ['sometimes', 'exists:users,id'],
            'sort' => ['sometimes', 'in:reg_date_desc,reg_date_asc,title_asc,title_desc,price_asc,price_desc'],
            'page' => ['sometimes', 'integer', 'min:1'],
            'per_page' => ['sometimes', 'integer', 'min:1', 'max:100'],
        ];
    }
}
