<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;
use MongoDB\BSON\ObjectId;
use MongoDB\BSON\UTCDateTime;

class NotificationController extends Controller
{
    public function index()
    {
        $notifications = Notification::where('userId', auth()->user()->_id)
            ->where('status', 'unread')
            ->orderBy('createdAt', 'desc')
            ->get();

        return view('notifications.index', compact('notifications'));
    }

    public function markAsRead($id)
    {
        $notification = Notification::find($id);
        
        if ($notification) {
            $notification->update([
                'status' => 'read',
                'readAt' => new UTCDateTime()
            ]);
        }

        return redirect()->back()->with('success', 'Notification marked as read');
    }
}
