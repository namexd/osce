<?php
/**
 * Created by PhpStorm.
 * User: j5110
 * Date: 2016/2/16
 * Time: 14:02
 */

namespace Modules\Osce\Entities\AutomaticPlanArrangement;


use Modules\Osce\Entities\Exam;
use Modules\Osce\Entities\ExamFlowStation;
use Modules\Osce\Entities\StationTeacher;

class ExamPlaceEntity implements ExamPlaceEntityInterface
{
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
    function getStatus()
    {

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
                $examFlowStations = ExamFlowStation::where('exam_id', '=' ,$examId)->get();

                if ($examFlowStations->isEmpty()) {
                    throw new \Exception('该场考试没有关联考站或考场！',-2);
                }
                //通过关联找到对应的考站信息
                foreach ($examFlowStations as $examFlowStation) {
                    $temp = $examFlowStation->station;
                    if (is_null($temp)) {
                        throw new \Exception('该场考试没有关联考站或考场！',-2);
                    }
                    //将serialnumber放入$temp对象
                    $temp->serialnumber = $examFlowStation->serialnumber;
                    $stations[] = $temp;
                }
            } elseif ($sequenceMode == 1) {
                //获取该考试下的所有考场
                $examFlowRooms = ExamFlowRoom::where('exam_id', $examId)->get();

                if ($examFlowRooms->isEmpty()) {
                    throw new \Exception('该场考试没有关联考站或考场！',-2);
                }

                //通过关联找到对应的考场信息
                foreach ($examFlowRooms as $examFlowRoom) {
                    $temp = $examFlowRoom->room;
                    if (is_null($temp)) {
                        throw new \Exception('该场考试没有关联考站或考场！',-2);
                    }
                    /*
                     * 得到该考场下考站的最大的考试时间
                     */
                    $stations = $temp->station;
                    $mins = 0;
                    //循环数组，找到mins最大的值
                    foreach ($stations as $v) {
                        if ($v->mins - $mins > 0) {
                            $mins = $v->mins;
                        }
                    }

                    //将mins的值写进room
                    $temp->mins = $mins;
                    //将serialnumber写进room
                    $temp->serialnumber = $examFlowRoom->serialnumber;
                    $stations[] = $temp;
                }
            } else {
                throw new \Exception('非法操作！请重试',-1);
            }

            return $stations;
        } catch (\Exception $ex) {
            throw $ex;
        }
    }
}