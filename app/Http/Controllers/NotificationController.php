<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Payment;
use App\Models\DriverDues;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function MarkAsRead (Request $request)
    {
        $notification = auth()->user()->notifications()->find($request->notif_id);
        if($notification) {
            $notification->markAsRead();
            return response()->json([
                'success' => true
            ]);
        }

        return response()->json(false);
    }

}
