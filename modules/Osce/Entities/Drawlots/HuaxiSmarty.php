<?php
/**
 * Created by PhpStorm.
 * User: j5110
 * Date: 2016/5/2
 * Time: 10:50
 */

namespace Modules\Osce\Entities\Drawlots;


use Modules\Osce\Entities\ExamQueue;
use Modules\Osce\Repositories\Common;

class HuaxiSmarty
{
    private $stationId;

    /**
     * 随机抽签给学生
     * @access public
     * @param $stations
     * @return mixed
     * @throws \Exception
     * @version 3.6
     * @author JiangZhiheng <JiangZhiheng@misrobot.com>
     * @time 2016-05-02
     * @copyright 2013-2016 MIS misrobot.com Inc. All Rights Reserved
     */
    public function ramdonId($stations)
    {
        if ($stations->isEmpty()) {
            throw new \Exception('当前数据有问题', -60);
        }

        return $this->stationId = $stations->random();
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
    public function writeExamQueue($obj)
    {
        $obj->station_id = $this->stationId;
        $obj->status = 1;
        return $obj->save();
    }

    /**
     * 拼装返回成功字符串
     * @access public
     * @param $name
     * @return string
     * @version 3.6
     * @author JiangZhiheng <JiangZhiheng@misrobot.com>
     * @time 2016-05-02
     * @copyright 2013-2016 MIS misrobot.com Inc. All Rights Reserved
     */
    public function assembly($name)
    {
        return ['抽签成功，请到' . $name . '考站进行考试'];
    }

    /**
     * 检查是否抽签
     * @access public
     * @param $studentId
     * @return mixed
     * @version
     * @author JiangZhiheng <JiangZhiheng@misrobot.com>
     * @time 2016-05-02
     * @copyright 2013-2016 MIS misrobot.com Inc. All Rights Reserved
     */
    public function isDraw($studentId)
    {
        return ExamQueue::whereStudentId($studentId)
            ->whereIn('status', [1, 2])
            ->orderBy('begin_dt', 'asc')
            ->first();
    }

    /**
     * 获取学生在数据库里的实例
     * @access public
     * @param $studentId
     * @param $screenId
     * @return mixed
     * @version
     * @author JiangZhiheng <JiangZhiheng@misrobot.com>
     * @time 2016-05-02
     * @copyright 2013-2016 MIS misrobot.com Inc. All Rights Reserved
     */
    public function getObj($studentId, $screenId)
    {
        //蒋同学，未见此处定义，如果是内置方法，请给我一个合理的解释
        return ExamQueue::whereStudentId($studentId)
            ->whereExamScreeningId($screenId)
            ->orderBy('begin_dt', 'asc')
            ->first();
    }

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

        return true;
    }
}