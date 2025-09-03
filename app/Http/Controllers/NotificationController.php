<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class NotificationController extends Controller
{
    /**
     * Mark a notification as read.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $notificationId
     * @return \Illuminate\Http\Response
     */
    public function read(Request $request, $id)
    {
        $notification = $request->user()->notifications()->findOrFail($id);
        $notification->markAsRead();

        if (isset($notification->data['link'])) {
            return redirect($notification->data['link'])->with('success', 'Notification marked as read.');
        }

        return redirect()->back()->with('success', 'Notification marked as read.');
    }
}
