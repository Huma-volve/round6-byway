<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Order;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Payment>
 */
class PaymentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $status = $this->faker->randomElement(['initiated', 'requires_action', 'succeeded', 'failed', 'canceled', 'refunded']);
        $amount = $this->faker->numberBetween(1000, 10000);

        return [
            'order_id' => Order::factory(),
            'provider' => $this->faker->randomElement(['stripe', 'paypal']),
            'method'   => $this->faker->randomElement(['card', 'wallet', 'kiosk']),
            'amount_cents' => $amount,
            'currency' => 'USD',
            'status'   => $status,
            'external_id' => $this->faker->uuid,
            'error_code'  => null,
            'error_message' => null,
            'paid_at' => $status === 'succeeded' ? now() : null,
            'meta'    => json_encode(['ip' => $this->faker->ipv4]),
        ];
    }
}
