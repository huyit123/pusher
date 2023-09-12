<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Pusher\Pusher;

class PusherController extends Controller
{
    public function index()
    {
        return view('pusher');
    }

    public function sendNotification(Request $request)
    {
        $message = $request->input('message');

        $pusher = new Pusher(
            config('broadcasting.connections.pusher.key'),
            config('broadcasting.connections.pusher.secret'),
            config('broadcasting.connections.pusher.app_id'),
            [
                'cluster' => config('broadcasting.connections.pusher.options.cluster'),
                'useTLS' => true,
            ]
        );

        $data = ['message' => $message];
        $pusher->trigger('my-channel', 'my-event', $data);

        return response()->json(['message' => 'Notification sent']);
    }
}
