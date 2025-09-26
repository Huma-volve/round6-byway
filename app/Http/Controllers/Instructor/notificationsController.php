<?php

namespace App\Http\Controllers\instructor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class notificationsController extends Controller
{
        public function index(Request $request)
    {
        $user = Auth::user();

        $type = $request->query('type');

        // Base query
        $query = DB::table('notifications')
            ->where('notifiable_id', $user->id)
            ->where('notifiable_type', get_class($user));

        if ($type) {
            $query->where('type', $type);
        }

        $notifications = $query->orderBy('created_at', 'desc')->get();


        $formatted = $notifications->map(function ($notification) {
            $data = json_decode($notification->data, true);

            return [
                'id'         => $notification->id,
                'body'       => $data['body'] ?? null,
                'icon'       => $data['icon'] ?? null,
                'url'        => $data['url'] ?? null,
                'read_at'    => $notification->read_at,
                'created_at' => $notification->created_at,
            ];
        });

        return response()->json([
            'success' => true,
            'count'   => $formatted->count(),
            'notifications' => $formatted,
        ]);
    }
}
