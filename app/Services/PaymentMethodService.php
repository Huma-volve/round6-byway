<?php

namespace App\Services;

use Stripe\StripeClient;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class PaymentMethodService
{
    protected StripeClient $stripe;

    public function __construct()
    {
        $this->stripe = new StripeClient(config('services.stripe.secret'));
    }

    public function addPaymentMethod(User $user, string $payment_method_id)
    {
        return DB::transaction(function () use ($user, $payment_method_id) {

            // Create Stripe Customer if not exists
            if (!$user->stripe_customer_id) {
                $customer = $this->stripe->customers->create([
                    'email' => $user->email,
                    'name'  => $user->first_name . ' ' . $user->last_name,
                ]);
                $user->update(['stripe_customer_id' => $customer->id]);
            }

            // Attach PaymentMethod to Stripe Customer if not already attached
            $attachedPMs = $this->stripe->paymentMethods->all([
                'customer' => $user->stripe_customer_id,
                'type' => 'card',
            ]);

            $alreadyAttached = collect($attachedPMs->data)
                ->pluck('id')
                ->contains($payment_method_id);

            if (!$alreadyAttached) {
                $this->stripe->paymentMethods->attach($payment_method_id, [
                    'customer' => $user->stripe_customer_id,
                ]);
            }

            // Retrieve PaymentMethod details from Stripe
            $pm = $this->stripe->paymentMethods->retrieve($payment_method_id);

            // Save in local DB
            return $user->paymentMethods()->create([
                'stripe_payment_method_id' => $pm->id,
                'provider' => 'stripe',
                'brand' => $pm->card->brand ?? null,
                'last_four' => $pm->card->last4 ?? null,
                'is_default' => $user->paymentMethods()->count() === 0, // First one default
            ]);
        });
    }
}
