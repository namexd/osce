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

    protected $stations     =   [];
    protected $cellTime     =   0;
    protected $batchTime    =   0;
    protected $flowsIndex   =   [];
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
        $mins   =   $this   ->  getMaxStationTime();
        $this   ->  getBatchTime();
        $examScreenings  =   $exam   ->  examScreening;
        foreach($examScreenings as $examScreening)
        {
            $batchNum    =$this   ->  getBatchNum($examScreening);
            $this   ->  setEachBatchTime($examScreening,$batchNum);
        }
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
        for($i=1;$i<=$batchNum;$i++)
        {
            foreach($this   ->  stations as $station)
            {
                $data[$i][$station->id] =   $nowTime;
            }
            $nowTime+=$this->cellTime;
        }
        dd($data);
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
}