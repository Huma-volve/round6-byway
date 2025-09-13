<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use Illuminate\Http\Request;
use App\Models\User;
use Stripe\StripeClient;
use Stripe\Stripe;
use Stripe\PaymentMethod;
use App\Services\PaymentMethodService;
use App\Trait\AuthTrait;

class PaymentMethodController extends Controller
{
    use AuthTrait;
    protected PaymentMethodService $paymentMethodService;
    public function __construct(PaymentMethodService $paymentMethodService)
    {
        $this->paymentMethodService = $paymentMethodService;
    }
    public function store(Request $request)
    {
        $request->validate([
            'payment_method_id' => 'required|string',
        ]);
        $user = $this->getAuthUser();




        try {
            $method = $this->paymentMethodService->AddPaymentMethod($user, $request->payment_method_id);
            return response()->json([
                'status'  => 'success',
                'message' => 'Payment method attached & saved successfully',
                'data'    => $method,
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => 'error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function listPaymentMethods(Request $request)
    {
        try {
            $user = $this->getAuthUser();


            // هات الـ Payment Methods المخزنة عندك
            $methods = $user->paymentMethods()
                ->select('id', 'brand', 'last_four', 'is_default', 'provider')
                ->get();

            return response()->json([
                'status'           => 'success',
                'payment_methods'  => $methods,
                'message'          => $methods->isEmpty()
                    ? 'No saved payment methods found for this user'
                    : null,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
