<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Payment;
use Stripe\StripeClient;
use Illuminate\Support\Facades\DB;
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

    // ==========================
    // CRUD على المدفوعات
    // ==========================
    public function index()
    {
        $payments = Payment::all();
        return response()->json($payments);
    }

    public function store(Request $request)
    {
        $payment = Payment::create($request->all());
        return response()->json([
            'message' => 'Payment created successfully',
            'data' => $payment
        ], 201);
    }

    public function show(Payment $payment)
    {
        return response()->json($payment);
    }

    public function update(Request $request, Payment $payment)
    {
        $payment->update($request->all());
        return response()->json([
            'message' => 'Payment updated successfully',
            'data' => $payment
        ]);
    }

    public function destroy(Payment $payment)
    {
        $payment->delete();
        return response()->json([
            'message' => 'Payment deleted successfully'
        ]);
    }

    // ==========================
    // Checkout عبر Stripe
    // ==========================
    public function checkout(Request $request)
    {
        $request->validate([
            'amount_cents' => 'required|numeric|min:50',
            'currency' => 'string|in:usd,egp',
            'payment_method_id' => 'required|string',
        ]);

        $user = $this->getAuthUser();
        $stripe = new StripeClient(config('services.stripe.secret'));

        // الحصول على آخر طلب (order) للمستخدم
        $order_id = $user->orders()->latest()->first()->id ?? null;

        DB::beginTransaction();
        try {
            [$payment, $intent] = $this->payments->CreatePayment(
                $user,
                $request->amount_cents,
                $request->currency,
                $request->payment_method_id
            );

            DB::commit();

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
                    'id'            => $intent->id,
                    'status'        => $intent->status,
                    'client_secret' => $intent->client_secret,
                ]
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to create payment: ' . $e->getMessage(),
            ], 500);
        }
    }

    // ==========================
    // سجل المدفوعات الخاص بالمستخدم
    // ==========================
    public function PaymentHistory(Request $request)
    {
        $user = $this->getAuthUser();

        try {
            $history = $this->payments->GetPaymentHistroy($user);
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
