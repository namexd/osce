<?php
/**
 * Created by PhpStorm.
 * User: j5110
 * Date: 2016/2/16
 * Time: 14:02
 */

namespace Modules\Osce\Entities\AutomaticPlanArrangement;


use Modules\Osce\Entities\Exam;
use Modules\Osce\Entities\ExamFlowRoom;
use Modules\Osce\Entities\ExamFlowStation;
use Modules\Osce\Entities\ExamPlanRecord;
use Modules\Osce\Entities\StationTeacher;

class ExamPlaceEntity implements ExamPlaceEntityInterface
{
    /*
     * 考试实体的开关门状态
     * false为关门，true为开门
     */
    static public $status = true;

    //进入 准备工作 倒计时
    function prepareTest()
    {

    }

    //开始考试
    //进入 考试 倒计时
    //1个考站或考场一个队列
    //方法内需触发考试结束事件（结束时间，考试人）
    function beginTest()
    {

    }

    //考试结束
    //关闭考试，设置考站或考场为空闲
    function endTest()
    {

    }

    //获取当前考站或考场状态
    static function getStatus($examId, $screenId, $entityId, $entityType)
    {
        if ($entityType == 2) {
            $builder = ExamPlanRecord::where('exam_id', $examId)
                ->where('exam_screening_id', $screenId)
                ->where('station_id', $entityId);
        } elseif ($entityType == 1) {
            $builder = ExamPlanRecord::where('exam_id', $examId)
                ->where('exam_screening_id', $screenId)
                ->where('room_id', $entityId);
        } else {
            throw new \Exception('系统错误，请重试', -9);
        }

        //如果该实体是否有开始而没有结束的,就说明当前是关门状态，设置静态属性为false
        if (is_null($builder->whereNull('end_dt')->first())) {
            return false;
            //反之，就说明是开门状态，设置其属性为true
        } else {
            return true;
        }
    }


    /*
     * 时间递增
     */
    function setTimeIncrease()
    {

    }

    /*
     * 获取实体正在考试的学生
     */
    function getEntityHavingStudents()
    {

    }

    /*
     * 获取实体需要的学生
     */
    function getEntityNeedStudents()
    {

    }

    /*
     * 考站的实例，以数组的方式返回
     */
    function stationTotal($examId)
    {
        try {
            //根据考试id找到对应的考试模式（考站还是考场）
            $sequenceMode = Exam::findOrFail($examId)->sequence_mode;

            //申明考站数组，直接返回这个数组
            $stations = [];
            if ($sequenceMode == 2) {
                //获取该考试下的所有考站
                $examFlowStations = ExamFlowStation::where('exam_id', '=', $examId)->get();

                if ($examFlowStations->isEmpty()) {
                    throw new \Exception('该场考试没有关联考站或考场！', -2);
                }
                //通过关联找到对应的考站信息
                foreach ($examFlowStations as $examFlowStation) {
                    //根据考站id找到对应的考场id
                    $roomId = $examFlowStation->roomStation->room->first()->id;
                    $temp = $examFlowStation->station;
                    if (is_null($temp)) {
                        throw new \Exception('该场考试没有关联考站或考场！', -2);
                    }
                    //将serialnumber和room_id放入$temp对象
                    $temp->sequence_mode = $sequenceMode;
                    $temp->serialnumber = $examFlowStation->serialnumber;
                    $temp->room_id = $roomId;
                    $temp->needNum = 1;
                    $stations[] = $temp;
                }
            } elseif ($sequenceMode == 1) {
                //获取该考试下的所有考场
                $examFlowRooms = ExamFlowRoom::where('exam_id', $examId)->get();

                if ($examFlowRooms->isEmpty()) {
                    throw new \Exception('该场考试没有关联考站或考场！', -2);
                }

                //通过关联找到对应的考场信息
                foreach ($examFlowRooms as $examFlowRoom) {
                    $temp = $examFlowRoom->room;
                    if (is_null($temp)) {
                        throw new \Exception('该场考试没有关联考站或考场！', -2);
                    }
                    /*
                     * 得到该考场下考站的最大的考试时间
                     */
                    $tempStations = $temp->station;
                    if ($tempStations->isEmpty()) {
                        throw new \Exception('该考场没有关联考站！', -3);
                    }
                    $mins = 0;

                    //循环数组，找到mins最大的值
                    foreach ($tempStations as $v) {
                        if (strtotime($v->mins) - $mins > 0) {
                            $mins = $v->mins;
                        }
                    }

                    //将mins的值写进room
                    $temp->mins = $mins;
                    //将serialnumber写进room
                    $temp->serialnumber = $examFlowRoom->serialnumber;
                    $temp->sequence_mode = $sequenceMode;
                    $temp->needNum = $tempStations->count();
                    $stations[] = $temp;
                }
            } else {
                throw new \Exception('非法操作！请重试', -1);
            }

            return $stations;
        } catch (\Exception $ex) {
            throw $ex;
        }
    }
}