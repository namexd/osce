<?php
/**
 * Created by PhpStorm.
 * User: j5110
 * Date: 2016/1/6
 * Time: 19:24
 */

namespace Modules\Osce\Entities;

class ExamScreening extends CommonModel
{
    protected $connection = 'osce_mis';
    protected $table = 'exam_screening';
    public $timestamps = true;
    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $guarded = [];
    protected $hidden = [];
    protected $fillable = ['exam_id', 'room_id', 'begin_dt', 'end_dt', 'create_user_id', 'status', 'sort', 'total', 'nfc_tag'];

     //关联考试表
    public function  ExamInfo(){
        return $this->hasOne('Modules\Osce\Entities\exam','id','exam_id');
    }



    //根据考试id获得考站和老师数据
    public function getStationList($examId){

        $builder = $this->leftJoin (
            'room',
            function ($join) {
                $join->on('room.id','=',$this->table.'.room_id');

            }
        )   ->  leftJoin (
            'station',
            function ($join) {
                $join->on('station.room_id','=','room.id');
            }
        )   ->  leftJoin (
            'station_case',
            function ($join) {
                $join->on('station_case.station_id','=','station.id');
            }
        )   ->  leftJoin (
            'cases',
            function ($join) {
                $join->on('case.id','=','station_case.case_id');
            }
        )   ->  leftJoin (
            'teacher_sp',
            function ($join) {
                $join->on('teacher_sp.case_id','=','case.id');
            }
        )->  leftJoin (
            'teacher',
            function ($join) {
                $join->on('teacher.id','=','teacher_sp.user_id');
            }
        )
            ->  where($this->table.'.id','=',$examId)
            ->  select([
                'station.id as station_id',
                'station.name as station_name',
                'teacher.name as teacher_name',
                'teacher.id as teacher_id'
            ]);
        return $builder->get();

    }



}