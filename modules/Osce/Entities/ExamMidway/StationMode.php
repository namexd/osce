<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/3/25 0025
 * Time: 20:10
 */

namespace Modules\Osce\Entities\ExamMidway;


use Modules\Osce\Entities\Exam;
use Modules\Osce\Entities\ExamFlowStation;
use Modules\Osce\Entities\ExamQueue;
use Modules\Osce\Entities\RoomStation;
use Modules\Osce\Entities\Student;
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
     * @author ZouYuChao
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
     * @author ZouYuChao
     * @time 2016-03-23 10:12
     */
    function getExaminee(array $serialnumber)
    {
        // TODO: Implement getExaminee() method.
        //获取首位固定的考生
        $sticks = ExamQueue::where('exam_id', $this->exam->id)
            ->whereIn('station_id',$this->stationIds)
            ->whereIn('stick', $this->stationIds)
            ->get();
        if ($sticks->isEmpty()) {
            //获取应该在此处考试的考生
//            $a=\DB::connection('osce_mis');
//            $a->enableQueryLog();

            $collection = ExamQueue::leftJoin('student', 'student.id', '=', 'exam_queue.student_id')
                ->whereIn('exam_queue.station_id', $this->stationIds)
                ->where('exam_queue.status', '<', 3)
                ->where('exam_queue.stick', '=', 0)
                ->where('student.exam_id', $this->exam->id)
                ->select(
                    'exam_queue.id as id',
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
//            dd($a->getQueryLog());
            if ($collection->isEmpty()) {
                //可以在此处考试的考生
                $query = ExamQueue::leftJoin('student', 'student.id', '=', 'exam_queue.student_id')
                    ->whereIn('exam_queue.serialnumber', $serialnumber)
                    ->where('exam_queue.status', '<', 3)
                    ->where('exam_queue.stick', '=', 0)
                    ->where('blocking', 1)
                    ->where('student.exam_id', $this->exam->id)
                    ->select(
                        'exam_queue.id as id',
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
                //实现首位固定
//                foreach ($query as $student) {
//                    $stick = ExamQueue::where('exam_id', $this->exam->id)
//                        ->where('student_id', $student->student_id)
//                        ->where('id', $student->id)
//                        ->orderBy('begin_dt', 'asc')
//                        ->first();
//                    $stick->stick = $this->stationIds[0];
//                    if (!$stick->save()) {
//                        throw new \Exception('系统异常，请重试', -5);
//                    }
//                }

                if ($query->isEmpty()) {
                    return collect([]);
                } else {
                    $stick  =   $query->first();
                    $stick->stick = $this->stationIds[0];
                    if (!$stick->save()) {
                        throw new \Exception('系统异常，请重试', -5);
                    }

                    //更改流程表中的考站id和考场id
//                    $a = ExamQueue::where('student_id', $query->first()->student_id)
//                        ->where('status', 0)->where('blocking', 1)
//                        ->orderBy('begin_dt', 'asc')->first();
//                    dd($stick);
                    $stick  =   ExamQueue::find($stick->id);
                    $stick->station_id = $this->stationIds[0];
                    $stick->room_id = RoomStation::where('station_id', $stick->station_id)->first()->room_id;
                    if (!$stick->save()) {
                        throw new \Exception('系统异常，请重试', -6);
                    }

                    return $query;
                }

            } else {
//                foreach ($collection as $student) {
//                    $stick = ExamQueue::where('exam_id', $this->exam->id)
//                        ->where('student_id', $student->student_id)
//                        ->where('id', $student->id)
//                        ->orderBy('begin_dt', 'asc')
//                        ->first();
//                    $stick->stick = $this->stationIds[0];
//                    if (!$stick->save()) {
//                        throw new \Exception('系统异常，请重试', -5);
//                    }
//                }
                $stick  =   $collection->first();
                $stick->stick = $this->stationIds[0];
                if (!$stick->save()) {
                    throw new \Exception('系统异常，请重试', -5);
                }

                return $collection;
            }
        } else {
            return ExamQueue::leftJoin('student', 'student.id', '=', 'exam_queue.student_id')
                        ->where('exam_queue.id', $sticks->first()->id)
                        ->where('student.id', $sticks->first()->student_id)
                        ->select(
                            'exam_queue.id as id',
                            'student.id as student_id',
                            'student.name as student_name',
                            'student.user_id as student_user_id',
                            'student.idcard as student_idcard',
                            'student.mobile as student_mobile',
                            'student.code as student_code',
                            'student.avator as student_avator',
                            'student.description as student_description',
                            'exam_queue.stick as stick',
                            'exam_queue.updated_at as updated_at'
                        )->get();
//            return Student::where('id', $sticks->first()->student_id)->select(
//                'student.id as student_id',
//                'student.name as student_name',
//                'student.user_id as student_user_id',
//                'student.idcard as student_idcard',
//                'student.mobile as student_mobile',
//                'student.code as student_code',
//                'student.avator as student_avator',
//                'student.description as student_description'
//            )->get();
        }
    }

    /**
     * 获取下一组考生的实例
     * @param array $serialnumber
     * @return mixed
     * @author ZouYuChao
     * @time 2016-03-23 10:34
     */
    function getNextExaminee(array $serialnumber)
    {
        // TODO: Implement getNextExaminee() method.
        $collection = ExamQueue::leftJoin('student', 'student.id', '=', 'exam_queue.student_id')
            ->whereIn('exam_queue.station_id', $this->stationIds)
            ->where('exam_queue.status', '<', 3)
            ->where('exam_queue.stick', 0)
            ->where('student.exam_id', $this->exam->id)
            ->select(
                'student.id as student_id',
                'student.name as student_name',
                'student.code as student_code',
                'exam_queue.blocking as blocking'
            )
            ->orderBy('exam_queue.begin_dt', 'asc')
            ->groupBy('student.id')
//            ->skip(1)
            ->take(1)
            ->get();

        if ($collection->isEmpty()) {

                return ExamQueue::leftJoin('student', 'student.id', '=', 'exam_queue.student_id')
                    ->whereIn('exam_queue.serialnumber', $serialnumber)
                    ->where('exam_queue.status', '<', 3)
                    ->where('blocking', 1)
                    ->where('exam_queue.stick', 0)
                    ->where('student.exam_id', $this->exam->id)
                    ->select(
                        'student.id as student_id',
                        'student.name as student_name',
                        'student.code as student_code',
                        'exam_queue.blocking as blocking'
                    )
                    ->orderBy('exam_queue.begin_dt', 'asc')
                    ->groupBy('student.id')
//                    ->skip(1)
                    ->take(1)
                    ->get();

        } else {
            return $collection;
        }
    }
}