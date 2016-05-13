<?php
/**
 * Created by PhpStorm.
 * User: j5110
 * Date: 2016/5/13
 * Time: 10:17
 */

namespace Modules\Osce\Entities\ExamMidway;


use Modules\Osce\Entities\ExamQueue;
use Modules\Osce\Entities\ExamStationStatus;

class ExamMidwayRepository
{
    /**
     * 更改开始考试的状态
     * @access public
     * @param $examId
     * @param $stationId
     * @return mixed
     * @version
     * @author JiangZhiheng <JiangZhiheng@misrobot.com>
     * @time 2016-05-13
     * @copyright 2013-2016 MIS misrobot.com Inc. All Rights Reserved
     */
    public function beginTheoryStatus($examId, $stationId, $status = 3)
    {
        return ExamStationStatus::whereExamId($examId)->whereStationId($stationId)->update(['status' => $status]);
    }

    /**
     * 获取已经抽签的学生队列
     * @access public
     * @param $studentId
     * @param $examId
     * @return mixed
     * @version
     * @author JiangZhiheng <JiangZhiheng@misrobot.com>
     * @time 2016-05-13
     * @copyright 2013-2016 MIS misrobot.com Inc. All Rights Reserved
     */
    public function getQueueByStudent($studentId, $examId)
    {
        return ExamQueue::whereStudentId($studentId)->whereExamId($examId)->whereStatus(1)->orderBy('begin_dt', 'asc')->first();
    }
}