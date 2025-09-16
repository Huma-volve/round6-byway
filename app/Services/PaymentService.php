<?php

namespace App\Services;

use Stripe\StripeClient;
use App\Models\User;
use App\Models\Payment;
use Illuminate\Support\Facades\DB;
use App\Services\InstructorEarnings;

class PaymentService
{
    protected $stripe;
    protected $instructor_earnings;
    public function __construct()
    {
        $this->stripe = new StripeClient(config('services.stripe.secret'));
        $this->instructor_earnings = new InstructorEarnings();
    }
    public function CreatePayment(User $user, int $amount_cents, string $currency, string $payment_method_id)
    {
        return DB::transaction(function () use ($user, $amount_cents, $currency, $payment_method_id) {
            $order = $user->orders()->latest()->first();
            $payment = $user->payments()->create([
                'type' => 'payment',
                'order_id' => $order->id,
                'provider' => 'stripe',
                'method' => 'card',
                'amount_cents' => $amount_cents,
                'currency' => strtoupper($currency ?? 'USD'),
                'status' => 'initiated',
                'external_id' => '',

            ]);
            $intent = $this->stripe->paymentIntents->create([
                'amount' => $amount_cents,
                'currency' => $payment->currency,
                'customer' => $user->stripe_customer_id,       // <--- مهم
                'payment_method' => $payment_method_id,
                'confirm' => true,
                'payment_method_types' => ['card'],

            ]);
            $payment->update([
                'status' => $intent->status === 'succeeded' ? 'succeeded' : 'failed',
                'external_id' => $intent->id,
            ]);
            $payment_id = $payment->id;
            
            if ($intent->status == 'succeeded') {
                event(new \App\Events\SaveOrder($order, $payment_id));
            }

            //$this->instructor_earnings->storeEarnings($order->id, $payment_id);

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
