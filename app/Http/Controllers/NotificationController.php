<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        $notifications = Notification::where('utilisateur_id', $request->user()->id)
            ->latest()
            ->paginate(20);

        return view('notifications.index', compact('notifications'));
    }

    public function markAsRead(Notification $notification)
    {
        if ($notification->utilisateur_id === auth()->id()) {
            $notification->update(['lu' => true]);
        }
        return back();
    }

    public function markAllRead(Request $request)
    {
        Notification::where('utilisateur_id', $request->user()->id)
            ->where('lu', false)
            ->update(['lu' => true]);

        return back()->with('success', __('Toutes les notifications marquées comme lues.'));
    }

    public function unreadCount(Request $request)
    {
        $count = Notification::where('utilisateur_id', $request->user()->id)
            ->where('lu', false)
            ->count();

        return response()->json(['count' => $count]);
    }
}
