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


class ExamPlan extends CommonModel
{
    protected $connection	=	'osce_mis';
    protected $table 		= 	'exam_plan';
    public $incrementing	=	true;
    public $timestamps	    =	true;
    protected $fillable 	=	['exam_id','exam_screening_id','student_id','station_id','room_id','begin_dt','end_dt','status','created_user_id'];

    protected $stations     =   [];
    protected $cellTime     =   0;
    protected $batchTime    =   0;
    protected $flowsIndex   =   [];
    protected $allStudent   =   [];

    protected $timeList     =   [];
    protected $roomList     =   [];
    protected $screeningStudents     =   [];
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
        $this   ->  stations   =   $this   ->  getAllStation($exam);
        $this   ->  allStudent =   $this   ->  getExamStudent($exam);

        $mins   =   $this   ->  getMaxStationTime();
        $this   ->  getBatchTime();
        $examScreenings =   $exam   ->  examScreening;
        $timeList       =   $this   ->  timeList;
        foreach($examScreenings as $examScreening)
        {
            $batchNum   =  $this   ->  getBatchNum($examScreening);
            $screeningTimeList      =   $this   ->  setEachBatchTime($examScreening,$batchNum);
            $timeList[$examScreening->id] = $screeningTimeList;
        }

        $plan   =   $this->distribute($timeList);

        $groupData  =   $this->makeGroupPlanByRoom($plan);
        return  $groupData;
    }

    public function distribute($timeList){
        $screeningStudents  =   $this   ->  screeningStudents;
        $flowsIndex         =   $this->flowsIndex;
        $examScreeningIndex =   0;

        $plan   =   [];

        foreach($timeList as $examScreeningId   =>  $examScreeningBactchList)
        {
            foreach($examScreeningBactchList as $batchId    =>   $batch)
            {
                $batchStudnet   =   [];
                foreach($batch as $serialnumber    =>  $batchInfo)
                {
                    if($batchStudnet===[])
                    {
                        $batchStudnet   =   $screeningStudents[$examScreeningIndex][$batchId];
                    }
                    else
                    {
                        $batchStudnet   =   $this   ->  changeStudentIndex($batchStudnet);
                    }
                    $stationIndex   =   0;
                    foreach($batchInfo as $stationId  =>  $batchData)
                    {
                        $stationStudnet     =   $batchStudnet[$stationIndex];
                        $stationIndex++;
                        $plan[$examScreeningId][$stationId][$batchData]   =   $stationStudnet;
                    }
                }
            }
            $examScreeningIndex++;
        }
        return $plan;
    }

    public function changeStudentIndex($batchStudnet){
        $data   =   [];
        $total  =   count($batchStudnet);
        foreach($batchStudnet as $index=>$student)
        {
            $newIndex           =   $index+1;
            if($newIndex>=$total)
            {
                $newIndex       =   $newIndex-$total;
            }
            $data[$newIndex]    =   $student;
        }
        return $data;
    }

    public function getPerBatchStudent(){
        $num        =   count($this->stations);
        $students   =   $this   ->  getStudentByNum($num);
        return $students;
    }
    public function getPerBatchStudentHaveWatch(){

    }
    public function getBatchNum($examScreening){
        $start  =   strtotime($examScreening->begin_dt);
        $end    =   strtotime($examScreening->end_dt);
        if($end<$start)
        {
            throw new \Exception('开始时间小于结束时间');
        }
        $batchNum   =   intval(($end-$start)/($this->batchTime*60));
        return $batchNum;
    }

    public function setEachBatchTime($examScreening,$batchNum){
        $start  =   strtotime($examScreening->begin_dt);
        $data   =   [];
        $nowTime    =   $start;
        $flowsIndex         =   $this->flowsIndex;

        $screeningStudents      =   $this->screeningStudents;
        if(empty($thisScreeningStudents))
        {
            $thisScreeningStudents  =[];
        }
        else
        {
            $thisScreeningStudents  =   $screeningStudents[$examScreening];
        }

        $batchStudents          =   [];
        for($i=1;$i<=$batchNum;$i++)
        {
            $thisBatchStudents      =   $this   ->  getPerBatchStudent();
            $batchStudents[$i]      =   $thisBatchStudents;
            foreach($flowsIndex as $flowList)
            {
                $first  =   array_shift($flowList);
                foreach($this   ->  stations as $station)
                {
                    $data[$i][$first->serialnumber][$station->id] =   $nowTime;
                }
                $nowTime+=$this->cellTime;
            }
        }
        $thisScreeningStudents[]=   $batchStudents;
        $this->screeningStudents=   $thisScreeningStudents;

        return $data;
    }

    public function getBatchTime(){
        $flowsIndex =   $this   ->  flowsIndex;
        $batchTime  =   count($flowsIndex)*$this->cellTime;
        $this   ->  batchTime   =   $batchTime;
        return $this;
    }
    /*
     * 获取报考学生
     */
    public function getExamStudent($exam){
        $data   =   [];
        foreach($exam   ->  students as $student)
        {
            $data[] =$student;
        }

        return  $data;
    }

    public function totalPrepare($time){
        return $time+config('osce.prepare',0);
    }

    public function getAllStation($exam){
        $flows  =   $this   ->  getExamFlow($exam);
        $flowsIndex         =   $this   ->  groupFlowByRoom($flows);
        $this   ->  flowsIndex  =   $flowsIndex;
        $examFlowRoomModel   =   new ExamFlowRoom();
        $data   =   [];
        foreach($flowsIndex as $flow)
        {
            foreach($examFlowRoomModel  ->  getRoomStationsByFlow($flow) as $station)
            {
                $data[$station->id] =   $station;
            }
        }
        return $data;
    }
    /*
     * 获取考试所有流程节点
     */
    public function getExamFlow($exam){
        return $exam    ->  flows;
    }
    /*
    *  根据考场分组流程
    */
    public function groupFlowByRoom($flows){
        $group                      =   [];
        foreach($flows as $flow)
        {
            $examFlowRoomRelation       =   $flow   ->  examFlowRoomRelation;
            if(is_null($examFlowRoomRelation->  serialnumber))
            {
                throw new \Exception('序号数据错误');
            }
            $group[$examFlowRoomRelation->  serialnumber][]=$examFlowRoomRelation;
        }

        ksort($group);
        return $group;
    }

    public function getMaxStationTime(){
        $mins   =   0;
        foreach($this   ->  stations as $station)
        {
            $mins   =   $mins>$station->mins? $mins:$station->mins;
        }
        $mins   =   $this   ->  totalPrepare($mins);
        $this   ->  cellTime    =   $mins;
        return $mins;
    }

    public function getStudentByNum($num){
        $allStudent =   $this   ->  allStudent;
        shuffle($allStudent);
        $data   =   [];
        for($i=0;$i<$num;$i++)
        {
            $student=   array_pop($allStudent);
            if(empty($student))
            {
                $student    =   new \stdClass();
                $student    ->  name    =   '空缺';
                $student    ->  id      =   0;
            }
            $data[] =   $student;
        }
        $this->allStudent   =   $allStudent;
        return $data;
    }

    public function makeGroupPlanByRoom($plan){
        $groupData  =   [];
        $list       =   $this->getStationRoomInfo();
        foreach($plan as $screeningId   =>  $screeningPlan){
            foreach($screeningPlan as $stationId=>$timeStudent)
            {
                foreach($timeStudent as $time=>$student){
                    $room   =   $list[$stationId];
                    if(is_null($room))
                    {
                        throw new   \Exception('没有找到考站'.$stationId.'的房间信息');
                    }
                    $groupData[$screeningId][$room->id][$time][$student->id]=$student;
                    $this->recordStudentTime($student,$time,$time+$this->cellTime*60,$this->stations[$stationId]);
                }
            }
        }
        return $this->groupPlanByTime($groupData);
    }

    public function groupPlanByTime($groupData){
        $data  = [];
        $roomList   =   $this->roomList;
        foreach($groupData as $screeningId   =>  $roomPlan){
            foreach($roomPlan as $roomId=>$timePlan)
            {
                $room   =   $roomList[$roomId];
                $roomdData  =   [
                    'name'  =>  $room->name,
                    'child' =>  []
                ];
                foreach($timePlan as $time=>$student)
                {
                    $item   =   [
                        'start' =>  $time,
                        'end'   =>  $time+$this->cellTime,
                        'items' =>  $student
                    ];
                    $roomdData['child'][]=$item;
                }
                $data[$screeningId][$roomId]=$roomdData;
            }
        }
        return $data;
    }
    public function getStationRoomInfo(){
        $stations   =   $this->stations;
        $data   =   [];
        foreach($stations as $station)
        {
            if(is_null($station->roomStation))
            {
                throw new \Exception('考站数据错误');
            }
            $room   =   $station->roomStation->room;
            $data[$station->id]         =   $room;
            $roomList[$room->id]        =   $room;
        }
        $this->roomList =   $roomList;
        return $data;
    }

    public function recordStudentTime($student,$start,$end,$station){
        $studentTimeRecord  =   $this->studentTimeRecord;
        $studentTimeRecord[$student->id][]    =   [
            'start'     =>  $start,
            'end'       =>  $end,
            'station'   =>  $station
        ];
        $this->studentTimeRecord    =   $studentTimeRecord;
        return $this;
    }


    public function changePerson($studentA,$studentB,$exam,$user){
        $plan       =   Cache::get('plan_'.$exam->id.'_'.$user->id);

        try{
            $studentAInfo   =   $this   ->  getStudentByChangeIndex($studentA,$plan);
            $studentBInfo   =   $this   ->  getStudentByChangeIndex($studentB,$plan);
            $studentARoom   =   $this   -> getRoomStudentByChangeIndex($studentA,$plan);
            $studentBRoom   =   $this   -> getRoomStudentByChangeIndex($studentB,$plan);

        }
        catch(\Exception $ex)
        {
            throw $ex;
        }
    }
    public function getStudentByChangeIndex($indexInfo,$plan){
        try{
            $student    =   $plan[$indexInfo['screening_id']][$indexInfo['room_id']]['child'][$indexInfo['batch_index']]['items'][$indexInfo['student_id']];
            return  $student;
        }
        catch(\Exception $ex)
        {
            throw new \Exception('没有找到该对应的学生安排');
        }
    }

    public function getRoomStudentByChangeIndex($indexInfo,$plan){
        try{
            $student    =   $plan[$indexInfo['screening_id']][$indexInfo['room_id']]['child'][$indexInfo['batch_index']];
            return  $student;
        }
        catch(\Exception $ex)
        {
            throw new \Exception('没有找到该对应的学生安排');
        }
    }
}