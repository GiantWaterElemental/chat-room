<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Chatroom extends Model
{
	public $pageSize = 9;

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
}
