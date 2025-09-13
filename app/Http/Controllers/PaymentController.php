<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
//use Illuminate\Container\Attributes\Auth;
use Stripe\StripeClient;
use Illuminate\Support\Facades\DB;
use App\Models\Payment;
use App\Services\PaymentService;
use App\Trait\AuthTrait;

class PaymentController extends Controller
{
    use AuthTrait;
    protected $payments;
    public function __construct(PaymentService $payments)
    {
        $this->payments = $payments;
    }
    public function checkout(request $request)
    {

        $request->validate([
            'amount_cents' => 'required|numeric|min:50',
            'currency' => 'string|in:usd,egp',
            'payment_method_id' => 'required|string',
        ]);
        $user = $this->getAuthUser();
        $stripe = new StripeClient(config('services.stripe.secret'));
        $order_id = $user->orders()->latest()->first()->id;
        DB::beginTransaction();
        try {
            [$payment, $intent] = $this->payments->CreatePayment(
                $user,
                $request->amount_cents,
                $request->currency,
                $request->payment_method_id
            );
            return response()->json([
                'status' => 'success',
                'payment' => [
                    'id'        => $payment->id,
                    'order_id'  => $payment->order_id,
                    'amount'    => $payment->amount_cents,
                    'currency'  => $payment->currency,
                    'status'    => $payment->status,
                ],
                'intent' => [
                    'id'     => $intent->id,
                    'status' => $intent->status,
                    'client_secret' => $intent->client_secret, // لو محتاجه في الـ Frontend
                ]
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to create payment record: ' . $e->getMessage(),
            ], 500);
        }
    }
    public function PaymentHistory(Request $request)
    {
        $user = $this->getAuthUser();

        try {

            $history = $this->payments->GetPaymentHistroy(($user));
            return response()->json([
                'status' => 'success',
                'data' => $history->isEmpty() ? 'No payment history found' : $history,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch payment history: ' . $e->getMessage(),
            ], 500);
        }
    }
}
