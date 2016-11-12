<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/5/7 0007
 * Time: 11:30
 */

namespace Modules\Osce\Entities\Drawlots\Traits;

use Modules\Osce\Entities\ExamQueue;
use Modules\Osce\Entities\QuestionBankEntities\ExamMonitor;
use Modules\Osce\Repositories\Common;

trait SmartTraits
{
    /**
     * 处理队列表的时间
     * @access public
     * @param $studentId
     * @return bool
     * @throws \Exception
     * @version 3.6
     * @author JiangZhiheng <JiangZhiheng@misrobot.com>
     * @time 2016-05-04
     * @copyright 2013-2016 MIS misrobot.com Inc. All Rights Reserved
     */
    public function judgeTime($studentId, $screen)
    {
        //获取当前时间
        $date = date('Y-m-d H:i:s');

        //将当前时间与队列表的时间比较，如果比队列表的时间早，就用队列表的时间，否则就整体延后
        $tempStudent = ExamQueue::where('student_id', $studentId)
            ->where('exam_screening_id', $screen->id)
            ->where('status', 1)
            ->first();
        Common::valueIsNull($tempStudent, -50, '当前没有符合条件的队列');

        /*
         * 获取开始时间和结束时间
         */
        $studentBeginTime = $tempStudent->begin_dt;

        /**
         * 判断时间
         * 如果当前时间已经晚于开始时间，就递推
         */
        if (strtotime($date) > strtotime($studentBeginTime)) {
            $diff = strtotime($date) - strtotime($studentBeginTime);
            $studentObjs = ExamQueue::where('student_id', $studentId)
                ->where('exam_screening_id', $screen->id)
                ->where('status', '<', 2)
                ->get();
            foreach ($studentObjs as $studentObj) {
                $studentObj->begin_dt = date('Y-m-d H:i:s', strtotime($studentObj->begin_dt) + $diff);
                $studentObj->end_dt = date('Y-m-d H:i:s', strtotime($studentObj->end_dt) + $diff);
                if (!$studentObj->save()) {
                    throw new \Exception('抽签失败！', -1001);
                }
            }
        }

//        return true;
    }

    /**
     * 将信息写入exam_queue
     * @access public
     * @param $obj ExamQueue's object
     * @return mixed
     * @throws \Exception
     * @version 3.6
     * @author JiangZhiheng <JiangZhiheng@misrobot.com>
     * @time 2016-05-02
     * @copyright 2013-2016 MIS misrobot.com Inc. All Rights Reserved
     */
    public function writeExamQueue($obj, $stationId, $status = 1)
    {
        //把异常考生记录表里考站改过来
         $ExamMonitor =ExamMonitor::where('exam_screening_id','=',$obj->exam_screening_id)
             ->where('exam_id','=',$obj->exam_id)
             ->where('room_id','=',$obj->room_id)
             ->where('student_id','=',$obj->student_id)
             ->where('status','=',0)
             ->first();
        if(!is_null($ExamMonitor)){
            $ExamMonitor->station_id = $stationId;
            $ExamMonitor->status = $status;
            if(!$ExamMonitor->save()){
                throw new \Exception('更改缺考记录表失败！', -1005);
            }
        }
        $obj->station_id = $stationId;
        $obj->status = $status;
        $obj->blocking = 0;
        if(!$obj->save()){
            throw new \Exception('抽签失败！', -1005);
        }
//        return $obj->save();
    }
}