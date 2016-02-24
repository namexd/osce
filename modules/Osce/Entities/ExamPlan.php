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
use DB;


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
    protected $screeningStudents    =   [];
    protected $stationStudent       =   [];


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
        $this   ->  stations   =   $this   ->  getAllStation($exam);
        $this   ->  allStudent =   $this   ->  getExamStudent($exam);
        $mins   =   $this   ->  getMaxStationTime();
        $this   ->  getBatchTime();
        $examScreenings =   $exam   ->  examScreening;
        $timeList       =   $this   ->  timeList;
        foreach($examScreenings as $examScreening)
        {
            $batchNum   =  $this    ->  getBatchNum($examScreening);
            $screeningTimeList      =   $this   ->  setEachBatchTime($examScreening,$batchNum);
            $timeList[$examScreening->id]   =   $screeningTimeList;
        }
        $plan   =   $this->distribute($timeList);
        //dd($timeList);
        $this   ->  timeList    =   $timeList;
        $groupData  =   $this->makeGroupPlanByRoom($plan);
        return  $groupData;
    }

    public function distribute($timeList){
        $screeningStudents  =   $this   ->  screeningStudents;
        //$flowsIndex         =   $this->flowsIndex;
        //$examScreeningIndex =   0;

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
                        $batchStudnet   =   $screeningStudents[$examScreeningId][$batchId];
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
            //$examScreeningIndex++;
        }
        return $plan;
    }

    public function changeStudentIndex($batchStudnet){
        $data   =   [];
        $total  =   count($batchStudnet);
        foreach($batchStudnet as $index=>$student)
        {
            $newIndex           =   $index+1;
            if($newIndex        >=  $total)
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
        $nowTime=   $start;
        $flowsIndex         =   $this->flowsIndex;

        $screeningStudents      =   $this->screeningStudents;
//        if(!array_key_exists($examScreening->id,$screeningStudents))
//        {
//            $thisScreeningStudents  =   [];
//        }
//        else
//        {
//            $thisScreeningStudents  =   $screeningStudents[$examScreening->id];
//        }

        $batchStudents          =   [];
        if($batchNum==0)
        {
            throw new \Exception();
        }
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
                $nowTime+=$this->cellTime*60;
            }
        }
        //dd($batchStudents);
        //$thisScreeningStudents[]                =   $batchStudents;
        $screeningStudents[$examScreening->id]  =   $batchStudents;
        $this->screeningStudents=   $screeningStudents;

        return $data;
    }

    public function getBatchTime(){
        $flowsIndex =   $this   ->  flowsIndex;
        $batchTime  =   count($flowsIndex)*$this->cellTime;
        $this   ->  batchTime   =   $batchTime;
        return $this;
    }
    /**
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
        $data   =   [];

        if($exam    ->  sequence_mode===1)
        {
            $flowsIndex         =   $this   ->  groupFlowByRoom($flows);
            $examFlowRoomModel   =   new ExamFlowRoom();
            foreach($flowsIndex as $flow)
            {
                foreach($examFlowRoomModel  ->  getRoomStationsByFlow($flow) as $station)
                {
                    $data[$station->id] =   $station;
                }
            }
        }
        else
        {
            $examFlowStationModel   =   new ExamFlowStation();
            $flowsIndex             =   $this   ->  groupFlowByStation($flows);
            foreach($flowsIndex as $flowGroup)
            {
                foreach($flowGroup as $flow)
                {
                    $station            =   $flow->station;
                    $data[$station->id] =   $station;
                }
            }
        }
        $this   ->  flowsIndex  =   $flowsIndex;
        return $data;
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
        $group                      =   [];
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

    public function groupFlowByStation($flows){
        $group                      =   [];
        foreach($flows as $examFlow)
        {
            $examFlowStationRelation       =   $examFlow   ->  examFlowStationRelation;
            if(is_null($examFlowStationRelation->  serialnumber))
            {
                throw new \Exception('序号数据错误');
            }
            $group[$examFlowStationRelation->  serialnumber][]=$examFlowStationRelation;
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
        $groupData      =   [];
        $stationStudent =   [];
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
                    $groupData[$screeningId][$room->id.'-'.$stationId][$time][$student->id]=   $student;
                    $stationStudent[$student->id][]                         =   $stationId;
                    $this->recordStudentTime($student,$time,$time+$this->cellTime*60,$this->stations[$stationId]);
                }
            }
        }
        //$this   ->  stationStudent   =   $stationStudent;
        return $this->groupPlanByTime($groupData);
    }

    public function groupPlanByTime($groupData){
        $data  = [];
        $roomList   =   $this->roomList;
        foreach($groupData as $screeningId   =>  $roomPlan){
            foreach($roomPlan as $roomId=>$timePlan)
            {
                $roomIdInfo =   explode('-',$roomId);
                $room   =   $roomList[array_shift($roomIdInfo)];
                $roomdData  =   [
                    'name'  =>  $room->name.'-'.$this->stations[array_shift($roomIdInfo)]->name,
                    'child' =>  []
                ];
                $end    =   0;
                foreach($timePlan as $time=>$student)
                {
                    if($end!=0)
                    {
                        $perEnd =   $end;
                        if($time<=$perEnd)
                        {
                            $roomdData['child'][]=[
                                'start'     =>  $perEnd,
                                'end'       =>  $time,
                                'screening' =>  $screeningId,
                                'items'     =>  []
                            ];
                        }
                    }
                    $end    =   $time+$this->cellTime*60;
                    $item   =   [
                        'start'     =>  $time,
                        'end'       =>  $end,
                        'screening' =>  $screeningId,
                        'items'     =>  $student
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
        $plan                   =   Cache::get('plan_'.$exam->id.'_'.$user->id);

        try{
            $plan   =   $this->changeStudent($studentA,$studentB,$plan);
            if($exam->sequence_mode==1)
            {

            }
            else
            {
                $this   -> plan_station_student;
                $studentTimePlan    =   $this   ->  getTimeListFromPlan($plan);
                $stationStudent     =   $this   ->  stationStudent;

                $redStudentForTime  =   $this->checkStudentTime($studentTimePlan);
                $redStudentForSation=   $this->checkStudentStation($stationStudent);

                $redMan =   array_merge($redStudentForTime,$redStudentForSation);
                Cache::pull('plan_'.$exam->id.'_'.$user->id);
                return $redMan;
            }
        }
        catch(\Exception $ex)
        {
            throw $ex;
        }
    }
    public function getStudentByChangeIndex($indexInfo,$plan){
        try{
            if(array_key_exists('station_id',$indexInfo))
            {
                $student    =   $plan[$indexInfo['screening_id']][$indexInfo['room_id'].'-'.$indexInfo['station_id']]['child'][$indexInfo['batch_index']]['items'][$indexInfo['student_id']];
            }
            else
            {
                $student    =   $plan[$indexInfo['screening_id']][$indexInfo['room_id']]['child'][$indexInfo['batch_index']]['items'][$indexInfo['student_id']];
            }
            return  $student;
        }
        catch(\Exception $ex)
        {
            throw new \Exception('没有找到该对应的学生安排');
        }
    }

    public function getRoomStudentByChangeIndex($indexInfo,$plan){
        try{
            if(array_key_exists('station_id',$indexInfo))
            {
                $student    =   $plan[$indexInfo['screening_id']][$indexInfo['room_id'].'-'.$indexInfo['station_id']]['child'][$indexInfo['batch_index']];
            }
            else
            {
                $student    =   $plan[$indexInfo['screening_id']][$indexInfo['room_id']]['child'][$indexInfo['batch_index']];
            }
            return  $student;
        }
        catch(\Exception $ex)
        {
            throw new \Exception('没有找到该对应的学生安排');
        }
    }

    public function getTimeListFromPlan($plan){
        $students       =   [];
        $studentStation =   [];
        foreach($plan as $examScreening => $roomList)
        {
            foreach($roomList as $roomStationId=>$room)
            {
                $roomStationInfo    =   explode('-',$roomStationId);
                //dd($roomStationInfo);
                foreach($room['child'] as $timeList)
                {
                    foreach($timeList['items'] as $student)
                    {
                        $students[$student->id][$timeList['start']+1] =   1;
                        $students[$student->id][$timeList['end']-1]   =   0;
                        $studentStation[$student->id][]=$roomStationInfo[1];
                    }
                }
            }
        }
        $this   ->  stationStudent  =   $studentStation;
        return $students;
    }

    public function getTimeList(){
        return $this->timeList;
    }

    public function getStationStudent(){
        return $this->stationStudent;
    }

    public function checkStudentTime($studentTimePlan){
        $redStudent =   [];
        foreach($studentTimePlan as $studentTime){
            $preStatus  =   0;
            foreach($studentTime as $studentId=>$status){
                if($preStatus==$status)
                {
                    $redStudent[]=$studentId;
                }
                else
                {
                    $preStatus=$status;
                }
            }
        }
        return $redStudent;
    }
    public function checkStudentStation($stationStudent){
        $aginStudent    =   [];
        unset($stationStudent[0]);
        foreach($stationStudent as $studentId =>   $studentPlan)
        {
            if(count($studentPlan)==count(array_unique($studentPlan)))
            {
                $aginStudent[]= $studentId;
            }
        }
        return $aginStudent;
    }

    public function changeStudent($studentA,$studentB,$plan){
        $studentAInfo   =   $this   ->  getStudentByChangeIndex($studentA,$plan);
        $studentBInfo   =   $this   ->  getStudentByChangeIndex($studentB,$plan);
        if(array_key_exists('station_id',$studentA))
        {
            //dd($studentBInfo);
            $studentAOldList    =   $plan[$studentA['screening_id']][$studentA['room_id'].'-'.$studentA['station_id']]['child'][$studentA['batch_index']]['items'];
            $studentBOldList    =   $plan[$studentB['screening_id']][$studentB['room_id'].'-'.$studentB['station_id']]['child'][$studentB['batch_index']]['items'];
            if(array_key_exists($studentBInfo->id,$studentAOldList))
            {
                $plan[$studentA['screening_id']][$studentA['room_id'].'-'.$studentA['station_id']]['child'][$studentA['batch_index']]['items'][$studentBInfo->id]=false;
                $plan[$studentA['screening_id']][$studentA['room_id'].'-'.$studentA['station_id']]['child'][$studentA['batch_index']]['items'][$studentAInfo->id]=false;
            }
            else
            {
                unset($plan[$studentA['screening_id']][$studentA['room_id'].'-'.$studentA['station_id']]['child'][$studentA['batch_index']]['items'][$studentAInfo->id]);
                $plan[$studentA['screening_id']][$studentA['room_id'].'-'.$studentA['station_id']]['child'][$studentA['batch_index']]['items'][$studentBInfo->id]=$studentBInfo;
            }
        }
        else
        {
            //$student    =   $plan[$indexInfo['screening_id']][$indexInfo['room_id']]['child'][$indexInfo['batch_index']]['items'][$indexInfo['student_id']];
        }
        return $plan;
    }

    public function savePlan($exam_id,$plan){
        $user=\Auth::user();
        $hasList    =   [];

        foreach($plan as $examScreening => $roomList)
        {
            foreach($roomList as $roomStationId=>$room)
            {
                $roomStationInfo    =   explode('-',$roomStationId);
                //dd($room['child']);
                foreach($room['child'] as $timeList)
                {
                    foreach($timeList['items'] as $student)
                    {
                        if($student->id)
                        {
                            if(array_key_exists(1,$roomStationInfo))
                            {
                                if(array_key_exists($student->id,$hasList))
                                {
                                    if(in_array(intval($roomStationInfo[1]),$hasList[$student->id]))
                                    {
                                        continue;
                                    }
                                }
                                $data[]=[
                                    'exam_id'           =>  $exam_id,
                                    'exam_screening_id' =>  $examScreening,
                                    'student_id'        =>  $student->id,
                                    'station_id'        =>  intval($roomStationInfo[1]),
                                    'room_id'           =>  intval($roomStationInfo[0]),
                                    'begin_dt'          =>  date('Y-m-d H:i:s',$timeList['start']),
                                    'end_dt'            =>  date('Y-m-d H:i:s',$timeList['end']),
                                    'status'            =>  1,
                                    'created_user_id'   =>  $user->id,
                                ];
                                $hasList[$student->id][]=$roomStationInfo[1];
                            }
                            else
                            {
                                $data[]=[
                                    'exam_id'           =>  $exam_id,
                                    'exam_screening_id' =>  $examScreening,
                                    'student_id'        =>  $student->id,
                                    'room_id'           =>  intval($roomStationInfo[0]),
                                    'begin_dt'          =>  date('Y-m-d H:i:s',$timeList['start']),
                                    'end_dt'            =>  date('Y-m-d H:i:s',$timeList['end']),
                                    'status'            =>  1,
                                    'created_user_id'   =>  $user->id,
                                ];
                            }
                        }
                    }
                }
            }
        }

        $connection =   \DB::connection($this->connection);
        $connection ->  beginTransaction();
        try{
            $oldPlanList    =   $this   -> getOldPlanByExamId($exam_id);
            $exam       =   Exam::find($exam_id);
            if($exam->status!=0)
            {
                throw new \Exception('当前考试已在不在未开始状态，不能再次编辑排考信息');
            }
            foreach($oldPlanList as $oldPlan)
            {
                if(!$oldPlan    ->  delete())
                {
                    throw new \Exception('弃用旧计划失败');
                }
            }
            foreach($data as $item)
            {
                if(!$this->create($item))
                {
                    throw new \Exception('保存考试计划失败');
                }
            }
            $this  ->  saveStudentOrder($exam_id);
            $connection ->  commit();
            return true;
        }
        catch(\Exception $ex)
        {
            $connection ->  rollBack();
            throw $ex;
        }
    }

    public function getOldPlanByExamId($exam_id){
        return $this->where('exam_id','=',$exam_id)->get();
    }

    public function showPlan($exam){
        $list   =   $this->where('exam_id','=',$exam->id)->get();
//        $user   =   \Auth::user();
//        $plan   =   Cache::get('plan_'.$exam->id.'_'.$user->id);
//        dd($plan);
        $screeningData   =   [];
        $roomStationData   =   [];
        $roomStationInfoData   =   [];
        $roomStationBatchData   =   [];
        $roomStationItemData   =   [];
        $roomTimeGroup  =   [];

        foreach($list as $item)
        {
            $screeningData[$item->exam_screening_id][] =  $item;
            $roomTimeGroup[$item->exam_screening_id][$item->room_id][$item->begin_dt][]=   $item;
        }
        foreach($screeningData as $screeningId=>$examPlanList)
        {
            foreach($examPlanList as $examPlan)
            {
                $roomStationData[$screeningId][$examPlan->room_id.'-'.$examPlan->station_id]=$examPlan;
            }
        }

        foreach($roomStationData as $screeningId=>$examPlanList)
        {
            foreach($examPlanList as $examPlan)
            {
                if(is_null($examPlan->station_id))
                {
                    $roomStationInfoData[$screeningId][$examPlan->room_id]=[
                        'name'  =>  $examPlan->room->name,
                        'child' =>  []
                    ];
                }
                else
                {
                    $roomStationInfoData[$screeningId][$examPlan->room_id.'-'.$examPlan->station_id]=[
                        'name'  =>  $examPlan->room->name.'-'.$examPlan->room->station,
                        'child' =>  []
                    ];
                }

            }
        }

        foreach($roomStationInfoData as $screeningId=>$examPlanList)
        {
            foreach($examPlanList as $roomStaionId=>$examPlan)
            {
                $roomStaionInfo =   explode('-',$roomStaionId);
                $roomTime    =  $roomTimeGroup[$screeningId][$roomStaionInfo[0]];
                $items   =   [];
                foreach($roomTime as $timeInfo){
                    foreach($timeInfo as $item)
                    {
                        if(array_key_exists(1,$roomStaionInfo))
                        {
                            if($item['station_id']==$roomStaionInfo[1])
                            {
                                $items[]=$item;
                            }
                        }
                        else
                        {
                            $items[]=$item;
                        }
                    }
                }
                if(array_key_exists(1,$roomStaionInfo))
                {
                    $roomStationBatchData[$screeningId][$roomStaionInfo[0].'-'.$roomStaionInfo[1]]['child']=$items;
                }
                else
                {
                    $roomStationBatchData[$screeningId][$roomStaionInfo[0]]['child']=$items;
                }
            }
        }

        foreach($roomStationBatchData as $screeningId=>$examPlanList)
        {
            foreach($examPlanList as $roomStaionId=>$examPlan)
            {
                $roomStaionInfo =   explode('-',$roomStaionId);
                foreach($examPlan['child'] as  $bacthIndex=>$examPlan)
                {
                    if(array_key_exists(1,$roomStaionInfo))
                    {
                        $roomStationItemData[$screeningId][$roomStaionInfo[0].'-'.$roomStaionInfo[1]]['child'][$bacthIndex]  = $examPlan;
                    }
                    else
                    {
                        $roomStationItemData[$screeningId][$roomStaionInfo[0]]['child'][$bacthIndex]  = $examPlan;
                    }
                }
            }
        }
        $examPlanData=  [];
        foreach($roomStationItemData as $screeningId=>$examPlanList)
        {
            foreach($examPlanList as $roomStaionId=>$examPlan)
            {
                //dd($examPlanList);
                $roomStaionInfo =   explode('-',$roomStaionId);
                foreach($examPlan['child'] as $bacthIndex=>$examPlan)
                {
                    if(array_key_exists(1,$roomStaionInfo))
                    {
                        $examPlanData
                        [$screeningId]
                        [$roomStaionInfo[0].'-'.$roomStaionInfo[1]]
                        ['name']   =   $examPlan->room->name.'-'.$examPlan->station->name;


                        $examPlanData
                        [$screeningId]
                        [$roomStaionInfo[0].'-'.$roomStaionInfo[1]]
                        ['child'][$bacthIndex]
                        ['start'] =  strtotime($examPlan->begin_dt);

                        $examPlanData
                        [$screeningId]
                        [$roomStaionInfo[0].'-'.$roomStaionInfo[1]]
                        ['child'][$bacthIndex]
                        ['screening'] =  $screeningId;

                        $examPlanData
                        [$screeningId]
                        [$roomStaionInfo[0].'-'.$roomStaionInfo[1]]
                        ['child'][$bacthIndex]
                        ['end'] =  strtotime($examPlan->end_dt);


                        $examPlanData
                        [$screeningId]
                        [$roomStaionInfo[0].'-'.$roomStaionInfo[1]]
                        ['child'][$bacthIndex]
                        ['items'][] =   $examPlan->student;
                    }
                    else
                    {
                        $examPlanData
                        [$screeningId]
                        [$roomStaionInfo[0]]
                        ['name']   =   $examPlan->room->name;
                        $examPlanData
                        [$screeningId]
                        [$roomStaionInfo[0]]
                        ['child'][$bacthIndex]
                        ['start'] =  strtotime($examPlan->begin_dt);
                        $examPlanData
                        [$screeningId]
                        [$roomStaionInfo[0]]
                        ['child'][$bacthIndex]
                        ['end'] =  strtotime($examPlan->end_dt);
                        $examPlanData
                        [$screeningId]
                        [$roomStaionInfo[0]]
                        ['child'][$bacthIndex]
                        ['items'][] =   $examPlan->student;
                        $examPlanData
                        [$screeningId]
                        [$roomStaionInfo[0].'-'.$roomStaionInfo[1]]
                        ['child'][$bacthIndex]
                        ['screening'] =  $screeningId;
                    }
                }
            }
        }
        return $examPlanData;
    }

    /**
     * 保存学生考试顺序
     * @access public
     *
     * @param $exam_id int  考试ID
     *
     * @return void
     *
     * @version 0.3
     * @author Luohaihua <Luohaihua@misrobot.com>
     * @date 2016-01-29 13:36
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     *
     */
    public function saveStudentOrder($exam_id){
        $planList   =   $this->where('exam_id','=',$exam_id)->orderBy('begin_dt','asc')->get();
        $studentOrderData   =   [];
        $user   =   \Auth::user();
        if(ExamOrder::where('exam_id','=',$exam_id)->delete()===false)
        {
            throw new \Exception('弃用旧安排失败');
        }
        try
        {
            foreach($planList as $plan)
            {
                if(!array_key_exists($plan->student_id,$studentOrderData))
                {
                    $studentOrderData[$plan->student_id] =   [
                        'exam_id'           =>  $exam_id,
                        'exam_screening_id' =>  $plan->exam_screening_id,
                        'student_id'        =>  $plan->student_id,
                        'begin_dt'          =>  $plan->begin_dt,
                        'status'            =>  0,
                        'created_user_id'   =>  $user->id,
                    ];
                }
            }
            foreach($studentOrderData as $stduentOrder){
                if(!ExamOrder::create($stduentOrder))
                {
                    throw new \Exception('保存学生考试顺序失败');
                }
            }
        }
        catch(\Exception $ex)
        {
            throw $ex;
        }
    }

    public function getexampianStudent($ExamScreeningId){
        return $this->where('exam_screening_id','=',$ExamScreeningId)
//                    ->groupBy('student_id')
                    ->count();


   }

    public function storePlan($examId,$user)
    {
        $connection = DB::connection($this->connection);
        $connection->beginTransaction();
        try {
            //通过id找到对应的数据
            $data = ExamPlanRecord::where('exam_id',$examId)->get();

            foreach ($data as $item) {
                $array = [
                    'exam_id' => $examId,
                    'exam_screening_id' => $item->exam_screening_id,
                    'student_id' => $item->student_id,
                    'station_id' => $item->station_id,
                    'room_id' => $item->room_id,
                    'begin_dt' => $item->begin_dt,
                    'end_dt' => $item->end_dt,
                    'status' => 0,
                    'created_user_id' => $user->id,
                ];
                if (!$a = ExamPlan::create($array)) {
                    throw new \Exception('保存失败！');
                }
            }

            $this->saveStudentOrder($examId);
            $connection->commit();
        } catch (\Exception $ex) {
            $connection->rollBack();
            throw $ex;
        }
    }
}