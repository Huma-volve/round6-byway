<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PaymentMethod;

class PaymentMethodSeeder extends Seeder
{
    public function run()
    {
        $methods = [
            [
                'user_id' => 1,
                'stripe_payment_method_id' => 'pm_card_visa',
                'provider' => 'stripe',
                'brand' => 'visa',
                'last_four' => '4242',
                'is_default' => true,
            ],
            [
                'user_id' => 1,
                'stripe_payment_method_id' => 'pm_card_mastercard',
                'provider' => 'stripe',
                'brand' => 'mastercard',
                'last_four' => '4444',
                'is_default' => false,
            ],
        ];

        foreach ($methods as $method) {
            PaymentMethod::firstOrCreate(
                ['stripe_payment_method_id' => $method['stripe_payment_method_id']],
                $method
            );
        }
    }
}
