<?php

namespace App\Observers;

use App\Models\Payment;
use App\Models\Enrollment;
use App\Models\OrderItem;
use App\Models\Course;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;


class PaymentObserver
{
    /**
     * Handle the Payment "created" event.
     */
    public function created(Payment $payment): void
    {
        Cache::forget('admin_dashboard_stats');
    }


    /**
     * Handle the Payment "updated" event.
     */

    public function updated(Payment $payment): void
    {
        Cache::forget('admin_dashboard_stats');

        // Auto-fulfill successful course purchases
        if ($payment->wasChanged('status') && $payment->status === 'succeeded') {
            $order = $payment->order()->with(['items'])->first();
            if (!$order) {
                return;
            }

            DB::transaction(function () use ($order) {
                foreach ($order->items as $item) {
                    // Only handle Course items for now
                    if ($item->purchasable_type !== Course::class) {
                        continue;
                    }

                    $courseId = $item->purchasable_id;
                    $studentId = $order->user_id;

                    // Create enrollment if not exists
                    Enrollment::firstOrCreate(
                        [
                            'course_id' => $courseId,
                            'student_id' => $studentId,
                        ],
                        []
                    );
                }
            });
        }
    }


    /**
     * Handle the Payment "deleted" event.
     */
    public function deleted(Payment $payment): void
    {
        Cache::forget('admin_dashboard_stats');
    }

    /**
     * Handle the Payment "restored" event.
     */
    public function restored(Payment $payment): void
    {
        //
    }

    /**
     * Handle the Payment "force deleted" event.
     */
    public function forceDeleted(Payment $payment): void
    {
        //
    }
}
