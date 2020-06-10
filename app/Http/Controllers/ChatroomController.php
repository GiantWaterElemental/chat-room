<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Chatroom;

class ChatroomController extends Controller
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

    /**
     * Show the chatroom list.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function list()
    {
        $chatroom = new Chatroom();
        $list = $chatroom->list();
        $total = $chatroom->total();
        $count = count($list);
        return view('chatroomList', ['list' => $list, 'total' => $total, 'count' => $count]);
    }
}
