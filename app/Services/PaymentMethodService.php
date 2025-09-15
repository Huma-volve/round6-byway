<?php

namespace App\Services;

use Stripe\StripeClient;
use App\Models\User;

class PaymentMethodService
{
    protected StripeClient $stripe;
    public function __construct(StripeClient $stripe)
    {

        $this->stripe = $stripe;
    }
    public function AddPaymentMethod(User $user, string $payment_method_id)
    {
        // لو اليوزر ده مالوش customer في Stripe → نعمله
        if (!$user->stripe_customer_id) {
            $customer = $this->stripe->customers->create([
                'email' => $user->email,
                'name' => $user->name,
            ]);
            $user->upadte(['stripe_customer_id' => $customer->id]);
        }
        // نربط الـ payment method بالـ customer
        $this->stripe->paymentMethods->attach(
            $payment_method_id,
            ['customer' => $user->stripe_customer_id]
        );
        // نجيب تفاصيل الـ payment method من Stripe
        $paymentMethod = $this->stripe->paymentMethods->retrieve($payment_method_id);
        return $user->paymentMethods()->create([
        'stripe_payment_method_id' => $paymentMethod->id,
        'provider' => 'stripe',
        'brand' => $paymentMethod->card->brand ?? null,
        'last_four' => $paymentMethod->card->last4 ?? null,
        'is_default' => $user->paymentMethods()->count() === 0, // أول كارت يبقى Default
        ]);
    }
}
