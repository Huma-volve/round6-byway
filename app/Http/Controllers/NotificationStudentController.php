<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationStudentController extends Controller
{
    //  عرض كل النوتيفيكيشنز
    public function index()
    {
        $notifications = Auth::user()->notifications;
        return response()->json($notifications);
    }

    //  عرض النوتيفيكيشنز الغير مقروءة
    public function unread()
    {
        $notifications = Auth::user()->unreadNotifications;
        return response()->json($notifications);
    }

    //  تعليم نوتيفيكيشن معينة كمقروءة
    public function markAsRead($id)
    {
        $notification = Auth::user()->notifications->where('id', $id)->first();

        if ($notification) {
            $notification->markAsRead();
            return response()->json(['message' => 'Notification marked as read']);
        }

        return response()->json(['message' => 'Notification not found'], 404);
    }

    //  تعليم كل النوتيفيكيشنز كمقروءة
    public function markAllAsRead()
    {
        Auth::user()->unreadNotifications->markAsRead();
        return response()->json(['message' => 'All notifications marked as read']);
    }

    //  مسح نوتيفيكيشن معينة
    public function destroy($id)
    {
        $notification = Auth::user()->notifications->where('id', $id)->first();

        if ($notification) {
            $notification->delete();
            return response()->json(['message' => 'Notification deleted']);
        }

        return response()->json(['message' => 'Notification not found'], 401);
    }

    //  مسح كل النوتيفيكيشنز
    public function clearAll()
    {
        Auth::user()->notifications()->delete();
        return response()->json(['message' => 'All notifications deleted']);
    }
}
