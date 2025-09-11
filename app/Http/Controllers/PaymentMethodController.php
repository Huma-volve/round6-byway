<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Stripe\StripeClient;
use Stripe\Stripe;
use Stripe\PaymentMethod;

class PaymentMethodController extends Controller
{
public function store(Request $request)
{
    $request->validate([
        'payment_method_id' => 'required|string',
    ]);

    $stripe = new \Stripe\StripeClient(config('services.stripe.secret'));

    // مؤقتًا: هنستخدم user ثابت (ID = 1)
    $user = User::find(1);

    // لو اليوزر ده مالوش customer في Stripe → نعمله
    if (!$user->stripe_customer_id) {
        $customer = $stripe->customers->create([
            'email' => $user->email,
            'name' => $user->name,
        ]);

        $user->stripe_customer_id = $customer->id;
        $user->save();
    }

    // نربط الـ payment method بالـ customer
    $stripe->paymentMethods->attach(
        $request->payment_method_id,
        ['customer' => $user->stripe_customer_id]
    );

    // نجيب تفاصيل الـ payment method من Stripe
    $paymentMethod = $stripe->paymentMethods->retrieve($request->payment_method_id);

    // نخزن نسخة في جدول payment_methods
    $method = $user->paymentMethods()->create([
        'stripe_payment_method_id' => $paymentMethod->id,
        'provider' => 'stripe',
        'brand' => $paymentMethod->card->brand ?? null,
        'last_four' => $paymentMethod->card->last4 ?? null,
        'is_default' => $user->paymentMethods()->count() === 0, // أول كارت يبقى Default
    ]);

    return response()->json([
        'message' => 'Payment method attached & saved successfully',
        'data' => $method,
    ], 201);
}
    
public function listPaymentMethods($id)
{
    try {
        // هات اليوزر من الداتابيس
        $user = User::findOrFail($id);

        // هات الـ Payment Methods المخزنة عندك
        $methods = $user->paymentMethods()
            ->select('id', 'brand', 'last_four', 'is_default', 'provider')
            ->get();

        // لو مفيش أي كروت
        if ($methods->isEmpty()) {
            return response()->json([
                'status' => 'success',
                'payment_methods' => [],
                'message' => 'No saved payment methods found for this user',
            ]);
        }

        return response()->json([
            'status' => 'success',
            'payment_methods' => $methods,
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => $e->getMessage(),
        ], 500);
    }
}

}
