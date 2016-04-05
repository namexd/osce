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
use Modules\Osce\Entities\Student;
use Modules\Osce\Entities\Teacher;

class RoomMode implements ModeInterface
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
        $this->_T_Count = RoomStation::where('room_id', '=', $this->room->room_id)->count();
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
            //获取首位固定
            $sticks = ExamQueue::where('exam_id', $this->exam->id)->where('room_id', $this->room->room_id)->where('stick', $this->room->room_id)->get();
            
            if (count($sticks) < $this->_T_Count) {
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
                        'student.description as student_description',
                        'exam_queue.blocking as blocking',
                        'exam_queue.id as id'
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

                if (count($array) < $this->_T_Count) {
                    $difference = $this->_T_Count - count($array);
                    $temp = ExamQueue::leftJoin('student', 'student.id', '=', 'exam_queue.student_id')
                        ->whereIn('exam_queue.serialnumber', $serialnumber)
                        ->where('exam_queue.status', '<', 3)
                        ->where('exam_queue.blocking', 1)
                        ->whereNull('stick')
                        ->whereNotIn('student.id', $array->pluck('student_id'))
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
                            'student.description as student_description'
                        )
                        ->orderBy('exam_queue.begin_dt', 'asc')
                        ->groupBy('student.id')
                        ->take($difference)
                        ->get();
                    if (!$temp->isEmpty()) {
                        foreach ($temp as $item) {
                            //将item的考场信息修改
                            $item->room_id = $this->room->room_id;
                            $item->save();
                            $array->push($item);
                        }
                    }
                }
                dd($array);
                //实现首位固定
                foreach ($array as $student) {
//                    $stick = ExamQueue::where('exam_id', $this->exam->id)
//                        ->where('student_id', $student->student_id)
//                        ->orderBy('begin_dt', 'asc')
//                        ->first();
//                    $stick->stick = $this->room->id;
//                    \Log::alert('stick', $stick->toArray());
//                    if (!$stick->save()) {
//                        throw new \Exception('系统异常，请重试', -5);
//                    }
                    $student->stick = $this->room->room_id;
                    if (!$student->save()) {
                        throw new \Exception('系统错误，请重试', -1);
                    }
                }

                return $array;
            } else {
                return ExamQueue::leftJoin('student', 'student.id', '=', 'exam_queue.student_id')
                    ->whereIn('exam_queue.id', $sticks->pluck('id')->toArray())
                    ->whereIn('student.id', $sticks->pluck('student_id')->unique()->toArray())
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
            }
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
                    'student.description as student_description',
                    'exam_queue.blocking as blocking'
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