<?php

namespace App\Traits;

use Illuminate\Support\Facades\Auth;

trait AuthTrait
{
    protected function getAuthUser()
    {

        $user = Auth::user();
        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'User not authenticated',
            ], 401);
        }
        return $user;
    }
}
