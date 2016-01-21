<?php
/**
 * Created by PhpStorm.
 * User: fengyell <Luohaihua@misrobot.com>
 * Date: 2016/1/11
 * Time: 10:57
 */

namespace Modules\Osce\Entities;

use Illuminate\Database\Eloquent\Collection;
use Modules\Osce\Entities\CommonModel;
use Cache;


class ExamPlanForRoom extends CommonModel
{
    protected $connection	=	'osce_mis';
    protected $table 		= 	'exam_plan';
    public $incrementing	=	true;
    public $timestamps	    =	true;
    protected $fillable 	=	['exam_id','exam_screening_id','student_id','station_id','room_id','begin_dt','end_dt','status','created_user_id'];

    //场外等待学生
    protected $outSideWaiteStudent  =   [];

    //每个考场外等待学生（键名为流程编号）
    protected $flowWaiteStudent     =   [];

    //当前考试主流程
    protected $flowIndex            =   [];

    //流程列表
    protected $flowList             =   [];

    protected $nowTime              =   0;

    protected $stationList          =   [];

    protected $stationRoomGroup     =   [];

    protected $roomSerialnumberGroup=   [];

    protected $thisTimeRoomStudents =   [];

    protected $roomTime             =   [];

    protected $studentExamed        =   [];

    protected $studentExamedTime    =   [];

    //以下属性为结果记录用途
    //场次学生分组
    protected $screeningStudent     =   [];
    //当前考试已有学生
    protected $thisTimeRoomExamingStudent   =   [];

    public function student(){
        return $this->hasOne('\Modules\Osce\Entities\Student','id','student_id');
    }

    public function room(){
        return $this->hasOne('\Modules\Osce\Entities\Room','id','room_id');
    }

    public function station(){
        return $this->hasOne('\Modules\Osce\Entities\Station','id','station_id');
    }



    /**
     *  智能排考
     * @access public
     *
     * @param   object    $exam 考试数据实例
     *
     * @return array
     *
     * @version 1.0
     * @author Luohaihua <Luohaihua@misrobot.com>
     * @date 2015-12-29 17:09
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     *
     */
    public function IntelligenceEaxmPlan($exam){
        //初始化 场外等待学生
        $this   ->  initExamStudent($exam);

        $screeningList   =$exam   ->  examScreening;

        foreach($screeningList as $screening)
        {
            $startTime  =   strtotime($screening  ->begin_dt);
            $endTime    =   strtotime($screening  ->end_dt);
            //初始化时间
            $this       ->  nowTime   =   0;
            //初始化 考场外等待学生
            $this   ->  initFlowWaiteStudent($exam);

            $this   ->  examing($exam,$startTime,$endTime);
            //开考考生进场

        }
    }

    //考试中
    public function examing($exam,$startTime,$endTime){
        $nowTime    =   $this       ->  nowTime;
        $checkFrequency =   60;//时间检查频率
        for($nowTime;$nowTime<=$endTime;$nowTime+=$checkFrequency)
        {
            if($nowTime==0)
            {
                $nowTime    =   $startTime;
                $this       ->  nowTime =   $nowTime;
                //如果时间等于0 填满所有考场并初始化考场时间线
                $this       ->  fullAllRoom();
            }
            else
            {
                $this       ->  nowTime =   $nowTime;
                $this       ->  roomTimeChange($checkFrequency);
            }
        }
    }


    protected function roomTimeChange($checkFrequency){
        $roomTimeList   =   $this   ->  roomTime;
        $roomTimeUpdate =   [];
        foreach($roomTimeList as $roomId=>$roomTime)
        {
            $room       =   $this   ->  getRoomInfoById($roomId);
            $stations   =   $room   ->  stations;
            //获取当前考场单次所需时间
            $roomNeedTime   =   $this   ->  getRoomStaionMaxTime($room);
            if($roomNeedTime<=$roomTime)
            {
                $roomTimeUpdate[$roomId]    =   0;
                //如果考试时间到 获取当前考场中学生列表//将列表中学生请出考场
                $students   =   $this   ->  getStudentOutRoom($roomId);
                //将学生分别放入各自的下一个考场
                $this       ->  putStudentsToNextFlow($students);
                //获取当前考场下一批考生
                foreach($stations as $station)
                {
                    $this   ->  getStudentIntoToRoom($room);
                }
            }
            else
            {
                $roomTimeUpdate[$roomId]    +=  $checkFrequency;
            }
        }
        $this   ->  roomTime    =   $roomTimeUpdate;
    }

    //初始化考试学生
    public function initExamStudent($exam){
        $students   =   Student::where('exam_id','=',$exam->id)->get();
        $this   ->  outSideWaiteStudent =   $students;

        $studentExamed  =   [];

        foreach($students as $student)
        {
            $studentExamed[$students->id]   =   [];
        }

        $this   ->  studentExamed   =   $studentExamed;
        return $this;
    }

    //初始化考场学生数据
    protected function initRoomStudent($exam){
        //$thisTimeRoomStudents   =   $this   ->  thisTimeRoomStudents;
        $flowList   =   $this   ->  getExamFlowRoom($exam);
        $thisTimeRoomExamingStudent =   [];;
        foreach($flowList as $flow)
        {
            $thisTimeRoomExamingStudent[$flow->room_id]   =   [];
        }
        $this   ->  thisTimeRoomExamingStudent  =   $thisTimeRoomExamingStudent;
        return $this;
    }
    //获取当前学生下一个流程
    public function getStudentNextFlow(){

    }
    //初始化 流程等待列表
    public function initFlowWaiteStudent($exam){
        $flowList   =   $this   ->  getExamFlowRoom($exam);

        $this   ->  flowList    =   $flowList;

        $flowGroupList  =   $this->groupFlowBySerialnumber($flowList);
        $flowWaiteStudent   =   [];
        foreach($flowGroupList as $serialnumber=>$flowGroup)
        {
            $flowWaiteStudent[$serialnumber] =  [];
        }
        $this   ->  flowWaiteStudent    =   $flowWaiteStudent;
        return $flowWaiteStudent;
    }
    //将流程列表按照 流程编号 分组
    protected function groupFlowBySerialnumber($flowList){
        $group  =   [];
        foreach($flowList as $flow)
        {
            $group[$flow->serialnumber][]    =  $flow;
        }
        return $group;
    }

    //从场外获取一个学生 进入场内
    public function getOneStudentFormOutSideWaiteStudent(){
        $outSideWaiteStudent    =   $this   ->  outSideWaiteStudent;
        $student                =   array_shift($outSideWaiteStudent);
        $this   ->  outSideWaiteStudent =   $outSideWaiteStudent;
        if(empty($student))
        {
            $student    =  new \stdClass();
            $student    -> id       =   0;
            $student    ->  name    =   '空缺';
        }
        return $student;
    }

    //填满考场
    protected function fullAllRoom(){
        $stationList    =   $this   ->  getStationList();
        foreach($stationList as $station)
        {
            $this   ->  getStudentIntoToRoom($station->room);
            $this   ->  initRoomTime($station->room);
        }
    }

    //获取考站列表
    protected function getStationList(){
        $flowList       =   $this   ->  flowList;

        $stationList                =   [];
        $stationRoomGroup           =   [];
        $roomSerialnumberGroup      =   [];
        $roomList                   =   [];

        foreach($flowList as $flow)
        {
            $room       =   $flow   ->  room;
            $roomList[$room->id]    =   $room;
            $stations   =   $room   ->  stations;
            foreach($stations as $station)
            {
                $stationList[]      =   $station;
                $stationRoomGroup[$flow->room_id][] =   $station;
            }
            $roomSerialnumberGroup[$room->id][]  =   $flow   -> serialnumber;
        }
        $this   ->  stationList         =   $stationList;
        $this   ->  roomList            =   $roomList;
        $this   ->  stationRoomGroup    =   $stationRoomGroup;
        $this   ->  roomSerialnumberGroup    =   $roomSerialnumberGroup;
        return $stationList;
    }

    protected function getExamFlowRoom($exam){
        return ExamFlowRoom::where('exam_id','=',$exam->id)->get();
    }

    //获取一个学生进入考场
    protected function getStudentIntoToRoom($room){
        //获取房间所属流程编号
        $flowSerialnumber   =   $this   ->  getRoomRoomSerialnumber($room);
        //从流程候考池获取一个学生
        $student    =   $this   ->  getStudentFromFlowWaiteStudent($flowSerialnumber);
        //如果在学生已经在这个考场考过了 则取另外一个，然后放回原等待区
        $student    =   $this   ->  studentHaveEaxmedCheck($student,$room);
        //如果学生为空则在 场外候考区获取一个学生
        if(empty($student))
        {
            $student    =   $this   ->  getOneStudentFormOutSideWaiteStudent();
        }
        //将获取到的学生放入 考场
        $thisTimeRoomExamingStudentArray    =   $this   ->  thisTimeRoomExamingStudent;
        $thisTimeRoomExamingStudent         =   $thisTimeRoomExamingStudentArray[$room->id];

        $this                               ->  setStudentStartTime($student,$room);

        $thisTimeRoomExamingStudent[]       =   $student;
        $thisTimeRoomExamingStudentArray[$room->id] =   $thisTimeRoomExamingStudent;
        $this   ->  thisTimeRoomExamingStudent      =   $thisTimeRoomExamingStudentArray;
    }
    //让一个考场的学生 出考场
    protected function getStudentOutRoom($roomid){
        $thisTimeRoomStudentsArray  =   $this->thisTimeRoomStudents;
        $thisTimeRoomStudents       =   $thisTimeRoomStudentsArray[$roomid];
        $room                       =   $this->getRoomInfoById($roomid);
        foreach($thisTimeRoomStudents as $student)
        {
            //学生出考场时间
            $this   ->  setStudentEndTime($student,$room);
        }
        //清空当前考场学生
        $thisTimeRoomStudentsArray[$roomid] =   [];
        return $thisTimeRoomStudents;
    }

    //从流程候考池获取一个学生
    protected function getStudentFromFlowWaiteStudent($flowSerialnumber){
        $flowWaiteStudent   =   $this   ->  flowWaiteStudent;
        $WaitingStudent     =  $flowWaiteStudent[$flowSerialnumber];
        if(empty($WaitingStudent))
        {
            $student        =   [];
        }
        else
        {
            $student        =   array_shift($WaitingStudent);
        }

        $flowWaiteStudent[$flowSerialnumber]    =   $WaitingStudent;
        $this   ->  flowWaiteStudent            =   $flowWaiteStudent;
        return $student;
    }

    //移动房间下次使用所在流程指针
    protected function getRoomRoomSerialnumber($room){
        $roomSerialnumberGroup  =   $this   ->  roomSerialnumberGroup;
        $roomSerialnumberInfo   =   $roomSerialnumberGroup[$room->id];

        $thisSerialnumber       =   array_shift($roomSerialnumberInfo);
        $roomSerialnumberInfo[] =   $thisSerialnumber;

        $roomSerialnumberGroup[$room->id]   =   $roomSerialnumberInfo;
        $this   ->  roomSerialnumberGroup   =   $roomSerialnumberGroup;
        return  $thisSerialnumber;
    }

    protected function studentHaveEaxmedCheck($student,$room){
        //如果学生来过 该考场则 换一个学生，否则 将传入学生回传
        if(false)
        {
            $studentOld =   $student;
            //获取房间所属流程编号
            $flowSerialnumber   =   $this   ->  getRoomRoomSerialnumber($room);
            //从流程候考池获取一个学生
            $student    =   $this   ->  getStudentFromFlowWaiteStudent($flowSerialnumber);
            //把原学生放回等待队列
            $this       ->  putBackStudentToWaitQueue($flowSerialnumber,$studentOld);
            $student    =   $this   ->  studentHaveEaxmedCheck($student,$room);
        }
        return $student;
    }

    protected function putBackStudentToWaitQueue($flowSerialnumber,$student){
        $flowWaiteStudent   =   $this   ->  flowWaiteStudent;
        $WaitingStudent     =  $flowWaiteStudent[$flowSerialnumber];
        $WaitingStudent     =   array_unshift($WaitingStudent,$student);
        $flowWaiteStudent[$flowSerialnumber]    =   $WaitingStudent;
        $this   ->  flowWaiteStudent    =   $flowWaiteStudent;
        return $this;
    }

    protected function initRoomTime($room){
        $roomTime   =   $this   ->  roomTime;
        $roomTime[$room->id]    =   0;
        $this   ->  roomTime    =   $roomTime;
        return $this;
    }

    protected function getRoomInfoById($id){
        $roomList   =   $this   ->  roomList;
        if(array_key_exists($id,$roomList))
        {
            return $roomList[$id];
        }
        return [];
    }

    //获取考场中 最长 的考站 时间
    protected function  getRoomStaionMaxTime($room){
        $stations   =   $room   ->  stations;
        $maxMins    =   0;
        foreach($stations as $station)
        {
            $mins   =   $stations->station->mins * 60;
            $maxMins    =   $maxMins>$mins? $maxMins:$mins;
        }
        return $maxMins;
    }

    protected function putStudentsToNextFlow($students){
        foreach($students as $student)
        {
            $this   ->  putStudentToNextFlow($student);
        }
    }

    protected function putStudentToNextFlow($student){
        $studentExamedTime      =   $this->studentExamedTime;
        $studentFlowInfo    =   $studentExamedTime[$student->id];

        $flowIndex          =   $this->flowIndex;

        $serialnumberArray      =   array_keys($studentFlowInfo);
        $nextSerialnumberNum    =   array_pop($serialnumberArray)+1;

        $nextSerialnumberNum    =   $nextSerialnumberNum>count($flowIndex)? $nextSerialnumberNum-count($flowIndex):$nextSerialnumberNum;

        if(array_key_exists($nextSerialnumberNum,$serialnumberArray))
        {
            return [];
        }
        else
        {
            $next                   =   $flowIndex[$nextSerialnumberNum];
            return $next;
        }
    }

    protected function setStudentStartTime($student,$room){
        $studentExameTimedArray =   $this   ->  studentExamedTime;
        $thisStudentExamedTime  =   $studentExameTimedArray[$student->id];

        $thisSerialnumber   =   $this   ->  getRoomRoomSerialnumber($room);

        $thisStudentExamedTime[$thisSerialnumber][$this   ->  nowTime]   =   1;
        $studentExamedArray[$student->id]           =   $thisStudentExamedTime;
        $this   ->  studentExamedTime               =   $studentExamedArray;
        return $this;
    }

    protected function setStudentEndTime($student,$room){
        $studentExameTimedArray =   $this   ->  studentExamedTime;
        $thisStudentExamedTime  =   $studentExameTimedArray[$student->id];

        $thisSerialnumber   =   $this   ->  getRoomRoomSerialnumber($room);

        $thisStudentExamedTime[$thisSerialnumber][$this   ->  nowTime]   =   0;
        $studentExamedArray[$student->id]           =   $thisStudentExamedTime;
        $this   ->  studentExamedTime               =   $studentExamedArray;
        return $this;
    }
}