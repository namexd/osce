<?php
/**
 * Created by PhpStorm.
 * User: fengyell <Luohaihua@misrobot.com>
 * Date: 2016/1/11
 * Time: 14:24
 */

namespace Modules\Osce\Entities;

use Modules\Osce\Entities\CommonModel;

class ExamFlowRoom extends CommonModel
{
    protected $connection = 'osce_mis';
    protected $table = 'exam_flow_room';
    protected $primaryKey = 'id';
    public $incrementing = true;
    public $timestamps = true;
    protected $fillable = ['serialnumber', 'room_id', 'flow_id', 'created_user_id', 'exam_id', 'effected'];

    /*
     * 所属房间
     */
    public function room()
    {
        return $this->hasOne('\Modules\Osce\Entities\Room', 'id', 'room_id');
    }

    public function queueStudent()
    {
        return $this->hasMany('\Modules\Osce\Entities\ExamQueue', 'room_id', 'room_id');
    }

    /**
     * 获取考场下 考站数量
     * @access public
     *
     * @return mixed
     *
     * @version 1.0
     * @author Luohaihua <Luohaihua@misrobot.com>
     * @date 2015-12-29 17:09
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     *
     */
    public function getRoomStationNum($examFlowRoom)
    {
        $room = $examFlowRoom->room;
        if (is_null($room)) {
            throw new \Exception('房间不存在');
        }
        $stations = $room->stations;
        return count($stations);
    }

    public function getRoomStaionTime($examFlowRoom)
    {
        $room = $examFlowRoom->room;
        if (is_null($room)) {
            throw new \Exception('房间不存在');
        }
        $stations = $room->stations;
        $mins = 0;
        foreach ($stations as $station) {
            $info = $station->station;
            if (is_null($info)) {
                throw new \Exception('考场不存在');
            }
            $mins = $mins > $info->mins ? $mins : $info->mins;
        }
        return $mins;
    }

    public function getRoomStationsByFlow($examFlowRoom)
    {
        $stationsData = [];
        foreach ($examFlowRoom as $one) {
            $room = $one->room;
            if (is_null($room)) {
                throw new \Exception('房间不存在');
            }
            $stations = $room->stations;
            foreach ($stations as $station) {
                $stationsData[] = $station->station;
            }
        }
        return $stationsData;
    }
}