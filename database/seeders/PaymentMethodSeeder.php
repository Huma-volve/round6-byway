<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PaymentMethod;
use App\Models\User;

class PaymentMethodSeeder extends Seeder
{
    public function run()
    {
        $methods = [
            [
                'provider' => 'paypal',
                'brand'    => 'paypal',
                'last_four'=> '0000',
            ],
            [
                'provider' => 'stripe',
                'brand'    => 'visa',
                'last_four'=> '4242',
            ],
            [
                'provider' => 'bank',
                'brand'    => 'bank-transfer',
                'last_four'=> '1111',
            ],
            [
                'provider' => 'cash',
                'brand'    => 'cash',
                'last_four'=> '9999',
            ],
        ];

        $users = User::all();

        foreach ($users as $user) {
            foreach ($methods as $method) {
                PaymentMethod::create([
                    'user_id'                  => $user->id,
                    'stripe_payment_method_id' => 'pm_' . uniqid(), // لازم يكون unique
                    'provider'                 => $method['provider'],
                    'brand'                    => $method['brand'],
                    'last_four'                => $method['last_four'],
                    'is_default'               => false,
                ]);
            }
        }
    }
}
