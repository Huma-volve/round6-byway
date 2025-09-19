<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class HandleWithdrawalRequest extends FormRequest
{
    public function authorize(): bool
    {
        // هنا ممكن تتحقق إن اللي بيعمل approve/reject هو admin
        return $this->user() && $this->user()->role === 'admin';
    }

    public function rules(): array
    {
        return [
            'status' => 'required|in:completed,rejected',
        ];
    }
    
    public function failedAuthorization()
{
    abort(403, 'Only admins can approve or reject withdrawals.');
}

}
