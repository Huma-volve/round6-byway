<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Stripe\StripeClient;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    public function checkout(request $request)
    {
        $request->validate([
            'amount_cents' => 'required|numeric|min:50',
            'currency' => 'string|in:usd,egp',
            'payment_method_id' => 'required|string',
        ]);
        $user = User::find(1);
        $stripe = new StripeClient(config('services.stripe.secret'));
        DB::beginTransaction();
        try {
            $amountCents = (int) $request->amount_cents;
            $payment = $user->payments()->create([
                'order_id' => 1, // مؤقتًا
                'provider' => 'stripe',
                'method' => 'card',
                'amount_cents' => $amountCents,
                'currency' => strtoupper($request->currency ?? 'USD'),
                'status' => 'initiated',
                'external_id' => '', // هنحدده بعدين

            ]);
            $intent = $stripe->paymentIntents->create([
                'amount' => $amountCents, // Stripe uses cents
                'currency' => $payment->currency,
                'payment_method' => $request->payment_method_id,
                'confirm' => true, // confirm immediately
                'payment_method_types' => ['card'],

            ]);
            $payment->update([
                'status' => $intent->status === 'succeeded' ? 'succeeded' : 'failed',
                'external_id' => $intent->id,
            ]);
            DB::commit();

            return response()->json([
                'status' => 'success',
                'intent' => $intent,
                'payment' => $payment,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to create payment record: ' . $e->getMessage(),
            ], 500);
        }
    }
}
