<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redis;
use App\Chatroom;
use App\Message;
use App\User;

class ChatroomController extends Controller
{
    protected $chatroom;
    protected $message;
    protected $user;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->chatroom = new Chatroom();
        $this->message = new Message();
        $this->user = new User();
    }

    /**
     * Show the chat room list.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function list()
    {
        $list = $this->chatroom->list();
        $total = $this->chatroom->total();
        $count = count($list);
        foreach ($list as &$room) {
            $key = 'chatroom_' . $room['room_id'];
            $room['count'] = Redis::scard($key);
        }
        return view('chatroomList', ['list' => $list, 'total' => $total, 'count' => $count]);
    }

    /**
     * Enter the chat room
     *
     * @param $roomId int
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index($roomId)
    {
        $userId = Auth::user()->id;
        // Add user to the chat room redis set
        $key = 'chatroom_' . $roomId;
        $result = Redis::sadd($key, Auth::user()->name);
        $userList = Redis::smembers($key);
        
        // Get latest message list
        $messageList = $this->message->list($roomId, 0, -1, 3, 0);
        foreach ($messageList as &$message) {
            $messageUser = $this->user->get($message['user_id']);
            $message['username'] = $messageUser->name;
        }

        $room = $this->chatroom->getChatRoomByRoomId($roomId);
        return view('chatroom', ['userList' => $userList, 'room' => $room, 'userId' => $userId, 'username' => Auth::user()->name, 'messageList' => $messageList]);
    }

    /**
     * ajax get chat room message list
     *
     * @param $roomId int
     * @param $messageId int
     * @return array
     */
    public function messageList(Request $request)
    {
        // Get previous message list
        $roomId = $request->input('roomId');
        $messageId = $request->input('messageId');
        $order = $request->input('order');
        $messageList = $this->message->list($roomId, $messageId, 0, 3, $order);
        foreach ($messageList as &$message) {
            $messageUser = $this->user->get($message['user_id']);
            $message['username'] = $messageUser->name;
        }
        if (!$order) {
            $messageList = $messageList->reverse();
        }

        return json_encode($messageList);
    }

}
