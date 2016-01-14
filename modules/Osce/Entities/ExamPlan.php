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
    protected $roomList     =   [];
    protected $allStudent   =   [];
    protected $allBlock     =   [];
    protected $studentGroup =   [];
    protected $batch        =   0;
    protected $flowsStudent =   [];
    protected $roomSerialnumber =   [];
    protected $flowTime     =   [];
    protected $roomTime     =   [];
    protected $startTime    =   [];
    protected $flowRoomNum  =   [];
    protected $screeningId  =   0;
    protected $plan         =   [];
    protected $studentBusyTime  =   [];

    //流程组 组内 考场优先级
    protected $flowGroupInnerPriority   =   [];

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
        $this   ->  initStartTime($exam);
        $this->allStudent   =   $this   ->  getExamStudent($exam);
        $this->roomList     =   $this   ->  getRoomList($exam);
        $examScreeningTotal =   $this   ->  groupStudent($exam);

        $this   ->  getAllBlock($exam);
        //$studentGroup       =[];

        /**
         * $examScreeningTotal[
         *      '场次ID'=>[
         *          '批次序号'=>[{学生}]
         *      ]
         * ]
         *
         * $allBlock[
         *      '场次ID'=>[
         *          '批次序号'=>[
         *              ‘考场ID’=>[],
         *              ‘考场ID’=>[],
         *              ‘考场ID’=>[],
         *          ]
         *      ]
         * ]
         */
        $plan   =[];
        $roomData   =   [];
        foreach($this->allBlock as $screeningId =>  $screeningBlock)
        {
            $this   ->screeningId   =   $screeningId;
            $screeningPlan  =[];
            foreach($screeningBlock as $batch=>$batchBlock)
            {
                $batchPlan  =[];
                foreach($batchBlock as $roomId=>$roomBlock)
                {
                    $this   ->  setRoomTime($roomId);
                    $student    =   [];
                    if(!empty($examScreeningTotal[$screeningId][$batch]))
                    {
                        $student    =   $this->putStudentToRoom($examScreeningTotal[$screeningId][$batch],$this->getRoomFlowSerialnumber($roomId),$roomId);
                        $batchPlan[$roomId] =   $student;
                    }
                    else
                    {
                        $batchPlan[$roomId] =   [];
                    }

                    $beginTime  =   $this->getRoomTime($roomId);
                    $endTime    =   $this->getRoomTime($roomId) +   $this   ->  getFlowTimeByRoomId($roomId);

                    $batchInfo =[
                        'begin'     =>  $beginTime,
                        'end'       =>  $endTime,
                        'items'     =>  $student,
                    ];
                    $this   ->  setStudentsBusyTime($student,$beginTime,$endTime,$screeningId.'-'.$roomId.'-'.$batch);
                    $roomData[$screeningId][$roomId]['name']             =   $this->getRoomName($roomId);
                    $roomData[$screeningId][$roomId]['child'][$batch]    =   $batchInfo;
                    //$roomData[$screeningId][$roomId][$batch]    =     $batch;
                }
                $screeningPlan[$batch]=$batchPlan;
            }
            $plan[$screeningId]=$screeningPlan;
        }
        $this   ->  plan    =$plan;
        dd($this->studentBusyTime);
        return $roomData;
    }

    /*
     * 初始化 开始时间
     */
    public function initStartTime($exam){
        $data   =   [];
        foreach($exam->examScreening as $examScreening)
        {
            $data[$examScreening->id] = strtotime($examScreening->begin_dt);
        }
        $this   ->  startTime   =   $data;
    }
    /*
     * 学生分场次
     */
    public function groupStudent($exam){
        //计算单批次时间
        $oneTotal       =   $this   ->  oneFlowsTime($exam);
        $stationList    =   $this   ->  getBatchAllStation($exam);
        $stationNum     =   count($stationList);
        $examScreeningTotal     =   [];
        //计算批次数量
        $batchList              =   [];
        $studentGroup           =   [];
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
            $batchList[$screening->id]  =   $batch;


            $batchStudent   =[];
            for($i=1;$i<=$batch;$i++)
            {
                try
                {
                    $batchStudent[$i]    =   $this->getStudentByNum($stationNum);
                }
                catch(\Exception $ex)
                {
                    $batchStudent[$i]   =   [];
                }
            }
            $studentGroup[$screening->id]   =   $batchStudent;
        }
        $this       ->  batch   =   $batchList;
        return $studentGroup;
    }

    public function getAllBlock($exam){
        foreach($exam   ->  examScreening as $screening)
        {
            $this       ->  makeBlock($screening->id);
        }
        return $this;
    }
    public function makeBlock($screeningId){
        $batch      =   $this   ->  batch[$screeningId];
        $allBlock  =   $this    ->  allBlock;
        $thisScreening  =   $batch[$screeningId];
        $roomList  =   $this    ->  roomList;
        for($i=1;$i<=$batch;$i++)
        {
            $item=  [];
            foreach($roomList as $room)
            {
                $item[$room->id][]   =   [];
            }
            $thisScreening[$i]=$item;
        }
        $allBlock[$screeningId] =   $thisScreening;
        $this   ->  allBlock    =   $allBlock;
        return  $allBlock;
    }

    /*
     * 获取单一流程时间
     */
    public function oneFlowsTime($exam){
        $flows  =   $this   ->  getExamFlow($exam);
        $flowsIndex         =   $this   ->  groupFlowByRoom($flows);
        $total              =   0;
        $stationNum     =   0;
        $flowTime       =   [];

        foreach($flowsIndex as $ser=>$group)
        {
            $longestTime    =   0;
            foreach($group as $examFlowRoom)
            {
                $this           ->  initFlowGroupInnerPriority($examFlowRoom);
                $this           ->  initFlowRoomNum($examFlowRoom);
                $time           =   $examFlowRoom   ->  getRoomStaionTime($examFlowRoom);
                $stationNum     +=  $examFlowRoom   ->  getRoomStationNum($examFlowRoom);
                $longestTime    =   $time>$longestTime? $time:$longestTime;
            }
            $groupTime  =   ceil($longestTime/count($group));
            $groupTime  =   intval($this   ->  totalPrepare($groupTime));

            //getRoomFlowSerialnumber
            $flowTime[$ser] =  $groupTime;

            $total+=$groupTime;
        }
        $this->flowTime     =   $flowTime;
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

    /*
     * 计算 叠加准备时间
     */
    public function totalPrepare($time){
        return $time+config('osce.prepare',0);
    }

    /*
     * 获取指定数量的考生
     */
    public function getStudentByNum($num,$screeningId=false){
        if(!$screeningId)
        {
            $allStudent =   $this   ->  allStudent;
        }
        else
        {
            $allStudent =   $this   ->  studentGroup[$screeningId];
        }

        if(empty($allStudent))
        {
            throw new \Exception('所有用户已经选择');
        }
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

    /*
     * 给考生分批次
     */
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

    /*
     * 获取考场清单
     */
    public function getRoomList($exam){
        $data   =   [];
        $examRoomModel  =   new ExamRoom();
        $roomSerialnumber   =   [];
        foreach($examRoomModel   ->  getExamRoomData($exam->id) as $roomFxam)
        {
            $roomSerialnumber[$roomFxam->room_id]=$roomFxam->serialnumber;
            $data[] =   $roomFxam->room;
        }
        $this->roomSerialnumber    =   $roomSerialnumber;
        return $data;
    }

    /*
     * 给指定流程的指定房间分配学生
     */
    public function putStudentToRoom($studentList,$flowId,$roomId){
        //检查是否有同流程考场（选考）
        $flowRoomNum    =   $this   ->  flowRoomNum($flowId);
        if($flowRoomNum>1)
        {
            //同流程考场人员优先级检查
            if(!$this   -> flowGroupInnerPriorityCheck($roomId,$flowId))
            {
                return [];
            }
        }

        $flowsStudent   =   $this->flowsStudent;
        if(array_key_exists($flowId,$flowsStudent))
        {
            $flowStudents=   $flowsStudent[$flowId];
            if(count(array_intersect($studentList,$flowStudents))>0)
            {
                return [];
            }
            foreach($studentList as $student)
            {
                $flowStudents[]=$student;
            }
        }
        else
        {
            $flowStudents=   $studentList;
        }
        $flowsStudent[$flowId]  =   $flowStudents;
        $this   ->  flowsStudent=   $flowsStudent;
        if($flowRoomNum>1)
        {
            $this   ->  setFlowGroupInnerPriority($roomId,$flowId);
        }
        return  $studentList;
    }

    /*
     * 根据房间号获取流程步骤（暂不支持 多选1以上）
     */
    public function getRoomFlowSerialnumber($roomId){
        $roomSerialnumber   =   $this->roomSerialnumber;
        return $roomSerialnumber[$roomId];
    }

    public function setRoomTime($roomId){
        $roomTime   =   $this   ->  roomTime;
        if(array_key_exists($roomId,$roomTime))
        {
            $roomTime[$roomId]  +=$this->getFlowTimeByRoomId($roomId);
        }
        else
        {
            $startTime          =   $this->startTime;
            $ser                =   $this   ->screeningId;
            $roomTime[$roomId]  =   $startTime[$ser];
        }
        $this   ->  roomTime    =   $roomTime;
        return $this;
    }

    /*
     * 根据考场ID获取考场关键时间
     */
    public function getRoomTime($roomId){
        $roomTime   =   $this   ->  roomTime;
        $flowTime   =   $this   ->  flowTime;
        if(empty($roomTime[$this->getRoomFlowSerialnumber($roomId)]))
        {
            $startTime          =   $this   ->  startTime;
            $ser                =   $this   ->  screeningId;
            return  $startTime[$ser];
        }
        else
        {
            $time   =   $roomTime[$roomId]+$flowTime[$this->getRoomFlowSerialnumber($roomId)];
            return  strval($time);
        }
    }

    /*
     * 获取房间所在流程的最大时间
     */
    public function getFlowTimeByRoomId($roomId){
        $flowTime   =   $this   ->  flowTime;
        return  $flowTime[$this->getRoomFlowSerialnumber($roomId)]*60;
    }

    /*
     * 流程组 内的 优先级检查
     */
    public function flowGroupInnerPriorityCheck($roomId,$flowId){
        $flowGroupInnerPrioritys    =   $this->flowGroupInnerPriority;
        if(!array_key_exists($flowId,$flowGroupInnerPrioritys))
        {
            $flowGroupInnerPriority[$roomId]    =   false;
            $flowGroupInnerPrioritys[$flowId]   =   $flowGroupInnerPriority;
        }
        $flowGroupInnerPriority     =   $flowGroupInnerPrioritys[$flowId];
        if(!array_key_exists($roomId,$flowGroupInnerPriority))
        {
            $flowGroupInnerPriority[$roomId]    =   false;
            $flowGroupInnerPrioritys[$flowId]   =   $flowGroupInnerPriority;
        }
        return $flowGroupInnerPriority[$roomId];
    }

    /*
     * 设置组内优先级
     */
    public function setFlowGroupInnerPriority($roomId,$flowId){
        $flowGroupInnerPrioritys    =   $this->flowGroupInnerPriority;
        $flowGroupInnerPrioritys[$flowId][$roomId]  =   false;
    }

    /*
     * 初始化组内优先级
     */
    public function initFlowGroupInnerPriority($examFlowRoom){
        $flowGroupInnerPriority =   $this->flowGroupInnerPriority;
        $flowGroupInnerPriority[$examFlowRoom->flow_id][$examFlowRoom->room_id] =   true;
        $this   ->  flowGroupInnerPriority  =   $flowGroupInnerPriority;
        return $this;
    }

    /*
     * 流程所在房间数量
     */
    public function flowRoomNum($flowId){
        return  $this   -> flowRoomNum[$flowId];
    }

    /*
     * 初始化流程房间数
     */
    public function initFlowRoomNum($examFlowRoom){
        $examFlowRoomModel   =   new ExamFlowRoom();
        $num    =   $examFlowRoomModel   ->  where('flow_id','=',$examFlowRoom->flow_id)->count();
        $flowRoomNum    =   $this   ->  flowRoomNum;
        $flowRoomNum[$examFlowRoom->flow_id]   =   $num;
        $this   ->  flowRoomNum =   $flowRoomNum;
        return $this;
    }

    /*
     * 获取房间名称
     */

    public function getRoomName($roomId){
        $roomList   =   $this->roomList;
        foreach($roomList as $room)
        {
            if($roomId==$room->id)
            {
                return  $room->name;
            }
        }
        return  '';
    }

    public function setStudentBusyTime($studentId,$start,$end,$localtion){
        $studentBusyTime    =   $this->studentBusyTime;
        $studentBusyTime[$studentId][$localtion]    =   [
            'start' =>  $start,
            'end'   =>  $end
        ];
        $this   ->  studentBusyTime =   $studentBusyTime;
        return $this;
    }

    public function setStudentsBusyTime($studentList,$start,$end,$localtion){
        foreach($studentList as $student)
        {
            $this   ->  setStudentBusyTime($student->id,$start,$end,$localtion);
        }
    }
}