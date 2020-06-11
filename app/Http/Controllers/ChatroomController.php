<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redis;
use App\Chatroom;

class ChatroomController extends Controller
{
    protected $chatroom;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(Chatroom $chatroom)
    {
        $this->middleware('auth');
        $this->chatroom = $chatroom;
    }

    /**
     * Show the chat room list.
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

    /**
     * Enter the chat room
     *
     * @param int
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index($roomId)
    {
        $userId = Auth::user()->id;
        // Add user to the chat room redis set
        $key = 'chatroom_' . $roomId;
        $result = Redis::sadd($key, Auth::user()->name);
        $userList = Redis::smembers($key);

        $room = $this->chatroom->getChatRoomByRoomId($roomId);
        return view('chatroom', ['userList' => $userList, 'room' => $room]);
    }
}
