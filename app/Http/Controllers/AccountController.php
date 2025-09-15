<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AccountController extends Controller
{
    public function closeAccount(Request $request){

        $user = $request->user();
        $request->validate([
            'confirm' => 'required|boolean',
        ]);

        if (!$request->confirm) {
            return response()->json([
                'status' => 'error',
                'message' => 'Account closure not confirmed.'
            ], 400);
        }
        
        $user->status = 'blocked';
        $user->save();        

        $user->tokens()->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Your account has been deactivated successfully.'
        ]);        
    }

    public function reactivate(Request $request)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'status' => 'failed',
                'message' => 'Unauthenticated.'
            ], 401);
        }

        if ($user->status !== 'blocked') {
            return response()->json([
                'status' => 'failed',
                'message' => 'Account is already active.'
            ], 400);
        }

        // reactivate account
        $user->update([
            'status' => 'active'
        ]);

        // delete old tokens (reactivate_token)
        $user->tokens()->delete();

        // create normal auth token
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'status' => 'success',
            'message' => 'Account reactivated successfully.',
            'user' => $user,
            'token' => $token
        ], 200);
    }    
}
