<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    /**
     * table name.
     *
     * @var string
     */
    protected $table = 'message';

	/**
     * Insert message into database
     *
     * @param $roomId int
     * @param $userId int
     * @param $message str
     */
    public function add($roomId, $userId, $message)
    {
    	$messageId = Message::insertGetId([
            'room_id' => $roomId,
            'user_id' => $userId,
            'message' => $message
        ]);
    	return $messageId;
    }

    /**
     * Get message list
     *
     * @param $roomId int
     * @param $messageId int
     * @param $index int
     * @param $pageSize int
     * @param $order int
     */
    public function list($roomId, $messageId, $index, $pageSize, $order)
    {
        $orderBy = $order ? 'ASC' : 'DESC';
        $pageSize = $pageSize ? $pageSize : $this->pageSize;
        $where = [['room_id', '=', $roomId]];
        if ($messageId) {
            $where[] = $order ? ['message_id', '>', $messageId] : ['message_id', '<', $messageId];
        }
        $list = Message::select('message_id', 'user_id', 'message')
                    ->where($where)
                    ->orderBy('message_id', $orderBy)
                    ->limit($pageSize)
                    ->get();
        if (!$order) {
            $list = $list->reverse();
        }
        return $list;
    }
}
