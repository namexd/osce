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

    public function room(){
        return $this->hasOne('\Modules\Osce\Entities\Room','id','room_id');
    }

    public function station(){
        return $this->hasOne('\Modules\Osce\Entities\Station','id','station_id');
    }

    /**
     * 获取考场对应的考站数据
     * @return mixed
     * @throws \Exception
     */
    public function getRoomStationData($room_id)
    {
        try{
            $builder = $this->select(['station.id', 'station.name', 'station.type'])
                ->leftJoin ('station', function ($join) {
                    $join->on('station.id', '=', $this->table.'.station_id');
                })
                ->where($this->table.'.room_id', '=', $room_id)
                ->orderBy($this->table.'.created_at', 'desc')->get();

            return $builder;

        } catch (\Exception $ex) {
            throw $ex;
        }
    }
}