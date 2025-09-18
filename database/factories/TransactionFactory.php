<?php

namespace Database\Factories;

use App\Models\Transaction;
use Illuminate\Database\Eloquent\Factories\Factory;

class TransactionFactory extends Factory
{
    protected $model = Transaction::class;

    public function definition(): array
    {
        return [
            'type'   => $this->faker->randomElement(['payment', 'withdrawal']),
            'amount' => $this->faker->randomFloat(2, 10, 1000),
            'status' => $this->faker->randomElement(['pending', 'completed', 'rejected']),
            'method' => $this->faker->randomElement(['bank', 'visa', 'paypal', 'cash']),
            'meta'   => null,
        ];
    }
}
