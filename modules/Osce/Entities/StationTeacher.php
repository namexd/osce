<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/1/12 0012
 * Time: 14:52
 */

namespace Modules\Osce\Entities;


class StationTeacher extends CommonModel
{
    protected $connection = 'osce_mis';
    protected $table = 'station_teacher';
    public $timestamps = true;
    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $guarded = [];
    protected $hidden = [];
    protected $fillable = ['station_id', 'user_id', 'case_id', 'created_user_id', 'type', 'exam_id'];

    public function station()
    {
        return $this->belongsTo('\Modules\Osce\Entities\Station','station_id','id');
    }

    //根据老师的 用户id，查询对对应的考试ID集
    public function getExamToUser($user_id)
    {
        return $this->where('user_id', $user_id)->select(['exam_id'])->groupBy('exam_id')->get();
    }

    /**
     * TODO: Zhoufuxiang 2016-3-9
     * 获取摄像头信息
     */
    public function getVcrInfo($exam_id, $teacher_id, $room_id)
    {
        try{
            $data = $this->select(['vcr.id','vcr.name','vcr.ip','vcr.status','vcr.port','vcr.channel','vcr.username','vcr.password'])
                ->leftJoin('room_station', 'room_station.station_id', '=', $this->table.'.station_id')
                ->leftJoin('station_vcr', 'station_vcr.station_id', '=', $this->table.'.station_id')
                ->leftJoin('vcr', 'vcr.id', '=', 'station_vcr.vcr_id')
                ->where('room_station.room_id', $room_id)
                ->where($this->table.'.user_id', $teacher_id)
                ->where($this->table.'.exam_id', $exam_id)
                ->get();

            return $data;
        } catch(\Exception $ex){
            return $ex;
        }
    }

}