<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Order;
use App\Models\Cart;
use App\Models\OrderItem;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function store(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }
        $cartItems = $user->carts()->with('course')->get();
        if ($cartItems->isEmpty()) {
            return response()->json(['message' => 'Cart is empty'], 400);
        }
        $total = DB::table('carts')
            ->join('courses', 'carts.course_id', '=', 'courses.id')
            ->where('carts.student_id', $user->id) 
            ->sum('courses.price');

        $order = Order::create([
            'user_id' => $user->id,
            'status' => 'pending',
            'currency' => 'USD',
            'subtotal_cents' => $total * 100,
            'discount_cents' => 0,
            'tax_cents' => 0,
            'total_cents' => $total * 100,
            'placed_at' => now(),
        ]);
        foreach ($cartItems as $item) {
            OrderItem::create([
                'order_id' => $order->id,
                'purchasable_type' => 'Course',
                'purchasable_id' => $item->course->id,
                'unit_price_cents' => $item->course->price * 100,
                'quantity' => 1,
                'total_cents' => $item->course->price * 100,

            ]);
        }
        Cart::where('student_id', $user->id)->delete();
        return response()->json(['message' => 'Order created successfully', 'order' => $order], 201);
    }
}
