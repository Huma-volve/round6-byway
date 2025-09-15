<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PlatformSettingRequest extends FormRequest
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

              'commission_percentage' => ['required', 'numeric', 'min:0', 'max:100'],
            'withdrawal_limit' => ['required', 'numeric', 'min:0'],
            'notifications_enabled' => ['required', 'boolean'],
        ];
        
    }

    public function messages(): array
    {
        return [
            'commission_percentage.required' => 'Commission percentage is required.',
            'commission_percentage.numeric' => 'Commission percentage must be a number.',
            'commission_percentage.min' => 'Commission percentage cannot be less than 0.',
            'commission_percentage.max' => 'Commission percentage cannot exceed 100.',
            
            'withdrawal_limit.required' => 'Withdrawal limit is required.',
            'withdrawal_limit.numeric' => 'Withdrawal limit must be a number.',
            'withdrawal_limit.min' => 'Withdrawal limit must be greater than or equal to 0.',

            'notifications_enabled.required' => 'Notifications status is required.',
            'notifications_enabled.boolean' => 'Notifications must be true or false.',
        ];
    }
}
