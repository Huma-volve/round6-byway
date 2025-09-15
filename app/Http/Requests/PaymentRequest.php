<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PaymentRequest extends FormRequest
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
            'order_id'     => 'required|exists:orders,id',
            'provider'     => 'required|string|max:50',
            'method'       => 'required|string|max:50',
            'amount_cents' => 'required|integer|min:1',
            'currency'     => 'required|string|size:3',
            'status'       => 'required|in:initiated,requires_action,succeeded,failed,canceled,refunded',
            'external_id'  => 'required|string|max:191',
            'error_code'   => 'nullable|string|max:100',
            'error_message'=> 'nullable|string',
            'paid_at'      => 'nullable|date',
            'meta'         => 'nullable|json',
        ];
    }

    public function messages(): array
    {
        return [
            'order_id.required'    => 'Order ID is required.',
            'order_id.exists'      => 'The selected order does not exist.',
            'provider.required'    => 'Payment provider is required.',
            'method.required'      => 'Payment method is required.',
            'amount_cents.required'=> 'Payment amount is required.',
            'amount_cents.integer' => 'Payment amount must be an integer (in cents).',
            'currency.size'        => 'Currency must be exactly 3 characters.',
            'status.in'            => 'Invalid payment status.',
            'external_id.required' => 'External payment ID is required.',
        ];
    }
}
