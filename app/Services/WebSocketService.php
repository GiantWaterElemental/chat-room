<?php
namespace App\Services;

use Hhxsv5\LaravelS\Swoole\WebSocketHandlerInterface;

use Swoole\Http\Request;

use Swoole\WebSocket\Frame;

use Swoole\WebSocket\Server;

use Illuminate\Support\Facades\Auth;

use Illuminate\Support\Facades\Redis;

use App\Message;
/**

 * @see https://wiki.swoole.com/#/start/start_ws_server

 */

class WebSocketService implements WebSocketHandlerInterface

{

    protected $message;

    const MESSAGETYPE = [
        0,  //聊天消息
        1,  //心跳
        2,  //加入
        3,  //退出
    ];
    const ROOMKEY = 'chatroom_';
    const CHANNELKEY = 'room_channel_';
    const FDUSERKEY = 'fd_user_';


    // 声明没有参数的构造函数

    public function __construct()

    {

        $this->message = new Message();

    }

    public function onOpen(Server $server, Request $request)

    {

        echo "fd " . $request->fd . " connected";
        // 在触发onOpen事件之前，建立WebSocket的HTTP请求已经经过了Laravel的路由，

        // 所以Laravel的Request、Auth等信息是可读的，Session是可读写的，但仅限在onOpen事件中。

        // \Log::info('New WebSocket connection', [$request->fd, request()->all(), session()->getId(), session('xxx'), session(['yyy' => time()])]);

        // $server->push($request->fd, 'Welcome to LaravelS');

        // throw new \Exception('an exception');// 此时抛出的异常上层会忽略，并记录到Swoole日志，需要开发者try/catch捕获处理

        $roomId = $request->get['id'];
        $userId = Auth::user()->id;
        $username = Auth::user()->name;

        //get current fd from this room
        $channelKey = self::CHANNELKEY . $roomId;
        $channelFd = Redis::smembers($channelKey);

        //add this fd to this room
        $fdUserKey = self::FDUSERKEY . $request->fd;
        $result = Redis::sadd($channelKey, $request->fd);

        //set fd user relationship
        $result = Redis::set($fdUserKey, json_encode(['userId' => $userId, 'username' => $username, 'roomId' => $roomId]));
        
        $data = [
            "type" => self::MESSAGETYPE[2],
            "message" => $username . "加入了聊天室",
            "userId" => $userId,
            "username" => $username
        ];
        //send enter message to current users in this room
        foreach ($channelFd as $fd) {
            $server->push($fd, json_encode($data));
        }

    }

    public function onMessage(Server $server, Frame $frame)

    {

        $data = json_decode($frame->data, true);

        switch ($data['type']) {
            case self::MESSAGETYPE[0]:
                // insert into database
                $messageId = $this->message->add($data['roomId'], $data['userId'], $data['message']);
                $data['message_id'] = $messageId;
                $frame->data = json_encode($data);
                // \Log::info('Received message', [$frame->fd, $frame->data, $frame->opcode, $frame->finish]);
                foreach ($server->connections as $fd) {
                    $server->push($fd, $frame->data);
                }
                // throw new \Exception('an exception');// 此时抛出的异常上层会忽略，并记录到Swoole日志，需要开发者try/catch捕获处理
                break;

            case self::MESSAGETYPE[3]:
                // delete user from chat room redis set
                $key = ROOMKEY . $data['roomId'];
                $result = Redis::srem($key, $data['username']);

            case self::MESSAGETYPE[2]:
                foreach ($server->connections as $fd) {
                    $server->push($fd, $frame->data);
                }
                break;

            default:
                # code...
                break;

        }

    }

    public function onClose(Server $server, $fd, $reactorId)

    {

        // throw new \Exception('an exception');// 此时抛出的异常上层会忽略，并记录到Swoole日志，需要开发者try/catch捕获处理

        // get user by fd from redis
        $fdUserKey = self::FDUSERKEY . $fd;
        $user = Redis::get($fdUserKey);
        $user = json_decode($user, true);

        //get current fd from this room
        $roomId = $user['roomId'];
        $channelKey = self::CHANNELKEY . $roomId;
        $channelFd = Redis::smembers($channelKey);

        // delete user from chat room redis set
        $key = self::ROOMKEY . $data['roomId'];
        $result = Redis::srem($key, $data['username']);

        $data = [
            "type" => self::MESSAGETYPE[3],
            "message" => $user['username'] . "离开了聊天室",
            "userId" => $user['userId'],
            "username" => $user['username']
        ];
        // send exit message to current users in this room
        foreach ($channelFd as $fd) {
            $server->push($fd, json_encode($data));
        }

    }

}