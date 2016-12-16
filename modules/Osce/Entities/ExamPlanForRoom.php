<?php
/**
 * Created by PhpStorm.
 * User: fengyell <Zouyuchao@sulida.com>
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

    protected $studentList          =   [];

    protected $startTime            =   0;
    //以下属性为结果记录用途
    //场次学生分组
    protected $studentFlowPath      =   [];
    protected $screeningStudent     =   [];
    //当前考试已有学生
    protected $thisTimeRoomExamingStudent   =   [];

    protected $studentRecord        =   [];

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
     * @author Zouyuchao <Zouyuchao@sulida.com>
     * @date 2015-12-29 17:09
     * @copyright  2013-2017 sulida.com  Inc. All Rights Reserved
     *
     */
    public function IntelligenceEaxmPlan($exam){
        //初始化 场外等待学生
        $this   ->  initExamStudent($exam);
        $this   ->  initRoomStudent($exam);
        $flows  =   $this   ->  getExamFlow($exam);
        $this   ->  flowIndex   =   $this   ->  groupFlowByRoom($flows);

        $screeningList   =$exam   ->  examScreening;

        $screeningStudentData   =   [];

        foreach($screeningList as $screening)
        {
            $startTime  =   strtotime($screening  ->begin_dt);
            $endTime    =   strtotime($screening  ->end_dt);
            //初始化时间
            $this       ->  nowTime   =   0;
            //初始化 考场外等待学生
            $this   ->  initFlowWaiteStudent($exam);
            //考试
            $this   ->  examing($exam,$startTime,$endTime);
            $thisScreeningData                      =   $this   ->  groupScreeningData($this   ->  getStudentRecord());
            $screeningStudentData[$screening->id]   =   $this   ->  getDataRoomName($thisScreeningData);
            $this   ->  studentRecord   =   [];
        }
        return  $screeningStudentData;
    }

    //获取分组数据房间名称
    public function getDataRoomName($thisScreeningData){
        $data= [];
        foreach($thisScreeningData as $roomId=>$times)
        {
            $room   =   $this->getRoomInfoById($roomId);
            $roomInfo   =   [
                'name'  =>  $room   ->  name,
                'child' =>  []
            ];
            $timeData   =   [];
            foreach($times as $time =>  $studentsInfo)
            {
                $item   =   [];
                foreach($studentsInfo as $studentInfo)
                {
                    $item[$time]['students'][]  =   $studentInfo['student'];
                    $item[$time]['start']       =   $studentInfo['start'];
                    $item[$time]['end']         =   $studentInfo['end'];
                }
                $timeData['items']    =  $item[$time]['students'];
                $timeData['start']  =   $item[$time]['start'];
                $timeData['end']    =   $item[$time]['end'];
                $roomInfo['child'][]      =   $timeData;
            }
            $data[$roomId]          =   $roomInfo;
        }
        return $data;
    }
    public function groupScreeningData($studentRecord){
        $data   =  [];
        $studentList    =   $this   ->  studentList;
        foreach($studentRecord as $studentId=>$studentData)
        {
            foreach($studentData as $roomId=>$timeInfo)
            {
                foreach($timeInfo as $time)
                {
                    if($studentId==0)
                    {
                        $student    =   new \stdClass();
                        $student    ->  id  =   0;
                        $student    ->  name=   '空缺';
                        $data[$roomId][$time['start']][]=[
                            'start'     =>  $time['start'],
                            'end'       =>  $time['end'],
                            'student'   =>  $student
                        ];
                    }
                    else
                    {
                        $data[$roomId][$time['start']][]=[
                            'start'     =>  $time['start'],
                            'end'       =>  $time['end'],
                            'student'   =>  $studentList[$studentId]
                        ];
                    }
                }
            }
        }
        return $data;
    }


    //考试中
    public function examing($exam,$startTime,$endTime){
        $nowTime    =   $this       ->  nowTime;
        $this       ->  startTime   =   strtotime($startTime);
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
        $this   ->  clearRoomWhenExamOver();
        return  $this;
    }

    protected function clearRoomWhenExamOver(){
        $thisTimeRoomExamingStudent =   $this   ->  thisTimeRoomExamingStudent;
        $StudentRecord              =   $this   ->  getStudentRecord();
        foreach($thisTimeRoomExamingStudent as $roomdId=>$students)
        {
            foreach($students as $student)
            {
                foreach($StudentRecord[$student->id][$roomdId] as $key=>$item)
                {
                    $StudentRecord[$student->id][$roomdId][$key]['end'] =   $this->nowTime;
                }
            }
        }
        $this->studentRecord    =   $StudentRecord;
        return $this;
    }

    /**
     * 获取考试所有流程节点
     */
    public function getExamFlow($exam){
        return $exam    ->  flows;
    }

    /*
    *  为考场方式，根据考场分组流程
    */
    public function groupFlowByRoom($flows){
        $group  =   [];
        foreach($flows as $examFlow)
        {
            $examFlowRoomRelation       =   $examFlow   ->  examFlowRoomRelation;
            if(is_null($examFlowRoomRelation->  serialnumber))
            {
                throw new \Exception('序号数据错误');
            }
            $group[$examFlowRoomRelation->  serialnumber][]=$examFlowRoomRelation;
        }

        ksort($group);
        return $group;
    }

    protected function roomTimeChange($checkFrequency){
        $roomTimeList   =   $this   ->  roomTime;
        $roomTimeUpdate =   $roomTimeList;
        foreach($roomTimeList as $roomId=>$roomTime)
        {
            $room       =   $this   ->  getRoomInfoById($roomId);
            $stations   =   $room   ->  stations;
            //获取当前考场单次所需时间
            $roomNeedTime   =   $this   ->  getRoomStaionMaxTime($room);

            if($roomNeedTime<=$roomTime)
            {
                //如果当前考场考试时间到
                $roomTimeUpdate[$roomId]    =   0;
                //如果考试时间到 获取当前考场中学生列表//将列表中学生请出考场
                $students   =   $this   ->  getStudentOutRoom($roomId);

                foreach($students as $student)
                {
                    $this       ->  outRecord($student,$roomId);
                }

                //将学生分别放入各自的下一个考场
                $this       ->  putStudentsToNextFlow($students,$roomId);
                //获取当前考场下一批考生
                foreach($stations as $station)
                {
                    $this   ->  getStudentIntoToRoom($room);
                }
            }
            else
            {
                //如果当前考场考试时间没到
                if(array_key_exists($roomId,$roomTimeUpdate))
                {
                    $roomTimeUpdate[$roomId]    +=  $checkFrequency;
                }
                else
                {
                    $roomTimeUpdate[$roomId]    =   0;
                }
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
            $studentExamed[$student->id]   =   [];
            $studentList[$student->id]     =    $student;
        }

        $this   ->  studentExamed   =   $studentExamed;
        $this   ->  studentList     =   $studentList;
        return $this;
    }

    //初始化考场学生数据
    protected function initRoomStudent($exam){
        //$thisTimeRoomStudents   =   $this   ->  thisTimeRoomStudents;
        $flowList   =   $this   ->  getExamFlowRoom($exam);
        $thisTimeRoomExamingStudent =   [];
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
//        $student                =   array_shift($outSideWaiteStudent);
        $student    =   $outSideWaiteStudent->shift();
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
            $this   ->  initRoomTime($station->room);
            $this   ->  getStudentIntoToRoom($station->room);
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
        $thisTimeRoomStudents   =   $this->thisTimeRoomStudents;
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
        $thisTimeRoomStudents[$room->id][$student->id]  =   $student;
        $this   ->thisTimeRoomStudents      =   $thisTimeRoomStudents;
        $thisTimeRoomExamingStudentArray[$room->id] =   $thisTimeRoomExamingStudent;
        $this   ->  startRecord($student,$room->id);
        $this   ->  thisTimeRoomExamingStudent      =   $thisTimeRoomExamingStudentArray;
    }
    //让一个考场的学生 出考场
    protected function getStudentOutRoom($roomid){
        $thisTimeRoomExamingStudentArray    =   $this   ->  thisTimeRoomExamingStudent;
//        dump($thisTimeRoomExamingStudentArray);
        $thisTimeRoomStudentsArray  =   $this->thisTimeRoomStudents;
        if(!array_key_exists($roomid,$thisTimeRoomStudentsArray))
        {
            return [];
        }
        $thisTimeRoomStudents       =   $thisTimeRoomStudentsArray[$roomid];
        $thisTimeRoomExamingStudent =   $thisTimeRoomExamingStudentArray[$roomid];
        $room                       =   $this->getRoomInfoById($roomid);
        foreach($thisTimeRoomStudents as $student)
        {
            //学生出考场时间
            $this   ->  setStudentEndTime($student,$room);
            //unset($thisTimeRoomExamingStudent[$student->id]);

        }
//        $thisTimeRoomExamingStudentArray[$roomid]   =   $thisTimeRoomExamingStudent;
        $thisTimeRoomExamingStudentArray[$roomid]   =   [];
//        dump($thisTimeRoomExamingStudentArray);
        $this   ->  thisTimeRoomExamingStudent      =   $thisTimeRoomExamingStudentArray;
        //清空当前考场学生
        $thisTimeRoomStudentsArray[$roomid] =   [];
        $this   ->  thisTimeRoomStudents =   $thisTimeRoomStudentsArray;
//        return $thisTimeRoomStudents;
        return $thisTimeRoomExamingStudent;
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
            $mins   =   $station->station->mins * 60;
            $maxMins    =   $maxMins>$mins? $maxMins:$mins;
        }
        return $maxMins;
    }

    protected function putStudentsToNextFlow($students,$roomId=null){
        $studentFlowPath    =   $this->studentFlowPath;
        //dump($this->studentFlowPath);
        foreach($students as $student)
        {

            if(array_key_exists($student->id,$studentFlowPath))
            {
                $thisStudenHaveExamed   =   $studentFlowPath[$student->id];
            }
            else
            {
                $thisStudenHaveExamed   =   [];
            }

            if(count($thisStudenHaveExamed)<count($this->flowIndex))
            {
                $this   ->  putStudentToNextFlow($student,$roomId);
            }
        }
    }

    protected function putStudentToNextFlow($student,$roomId=null){
        $studentExamedTime      =   $this->studentExamedTime;
        $flowWaiteStudent       =   $this->flowWaiteStudent;
        $flowIndex              =   $this   ->  flowIndex;


        if(!array_key_exists($student->id,$studentExamedTime))
        {

            $nextSerialnumberNum    =   2;
            $serialnumberArray      =   [];
            $this   ->  setStudentStartTime($student,$this->getRoomInfoById($roomId));
            $studentExamedTime      =   $this->studentExamedTime;
        }
        $studentFlowInfo        =   $studentExamedTime[$student->id];//dump($student->id);
        $serialnumberArray      =   array_keys($studentFlowInfo);
        $thisFolw               =   array_pop($serialnumberArray);
        $nextSerialnumberNum    =   $thisFolw+1;

        $studentFlowPath    =   $this   ->  studentFlowPath;
        //dump($thisFolw);
        $studentFlowPath[$student->id][]    =   $thisFolw;
        $this   ->  studentFlowPath =   $studentFlowPath;
        if(count($studentFlowPath[$student->id])>=count($flowIndex))
        {
            return [];
        }
        $nextSerialnumberNum    =   $nextSerialnumberNum>count($flowIndex)? $nextSerialnumberNum-count($flowIndex):$nextSerialnumberNum;

        $flowWaiteStudent[$nextSerialnumberNum][]   =   $student;
        $this->flowWaiteStudent =   $flowWaiteStudent;
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
        $thisSerialnumber   =   $this   ->  getRoomRoomSerialnumber($room);
        if(!array_key_exists($student->id,$studentExameTimedArray))
        {
            $studentExameTimedArray[$student->id]   =   '';
            $thisStudentExamedTime  =   $studentExameTimedArray[$student->id];
            $thisStudentExamedTime[$thisSerialnumber][$this   ->  startTime]   =   1;
        }
        else
        {
            $thisStudentExamedTime  =   $studentExameTimedArray[$student->id];
            $thisStudentExamedTime[$thisSerialnumber][$this   ->  nowTime]   =   1;
        }

        $studentExamedArray[$student->id]           =   $thisStudentExamedTime;
//        dump($student->id);
//        dump($thisStudentExamedTime);
        $this   ->  studentExamedTime               =   $studentExamedArray;
        return $this;
    }

    protected function setStudentEndTime($student,$room){
        $studentExameTimedArray =   $this   ->  studentExamedTime;
        //dump($studentExameTimedArray);
        if(!array_key_exists($student->id,$studentExameTimedArray))
        {
//            dump($student->id);
//            dd($studentExameTimedArray);
            $studentExameTimedArray[$student->id]   =   '';
        }
        $thisStudentExamedTime  =   $studentExameTimedArray[$student->id];

        $thisSerialnumber   =   $this   ->  getRoomRoomSerialnumber($room);

        $thisStudentExamedTime[$thisSerialnumber][$this   ->  nowTime]   =   0;
        $studentExamedArray[$student->id]           =   $thisStudentExamedTime;
//        dump($student->id);
//        dump($thisStudentExamedTime);
//        dd(123);
        $this   ->  studentExamedTime               =   $studentExamedArray;
        return $this;
    }

    public function getStudentRecord(){
        return $this->studentRecord;
    }

    protected function outRecord($student,$roomId){
        $studentRecord  =   $this   ->  studentRecord;
        //出门顺序检查代码
        //dump('out:'.$roomId.'-'.$student->id);
        if(!array_key_exists($student->id,$studentRecord))
        {
            $studentRecord[$student->id]    =   [];
        }

        if(!array_key_exists($roomId,$studentRecord[$student->id]))
        {
            $studentRecord[$student->id][$roomId]   =   [];
        }

        if(empty($studentRecord[$student->id][$roomId]))
        {
            throw new \Exception('没有找到开始时间');
        }

        $key    =   0;
        foreach($studentRecord[$student->id][$roomId] as $key=>$times){
            if(!array_key_exists('end',$times))
            {
                $times['end']   =   $this->nowTime;
                break;
            }
        }
        $studentRecord[$student->id][$roomId][$key] =   $times;
        $this   ->  studentRecord   =   $studentRecord;
        return $this;
    }

    public function startRecord($student,$roomId){
        $studentRecord  =   $this   ->  studentRecord;
        //进门顺序检查代码
        //dump('in:'.$roomId.'-'.$student->id);
        if(!array_key_exists($student->id,$studentRecord))
        {
            $studentRecord[$student->id]    =   [];
        }

        if(!array_key_exists($roomId,$studentRecord[$student->id]))
        {
            $studentRecord[$student->id][$roomId]   =   [];
        }
        $studentRecord[$student->id][$roomId][]=[
            'start' =>  $this->nowTime
        ];
        $this   ->  studentRecord   =   $studentRecord;
        return $this;
    }

    public function getTimeList(){

    }

}