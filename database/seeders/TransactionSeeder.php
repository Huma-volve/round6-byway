<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\{Transaction, User, PaymentMethod};

class TransactionSeeder extends Seeder
{
    public function run(): void
    {
        $instructors = User::where('role', 'instructor')->get();
        $students    = User::where('role', 'student')->get();

        $paymentMethods = PaymentMethod::all();

        // لو الجدول فاضي نوقف
        if ($paymentMethods->isEmpty()) {
            $this->command->warn('⚠ No payment methods found, skipping TransactionSeeder.');
            return;
        }

        // Student payments (increase instructor balance)
        foreach ($students->take(50) as $student) {
            $instructor = $instructors->random();
            $amount = fake()->randomFloat(2, 20, 200);

            $paymentMethod = $paymentMethods->random();

            Transaction::create([
                'user_id'           => $instructor->id,  // instructor يستلم الفلوس
                'type'              => 'payment',
                'amount'            => $amount,
                'status'            => 'completed',
                'payment_method_id' => $paymentMethod->id,
                'meta'              => [
                    'paid_by' => $student->id,
                ],
            ]);

            $instructor->increment('balance', $amount);
        }

        // Instructor withdrawals
        foreach ($instructors->take(30) as $instructor) {
            if ($instructor->balance <= 0) {
                continue;
            }

            $amount = fake()->randomFloat(2, 10, min(300, $instructor->balance));
            $status = fake()->randomElement(['pending', 'completed', 'rejected']);

            $paymentMethod = $paymentMethods->random();

            Transaction::create([
                'user_id'           => $instructor->id,
                'type'              => 'withdrawal',
                'amount'            => $amount,
                'status'            => $status,
                'payment_method_id' => $paymentMethod->id,
            ]);

            if ($status === 'completed') {
                $instructor->decrement('balance', $amount);
            }
        }
    }
}
