<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        // 獲取登入會員的所有通知
        $notifications = auth()->user()->notifications()->paginate(20);

        // 標記為已讀，未讀數量歸零
        auth()->user()->markAsRead();

        return view('notifications.index', ['notifications' => $notifications]);
    }
}
