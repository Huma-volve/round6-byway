<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    public function addToCart(Request $request){
        $request->validate([
            'course_id' => 'required|exists:courses,id',
        ]);

        $studentId = Auth::id();
        $existing = Cart::where('student_id', $studentId)
                ->where('course_id', $request->course_id)
                ->first();

        if ($existing) {
            return response()->json(['message' => 'Course already in cart']
            , 400);
        }

        $cart = Cart::create([
            'student_id' => $studentId,
            'course_id' => $request->course_id,
        ]);

        return response()->json([
            'message' => 'Course added to cart',
            'cart' => $cart
        ], 201);




    }

    public function getCart()
    {
        $studentId = Auth::id();

        $cartItems = Cart::with('course')
                        ->where('student_id', $studentId)
                        ->get();

        return response()->json($cartItems);
    }    
}
