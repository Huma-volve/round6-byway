<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TransactionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'        => $this->id,

            // النوع (Payment / Withdrawal)
            'type'      => ucfirst($this->type),

            // المبلغ مع العملة
            'amount'    => [
                'value'    => round((float) $this->amount, 2),
                'currency' => $this->currency ?? 'USD',
                'formatted'=> number_format($this->amount, 2) . ' ' . ($this->currency ?? 'USD'),
            ],

            // الحالة (Pending / Completed / Rejected)
            'status'    => ucfirst($this->status),

            // طريقة الدفع / السحب
            'method'    => $this->paymentMethod ? [
                'provider'  => $this->paymentMethod->provider,
                'brand'     => $this->paymentMethod->brand,
                'last_four' => $this->paymentMethod->last_four,
            ] : ($this->method ?? '-'),

            // المستخدم الأساسي (غالبًا المدرس في حالة السحب)
            'user_name' => $this->user
                ? trim(($this->user->first_name ?? '') . ' ' . ($this->user->last_name ?? ''))
                : null,

            // الطالب (فقط في حالة الدفع)
            'student'   => ($this->type === 'payment' && $this->paidBy())
                ? trim(($this->paidBy()->first_name ?? '') . ' ' . ($this->paidBy()->last_name ?? ''))
                : null,

            // التاريخ بالتنسيق المطلوب في التصميم
            'date'      => $this->created_at->format('d M Y'),

            // روابط الأكشن (View / Approve / Reject)
            'actions'   => [
                'view'     => route('admin.payments.show', $this->id),
                'approve'  => $this->when(
                    $this->type === 'withdrawal' && $this->status === 'pending',
                    route('admin.payments.updateStatus', $this->id)
                ),
                'reject'   => $this->when(
                    $this->type === 'withdrawal' && $this->status === 'pending',
                    route('admin.payments.updateStatus', $this->id)
                ),
            ],

            // ميتا إضافية (لو تحتاجها في التقارير)
            'meta'      => $this->meta,
        ];
    }
}
