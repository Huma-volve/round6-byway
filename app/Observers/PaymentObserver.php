<?php

namespace App\Observers;

use App\Models\Payment;
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
