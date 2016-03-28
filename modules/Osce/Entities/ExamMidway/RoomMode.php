<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/3/25 0025
 * Time: 20:14
 */

namespace Modules\Osce\Entities\ExamMidway;


use Modules\Osce\Entities\ExamFlowRoom;
use Modules\Osce\Entities\ExamQueue;
use Modules\Osce\Entities\RoomStation;
use Modules\Osce\Entities\Teacher;

class RoomMode
{
    /*
         * 老师所在的room
         */
    private $room;

    /*
     * 老师的id
     */
    private $id;

    /*
     * 考试实体
     */
    private $exam;

    private $_T_Count;

    function __construct($id, $exam)
    {
        $this->id = $id;
        $this->exam = $exam;
        $this->room = Teacher::room($id, $exam);
        $this->_T_Count = RoomStation::where('room_id','=',$this->room->room_id)->count();
    }

    /**
     * 获取flow
     * @author Jiangzhiheng
     * @time 2016-03-22 16:58
     */
    function getFlow()
    {
        // TODO: Implement getFlow() method.
        try {
            return ExamFlowRoom::where('exam_id', $this->exam->id)
                ->where('room_id', $this->room->room_id)
                ->get();
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    function getExaminee(array $serialnumber)
    {
        // TODO: Implement getExaminee() method.
        try {
            $collection = ExamQueue::leftJoin('student', 'student.id', '=', 'exam_queue.student_id')
                ->whereIn('exam_queue.room_id', $this->room->id)
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
                ->take($this->_T_Count)
                ->get();

            $array = [];
            foreach ($collection as $item) {
                if ($item->blocking != 1) {
                    continue;
                }
                $array[] = $item;
            }
            $array = collect($array);
            if (count($array) != $this->_T_Count) {
                $difference = $this->_T_Count - count($array);
                $temp = ExamQueue::leftJoin('student', 'student.id', '=', 'exam_queue.student_id')
                    ->whereIn('exam_queue.serialnumber', $serialnumber)
                    ->where('exam_queue.status', '<', 3)
                    ->where('exam_queue.blocking', 1)
                    ->whereNotIn('student.id', $array->pluck('student_id'))
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
                    ->take($difference)
                    ->get();
                if (!$temp->isEmpty()) {
                    foreach ($temp as $item) {
                        $array->push($item);
                    }
                }
            }

            return $array;
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    function getNextExaminee(array $serialnumber)
    {
        // TODO: Implement getNextExaminee() method.
        try {
            $collection = ExamQueue::leftJoin('student', 'student.id', '=', 'exam_queue.student_id')
                ->whereIn('exam_queue.room_id', $this->room->id)
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
                ->skip($this->_T_Count)
                ->take($this->_T_Count)
                ->get();

            $array = [];
            foreach ($collection as $item) {
                if ($item->blocking != 1) {
                    continue;
                }
                $array[] = $item;
            }
            $array = collect($array);
            if (count($array) != $this->_T_Count) {
                $difference = $this->_T_Count - count($array);
                $temp = ExamQueue::leftJoin('student', 'student.id', '=', 'exam_queue.student_id')
                    ->whereIn('exam_queue.serialnumber', $serialnumber)
                    ->where('exam_queue.status', '<', 3)
                    ->where('exam_queue.blocking', 1)
                    ->whereNotIn('student.id', $array->pluck('student_id'))
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
                    ->skip($difference)
                    ->take($difference)
                    ->get();

                if (!$temp->isEmpty()) {
                    foreach ($temp as $item) {
                        $array->push($item);
                    }
                }
            }
            return $array;
        } catch (\Exception $ex) {
            throw $ex;
        }
    }
}