<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Order;
use App\Models\Payment;

class PaymentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $orders = Order::where('status', 'paid')->get();

        foreach ($orders as $order) {
            Payment::factory()->create([
                'order_id' => $order->id,
                'amount_cents' => $order->total_cents,
                'currency' => $order->currency,
                'status' => 'succeeded',
                'paid_at' => now(),
            ]);
        }
    }
}
