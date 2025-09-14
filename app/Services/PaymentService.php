<?php

namespace App\Services;

use Stripe\StripeClient;
use App\Models\User;
use App\Models\Payment;
use Illuminate\Support\Facades\DB;

class PaymentService
{
    protected $stripe;
    public function __construct()
    {
        $this->stripe = new StripeClient(config('services.stripe.secret'));
    }
    public function CreatePayment(User $user, int $amount_cents, string $currency, string $payment_method_id)
    {
        return DB::transaction(function () use ($user, $amount_cents, $currency, $payment_method_id) {
            $order_id = $user->orders()->latest()->first()->id;
            $payment = $user->payments()->create([
                'order_id' => $order_id, // مؤقتًا
                'provider' => 'stripe',
                'method' => 'card',
                'amount_cents' => $amount_cents,
                'currency' => strtoupper($request->currency ?? 'USD'),
                'status' => 'initiated',
                'external_id' => '', // هنحدده بعدين

            ]);
            $intent = $this->stripe->paymentIntents->create([
                'amount' => $amount_cents, // Stripe uses cents
                'currency' => $payment->currency,
                'payment_method' => $payment_method_id,
                'confirm' => true, // confirm immediately
                'payment_method_types' => ['card'],

            ]);
            $payment->update([
                'status' => $intent->status === 'succeeded' ? 'succeeded' : 'failed',
                'external_id' => $intent->id,
            ]);
            return [$payment, $intent];
        });
    }
    public function GetPaymentHistroy(User $user)
    {
        $payments = Payment::with(['order.items.purchasable'])
            ->whereHas('order', function ($q) use ($user) {
                $q->where('user_id', $user->id);
            })->get();
        if ($payments->isEmpty()) {
            return response()->json([
                'status' => 'Success',
                'data' => 'No payment history found',
            ]);
        }
        return $payments->flatmap(function ($payment) {
            return $payment->order->items->map(function ($item) use ($payment) {
                return [
                    'date'     => $payment->paid_at ?? $payment->created_at->format('M d, Y'),
                    'course'   => $item->purchasable->title,
                    'amount'   => $payment->amount_cents / 100,
                    'currency' => $payment->currency,
                    'method'   => $payment->method,
                ];
            });
        });
    }
}
