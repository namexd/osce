<?php
/**
 * Created by PhpStorm.
 * User: j5110
 * Date: 2016/5/13
 * Time: 10:17
 */

namespace Modules\Osce\Entities\ExamMidway;


use Modules\Osce\Entities\Exam;
use Modules\Osce\Entities\ExamOrder;
use Modules\Osce\Entities\ExamQueue;
use Modules\Osce\Entities\ExamScreening;
use Modules\Osce\Entities\ExamScreeningStudent;
use Modules\Osce\Entities\ExamStationStatus;
use Modules\Osce\Entities\Watch;
use Modules\Osce\Entities\WatchLog;

class ExamMidwayRepository
{
    /**
     * 更改开始考试的状态
     * @access public
     * @param $examId
     * @param $stationId
     * @return mixed
     * @version
     * @author ZouYuChao <ZouYuChao@sulida.com>
     * @time 2016-05-13
     * @copyright 2013-2017 sulida.com Inc. All Rights Reserved
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
     * @author ZouYuChao <ZouYuChao@sulida.com>
     * @time 2016-05-13
     * @copyright 2013-2017 sulida.com Inc. All Rights Reserved
     */
    public function getQueueByStudent($studentId, $examId)
    {
        return ExamQueue::whereStudentId($studentId)->whereExamId($examId)->whereStatus(1)->orderBy('begin_dt',
            'asc')->first();
    }

    /**
     * 是否要把状态变更为2
     * @access public
     * @param $examId
     * @param array $stationIds
     * @return bool
     * @version
     * @author ZouYuChao <ZouYuChao@sulida.com>
     * @time 2016-05-13
     * @copyright 2013-2017 sulida.com Inc. All Rights Reserved
     */
    public function isChangeToTwo($examId, array $stationIds)
    {
        $count = ExamStationStatus::whereIn('station_id', $stationIds)
            ->whereExamId($examId)
            ->whereStatus(1)
            ->count();
        if ($count == count($stationIds)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * 重置一场考试（注意：此考试无法继续使用）
     * @access public
     * @param $examId
     * @return bool
     * @throws \Exception
     * @throws \Throwable
     * @version
     * @author ZouYuChao <ZouYuChao@sulida.com>
     * @time 2016-05-14
     * @copyright 2013-2017 sulida.com Inc. All Rights Reserved
     */
    public function reset($examId)
    {
        $connection = \DB::connection('osce_mis');
        $connection->beginTransaction();
        try {
            //将exam状态重置
            Exam::whereId($examId)->update(['status' => 0]);
            //将exam_screening状态重置
            $examScreenings = ExamScreening::whereExamId($examId)->get();
            foreach ($examScreenings as &$examScreening) {
                if (1 == $examScreening->status) {
                    $examScreening->status = 0;
                    if (!$examScreening->save()) {
                        throw new \Exception('重置$examScreening失败！');
                    }
                }
            }

            /*
             * 将腕表解绑
             */
            if (ExamScreeningStudent::whereIn('exam_screening_id', $examScreenings->pluck('id')->count())) {
                $examScreeningStudents = ExamScreeningStudent::whereIn('exam_screening_id', $examScreenings->pluck('id'))
                    ->get();

                ExamScreeningStudent::whereIn('exam_screening_id', $examScreenings->pluck('id'))
                    ->delete();

                //获取已经绑定了的腕表id的集合
                $watchIds = $examScreeningStudents->pluck('watch_id')->toArray();
                //获取学生集合
                $studentIds = $examScreeningStudents->pluck('student_id')->toArray();
                //删掉watch_log表
                if (WatchLog::whereIn('watch_id', $watchIds)->whereIn('student_id', $studentIds)->count()) {
                    WatchLog::whereIn('watch_id', $watchIds)->whereIn('student_id', $studentIds)->delete();
                }

                //重置watch表
                Watch::whereIn('id', $watchIds)->update(['status' => 0]);
            }

            /*
             * 重置order表
             */
            ExamOrder::whereExamId($examId)->update(['status' => 0]);
            $connection->commit();
        } catch (\Exception $ex) {
            $connection->rollBack();
            throw $ex;
        }

        return true;
    }
}