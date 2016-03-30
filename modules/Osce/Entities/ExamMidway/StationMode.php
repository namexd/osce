<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/3/25 0025
 * Time: 20:10
 */

namespace Modules\Osce\Entities\ExamMidway;


use Modules\Osce\Entities\ExamFlowStation;
use Modules\Osce\Entities\ExamQueue;
use Modules\Osce\Entities\Teacher;

class StationMode implements ModeInterface
{
    /*
         * 老师所在的stationid的集合
         */
    private $stationIds;

    /*
     * 老师的id
     */
    private $id;

    /*
     * 考试实体
     */
    private $exam;

    /**
     * StationMode constructor.
     * @param $id
     * @param $exam
     */
    function __construct($id, $exam)
    {
        $this->id = $id;
        $this->exam = $exam;
        $this->stationIds = Teacher::stationIds($id, $exam);
    }

    /**
     * @author Jiangzhiheng
     * @time 2016-03-22 16:58
     * @throws \Exception
     */
    function getFlow()
    {
        // TODO: Implement getFlow() method.
        try {
            return ExamFlowStation::where('exam_id', $this->exam->id)
                ->whereIn('station_id', $this->stationIds->toArray())
                ->get();
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    /**
     * 获取当前组考生的实例
     * @param array $serialnumber
     * @return mixed
     * @author Jiangzhiheng
     * @time 2016-03-23 10:12
     */
    function getExaminee(array $serialnumber)
    {
        // TODO: Implement getExaminee() method.
        $collection = ExamQueue::leftJoin('student', 'student.id', '=', 'exam_queue.student_id')
            ->whereIn('exam_queue.station_id', $this->stationIds)
            ->where('exam_queue.status', '<', 3)
            ->where('student.exam_id', $this->exam->id)
            ->select(
                'student.id as student_id',
                'student.name as student_name',
                'student.user_id as student_user_id',
                'student.idcard as student_idcard',
                'student.mobile as student_mobile',
                'student.code as student_code',
                'student.avator as student_avator',
                'student.description as student_description',
                'exam_queue.blocking as blocking'
            )
            ->orderBy('exam_queue.begin_dt', 'asc')
            ->groupBy('student.id')
            ->take(1)
            ->get();
        if (is_null($collection->first())) {
            if ($collection->first()->blocking != 1) {
                return ExamQueue::leftJoin('student', 'student.id', '=', 'exam_queue.student_id')
                    ->whereIn('exam_queue.serialnumber', $serialnumber)
                    ->where('exam_queue.status', '<', 3)
                    ->where('student.exam_id', $this->exam->id)
                    ->select(
                        'student.id as student_id',
                        'student.name as student_name',
                        'student.user_id as student_user_id',
                        'student.idcard as student_idcard',
                        'student.mobile as student_mobile',
                        'student.code as student_code',
                        'student.avator as student_avator',
                        'student.description as student_description'
                    )
                    ->orderBy('exam_queue.begin_dt', 'asc')
                    ->groupBy('student.id')
                    ->take(1)
                    ->get();
            } else {
                return $collection;
            }
        } else {
            return collect([]);
        }

    }

    /**
     * 获取下一组考生的实例
     * @param array $serialnumber
     * @return mixed
     * @author Jiangzhiheng
     * @time 2016-03-23 10:34
     */
    function getNextExaminee(array $serialnumber)
    {
        // TODO: Implement getNextExaminee() method.
        $collection = ExamQueue::leftJoin('student', 'student.id', '=', 'exam_queue.student_id')
            ->orWhereIn('exam_queue.station_id', $this->stationIds)
            ->where('exam_queue.status', '<', 3)
            ->where('student.exam_id', $this->exam->id)
            ->select(
                'student.id as student_id',
                'student.name as student_name',
                'student.code as student_code',
                'exam_queue.blocking as blocking'
            )
            ->orderBy('exam_queue.begin_dt', 'asc')
            ->groupBy('student.id')
            ->skip(1)
            ->take(1)
            ->get();

        if (is_null($collection->first())) {
            if ($collection->first()->blocking != 1) {
                return ExamQueue::leftJoin('student', 'student.id', '=', 'exam_queue.student_id')
                    ->whereIn('exam_queue.serialnumber', $serialnumber)
                    ->where('exam_queue.status', '<', 3)
                    ->where('student.exam_id', $this->exam->id)
                    ->select(
                        'student.id as student_id',
                        'student.name as student_name',
                        'student.user_id as student_user_id',
                        'student.idcard as student_idcard',
                        'student.mobile as student_mobile',
                        'student.code as student_code',
                        'student.avator as student_avator',
                        'student.description as student_description'
                    )
                    ->orderBy('exam_queue.begin_dt', 'asc')
                    ->groupBy('student.id')
                    ->skip(1)
                    ->take(1)
                    ->get();
            } else {
                return $collection;
            }
        } else {
            return collect([]);
        }
    }
}