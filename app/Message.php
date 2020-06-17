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
    protected $table = 'chatroom';

	/**
     * Return list of chatroom with page size.
     *
     * @var array
     */
    public function list()
    {
    	$list = Chatroom::select('room_id', 'name', 'imgpath')
    		->limit($this->pageSize)
    		->get();
    	return $list;
    }

	/**
     * Return total number of chatroom.
     *
     * @var int
     */
    public function total()
    {
    	$total = Chatroom::count();
    	return $total;
    }

	/**
     * Return chatroom with given id.
     *
     * @var array
     */
    public function getChatRoomByRoomId($id)
    {
    	$data = Chatroom::select('room_id', 'name', 'imgpath')
    		->where('room_id', $id)
    		->first();
    	return $data;
    }
}
