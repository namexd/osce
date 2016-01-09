<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/1/9 0009
 * Time: 11:05
 */

namespace Modules\Osce\Entities;

use DB;
class RoomStation extends CommonModel
{
    protected $connection = 'osce_mis';
    protected $table = 'room_station';
    public $timestamps = true;
    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $guarded = [];
    protected $hidden = [];
    protected $fillable = ['room_id', 'station_id', 'create_user_id'];


    public function getRoomStationData($room_id)
    {
        $result = $this->where('room_id', '=', $room_id)->get();
    }
}