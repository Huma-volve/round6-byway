<?php
namespace App\Services;

use Stripe\Stripe;
use Stripe\PaymentMethod;

class StripeService
{
    public function __construct()
    {
        Stripe::setApiKey(config('services.stripe.secret'));
    }

    public function createPaymentMethod($paymentMethodId)
    {
        return PaymentMethod::retrieve($paymentMethodId);
    }
}
