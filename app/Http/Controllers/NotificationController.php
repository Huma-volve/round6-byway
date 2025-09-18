<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $notifications = $user->notifications()->paginate(3);
        if($notifications->count() === 0)
        {
            return response()->json([
                'status' => 'Success',
                'message' => 'No Notifications'
            ]);
        }
        $formatted = $notifications->map(function ($notification) {
            return [
                'id' => $notification->id,
                'type' => class_basename($notification->type),
                'details' => $notification->data, 
                'read_at' => $notification->read_at,
                'created_at' => $notification->created_at->toDateTimeString(),
            ];
        });

        return response()->json([
            'status' => 'Success',
            'notifications' => $formatted
        ]);
    }
}
