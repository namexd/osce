<?php
/**
 * Created by PhpStorm.
 * User: fengyell <Luohaihua@misrobot.com>
 * Date: 2016/1/8
 * Time: 15:42
 */

namespace Modules\Osce\Entities;

use Modules\Osce\Entities\CommonModel;
use DB;

class ExamRoom extends CommonModel
{
    protected $connection	=	'osce_mis';
    protected $table 		= 	'exam_room';
    public $incrementing	=	true;
    public $timestamps	    =	true;
    protected $fillable 	=	['exam_id','room_id','create_user_id'];

    /*
     * 所属考试
     */
    public function exam(){
        return $this->hasOne('\Modules\Osce\Entities\Exam','id','exam_id');
    }

    /*
     * 所属房间
     */
    public function room(){
        return $this->hasOne('\Modules\Osce\Entities\Room','id','room_id');
    }

    /**
     * 获取考试所属房间关系列表
     * @access public
     *
     * @param $exam_id
     *
     * @return mixed
     * @version 1.0
     * @author Luohaihua <Luohaihua@misrobot.com>
     * @date 2015-12-29 17:09
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     *
     */
    public function getRoomListByExam($exam_id){
        return $this    ->  where('exam_id','=',$exam_id)   ->  get();
    }

    public function getRoomSpTeachersByExamId($exam_id){
        return  Teacher::leftJoin('station_teacher',function($join){
            $join    ->  on('teacher.id','=','station_teacher.user_id');
        })  ->leftJoin('room_station',function($join){
            $join    ->  on('station_teacher.station_id','=','room_station.station_id');
        })
            ->where('teacher.type','=',2)
            ->where('station_teacher.exam_id','=',$exam_id)
            ->select([
                'teacher.id as id',
                'teacher.name as name',
                'teacher.code as code',
                'teacher.type as type',
                'teacher.case_id as case_id',
                'teacher.status as status',
                'room_station.room_id',
            ])
            ->get();
    }
    public function getRoomTeachersByExamId($exam_id){
        return  Teacher::leftJoin('station_teacher',function($join){
            $join    ->  on('teacher.id','=','station_teacher.user_id');
        })  ->leftJoin('room_station',function($join){
            $join    ->  on('room_station.station_id','=','station_teacher.station_id');
        })
            ->whereIn('teacher.type',[1,3])
            ->where('station_teacher.exam_id','=',$exam_id)
            ->select(DB::raw(implode(',',[
                'teacher.id as id',
                'teacher.name as name',
                'teacher.code as code',
                'teacher.type as type',
                'teacher.case_id as case_id',
                'teacher.status as status',
            ])))
            ->get();
    }


    //为邀请sp老师提供的页面数据

    public function getStationList($exam_id){
        return  Teacher::leftJoin('station_teacher',function($join){
            $join    ->  on('teacher.id','=','station_teacher.user_id');
        })  ->leftJoin('room_station',function($join){
            $join    ->  on('room_station.station_id','=','station_teacher.station_id');
        })   ->leftJoin('station',function($join){
            $join    ->  on('station.id','=','station_teacher.station_id');
        })->leftJoin($this->table,function($join){
            $join    ->  on('room_station.room_id','=','exam_room.room_id');
        })
            ->whereIn('teacher.type',[2])
            ->where('exam_room.exam_id','=',$exam_id)
            ->select(DB::raw(implode(',',[
                'teacher.id as id',
                'teacher.name as name',
                'teacher.code as code',
                'teacher.type as type',
                'teacher.case_id as case_id',
                'teacher.status as status',
                'station.name as station_name',
                'station.id as station_id'
            ])))
            ->get();

    }

    /**
     * 获取 考试对应的 考站、老师数据
     * @param $exam_id
     * @return mixed
     * @throws \Exception
     */
    public function getExamStation($exam_id)
    {
//        //TODO: 罗海华 2016-02-19 解决 sp 邀请 状错误问题
//        $examScreeningList  =   ExamScreening::where('exam_id','=',$exam_id)->get();
//        $examScreeningIdList    =   $examScreeningList  ->  pluck('id');

        try{
            $builder  =   StationTeacher::leftJoin('room_station','station_teacher.station_id','=','room_station.station_id')
                ->leftJoin('station','station.id','=','room_station.station_id')
//                                ->leftJoin('station_teacher','station_teacher.station_id','=','station.id')
                ->leftJoin('teacher','teacher.id','=','station_teacher.user_id')
//                ->leftJoin('invite', 'station_teacher.user_id','=','teacher.id')
//                ->where('exam_room.exam_id' , '=' , $exam_id)
                ->Where('station_teacher.exam_id','=',$exam_id)
                ->select([
                    'teacher.id as id',
                    'teacher.name as name',
                    'teacher.code as code',
                    'teacher.type as type',
                    'teacher.case_id as case_id',
//                    'invite.status as status',
                    'station.name as station_name',
                    'station.id as station_id',
                    'station.type as station_type',
                    'room_station.room_id as room_id',
                ])
                ->distinct()->get();
            return  $builder;
        } catch(\Exception $ex){
            throw $ex;
        }
    }

    /**
     * 获取 考试对应的 考场数据
     * @param $exam_id
     * @return mixed
     * @throws \Exception
     */
    public function getExamRoomData($exam_id)
    {
        try{
            $result = $this
             ->leftJoin('exam_flow_room',
                        function($join){
                            $join -> on($this->table.'.room_id', '=', 'exam_flow_room.room_id');
                        })
            -> leftJoin('room',
                function($join){
                    $join -> on($this->table.'.room_id', '=', 'room.id');
            })
            -> Join('exam_flow',
                function($join){
                    $join -> on($this->table.'.exam_id', '=', 'exam_flow.exam_id');
                    $join -> on('exam_flow_room.flow_id', '=', 'exam_flow.flow_id');
            })
            ->where($this->table.'.exam_id', '=', $exam_id)
            ->select([
                'room.id',
                'room.name',
                'exam_flow_room.serialnumber as serialnumber',
                $this->table.'.room_id as room_id',
                $this->table.'.exam_id as exam_id',
                $this->table.'.id as exam_room_id',
                'exam_flow_room.id as exam_flow_room_id',

            ])-> get();

            return $result;
        } catch(\Exception $ex){
            throw $ex;
        }

    }
//
//    //获取考站摄像机信息
//    public function getStionVcr($room_id,$exam_id){
//        try{
//            $result = $this-> leftJoin('room_station', function($join){
//                $join -> on($this->table.'.room_id', '=', 'room_station.room_id');
//            })  ->leftJoin('station_vcr', function($join){
//                $join -> on('room_station.station_id', '=', 'station_vcr.station_id');
//            })   ->leftJoin('vcr', function($join){
//                $join -> on('vcr.id', '=', 'station_vcr.vcr_id');
//            });
//                $result=$result ->where('room_station.room_id',$room_id);
//
//                $result=$result ->where($this->table.'.exam_id', '=', $exam_id);
//
//                $result= $result->select(['station_vcr.id','vcr.id','vcr.name','vcr.ip','vcr.status','vcr.port','vcr.channel','vcr.username','vcr.password'])
//            -> get();
//
//            return $result;
//        } catch(\Exception $ex){
//            return $ex;
//        }
//    }

    //获取候考教室列表
    public function getWaitRoom($exam_id){
        $time=time();
        try{

            $builder=$this->Join('room','room.id','=','exam_room.room_id');
            $builder=$builder->join('exam_plan', function($join)
            {
                $join->on('exam_room.exam_id', '=', 'exam_plan.exam_id')->On('exam_plan.room_id','=','room.id');
            });
            $builder=$builder->where('exam_plan.exam_id',$exam_id);
            $builder=$builder->whereRaw(
                'unix_timestamp('.'exam_plan.begin_dt'.') > ?',
                [
                    $time
                ]
            );

            $data= $builder->select([
                'room.id as room_id',
                'room.name as room_name',
                'room.address as room_address',
                'room.code as room_code',
            ])->get();
            return $data;
        }catch (\Exception $ex){
            throw $ex;
        }

    }
}