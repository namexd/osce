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

class ExamPlan extends CommonModel
{
    protected $connection	=	'osce_mis';
    protected $table 		= 	'exam_plan';
    public $incrementing	=	true;
    public $timestamps	    =	true;
    protected $fillable 	=	['exam_id','exam_screening_id','student_id','station_id','room_id','begin_dt','end_dt','status','created_user_id'];

    protected $stationNum;

    public function IntelligenceEaxmPlan($exam){
        $examScreeningTotal =   $this   ->  groupStudent($exam);
        $this->allStudent   =   $this   ->  getExamStudent($exam);
        $studentGroup   =[];
        foreach($examScreeningTotal as $screeningId =>  $studentNum)
        {
            $studentGroup[$screeningId]=$this   ->  getStudentByNum($studentNum);
        }
        dd($this->getStationCombination($exam));
    }

    public function groupStudent($exam){
        //计算单批次时间
        $oneTotal   =   $this   ->  oneFlowsTime($exam);
        $stationList   =   $this   ->  getBatchAllStation($exam);
        $examScreeningTotal  =   [];
        //计算批次数量
        foreach($exam   ->  examScreening as $screening)
        {
            $startTime  =   strtotime($screening ->  begin_dt);
            $endTime    =   strtotime($screening ->  end_dt);
            if($startTime==false||$endTime==false)
            {
                throw new \Exception('开始/结束时间格式不对');
            }
            if($endTime<$startTime)
            {
                throw new \Exception('考试的开始时间大于结束时间，请确认设置');
            }
            $totalTime  =   intval(($endTime-$startTime)/60);
            $batch      =   intval($totalTime/$oneTotal);
            $totalStudent   =   $batch  *   $this->stationNum;
            $examScreeningTotal[$screening->id]   = $totalStudent;
        }

        return $examScreeningTotal;
    }
    public function oneFlowsTime($exam){
        $flows  =   $this   ->  getExamFlow($exam);
        $flowsIndex         =   $this   ->  groupFlowByRoom($flows);
        $list         =   $this   ->  getStationCombination($exam);
        $total              =   0;
        $stationNum     =   0;
        foreach($flowsIndex as $group)
        {
            $longestTime    =   0;
            foreach($group as $examFlowRoom)
            {
                $time           =   $examFlowRoom->getRoomStaionTime($examFlowRoom);
                $stationNum     +=   $examFlowRoom->getRoomStationNum($examFlowRoom);
                $longestTime    =   $time>$longestTime? $time:$longestTime;
            }
            $groupTime  =ceil($longestTime/count($group));
            $groupTime    =   intval($this   ->  totalPrepare($groupTime));
            $total+=$groupTime;
        }
        $this   ->  stationNum  =   $stationNum;
        return $total;
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

    public function totalPrepare($time){
        return $time+config('osce.prepare',0);
    }

    public function getStudentByNum($num){
        $allStudent =   $this->allStudent;
        shuffle($allStudent);
        $data   =   [];
        for($i=0;$i<$num;$i++)
        {
            $data[] =array_pop($allStudent);
        }
        $this->allStudent   =   $allStudent;
        return $data;
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
    public function getBatchAllStation($exam){
        $examFlowRoomModel   =   new ExamFlowRoom();
        $list   =   $examFlowRoomModel  ->  where('room_id','=',$exam->id)->get();
        $stationsList   =[];
        foreach($list as $roomRelation)
        {
            $room   =   $roomRelation->room;
            $station    =   [];
            if(is_null($room))
            {
                throw new \Exception('房间不存在');
            }
            else
            {
                $stations    =   $room->stations;
                foreach($stations as $station)
                {
                    $stationsList[]     =   $station;
                }
            }
        }
        return $stationsList;
    }

    public function getStationCombination($exam){
        $flows  =   $this   ->  getExamFlow($exam);
        $flowsIndex         =   $this   ->  groupFlowByRoom($flows);

    }
    public function makeCombination($flowsIndex){

    }

}