<?php
/**
 * Created by PhpStorm.
 * User: j5110
 * Date: 2016/5/2
 * Time: 10:03
 */

namespace Modules\Osce\Entities\Drawlots;


use Modules\Osce\Entities\ExamQueue;
use Modules\Osce\Entities\ExamScreeningStudent;


class Student implements StudentInterface
{
    public function getStudent($examScreeningId, $nfc)
    {
        try {
            return ExamScreeningStudent::watch()
                ->where('watch.status', 1)
                ->where('watch.code', $nfc)
                ->where('exam_screening_student.exam_screening_id', $examScreeningId)
                ->where('exam_screening_student.is_end', 0)
                ->select(
                    'exam_screening_student.student_id as student_id',
                    'exam_screening_student.exam_screening_id as exam_screening_id',
                    'exam_screening_student.watch_id as watch_id'
                )
                ->first();
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    /**
     * 获取当前考站已经抽签的考生队列
     * @access public
     * @param $examId
     * @param $stationId
     * @return mixed
     * @version 3.6
     * @author ZouYuChao <ZouYuChao@sulida.com>
     * @time 2016-05-14
     * @copyright 2013-2017 sulida.com Inc. All Rights Reserved
     */
    public function getDrawlots($examId, $stationId)
    {
        return ExamQueue::whereExamId($examId)
            ->whereStationId($stationId)
            ->whereIn('status', [1, 2])
            ->orderBy('begin_dt', 'asc')
            ->first();
    }
}