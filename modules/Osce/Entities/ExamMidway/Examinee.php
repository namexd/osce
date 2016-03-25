<?php
/**
 * Created by PhpStorm.
 * User: j5110
 * Date: 2016/3/22
 * Time: 14:38
 */

namespace Modules\Osce\Entities\ExamMidway\Examinee;


use Modules\Osce\Entities\Exam;
use Modules\Osce\Entities\ExamFlowRoom;
use Modules\Osce\Entities\ExamFlowStation;
use Modules\Osce\Entities\ExamQueue;
use Modules\Osce\Entities\Station;
use Modules\Osce\Entities\StationTeacher;
use Modules\Osce\Entities\Student;
use Modules\Osce\Entities\RoomStation;
use Modules\Osce\Entities\Teacher;
use Modules\Osce\Repositories\Common;

class Examinee
{
    private $params;

    private $exam;

    private $mode;

    /**
     * Drawlots constructor.
     * @param $exam
     * @param array $params
     */
    public function __construct($exam, array $params)
    {
        $this->exam = $exam;
        $this->params = $params;
    }

    public function setMode(ModeInterface $mode)
    {
        $this->mode = $mode;
    }

    /**
     * 获取到当前组学生
     * @throws \Exception
     * @author Jiangzhiheng
     * @time 2016-03-22 18:39
     */
    public function examinee()
    {
        try {
            //获取到对象实例
            $examFlowStation = $this->mode->getFlow();

            $serialnumber = $examFlowStation->pluck('serialnumber')->unique()->toArray();

            //直接仍回学生实例
            $students = $this->mode->getExaminee($serialnumber);

            //将图片地址加上域名
            foreach ($students as &$student) {
                $student->avator = asset($student->avator);
                $student->serialnumber = $serialnumber;
            }

            return $students;
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    /**
     * 获取下一组考生
     * @return mixed
     * @throws \Exception
     * @author Jiangzhiheng
     * @time 2016-03-23 10:32
     */
    public function nextExaminee()
    {
        try {
            //获取到对象实例
            $examFlowStation = $this->mode->getFlow();

            $serialnumber = $examFlowStation->pluck('serialnumber')->unique()->toArray();

            //直接仍回学生实例
            $students = $this->mode->getNextExaminee($serialnumber);

            //将图片地址加上域名
            foreach ($students as &$student) {
                $student->avator = asset($student->avator);
            }

            return $students;
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    /**
     * 获取当前考试实体的信息
     * @author Jiangzhiheng
     * @time 2016-03-23 18:21
     */
    public function getStation()
    {
        $stationTeacher = StationTeacher::where('user_id', $this->params['id'])
            ->where('exam_id', $this->exam->id)->get();
        try {
            switch ($stationTeacher->count()) {
                case 1: //说明要给出考场-考站
                    $roomStation = RoomStation::where('station_id', $stationTeacher->first()->station_id)
                        ->first();
                    Common::valueIsNull($roomStation, -1);
                    $room = $roomStation->room;

                    $station = $roomStation->station;

                    $station->name = $room->name . '-' . $station->name;

                    //将考场的id封装进去
                    $station->room_id = $room->id;

                    //将考试的id封装进去
                    $station->exam_id = $this->exam->id;

                    //将当前的服务器时间返回
                    $station->service_time = time() * 1000;

                    return $station;
                    break;
                case 0: //报错
                    throw new \Exception('数据错误，请重试', -1);
                    break;
                default: //说明只需要给出考场
                    $roomStation = RoomStation::where('station_id', $stationTeacher->first()->station_id)
                        ->first();
                    $room = $roomStation->room;

                    //将考场的id封装进去
                    $room->room_id = $room->id;

                    //将考试的id封装进去
                    $room->exam_id = $this->exam->id;

                    //将当前的服务器时间返回
                    $room->service_time = time() * 1000;
                    return $room;
                    break;
            }
        } catch (\Exception $ex) {
            throw $ex;
        }
    }
}

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
                'student.description as student_description'
            )
            ->orderBy('exam_queue.begin_dt', 'asc')
            ->groupBy('student.id')
            ->take(1)
            ->get();

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
        $collection = ExamQueue::leftJoin('student', 'student.id', '=', 'exam_queue.student_id')
            ->orWhereIn('exam_queue.station_id', $this->stationIds)
            ->where('exam_queue.status', '<', 3)
            ->where('student.exam_id', $this->exam->id)
            ->select(
                'student.id as student_id',
                'student.name as student_name',
                'student.code as student_code'
            )
            ->orderBy('exam_queue.begin_dt', 'asc')
            ->groupBy('student.id')
            ->skip(1)
            ->take(1)
            ->get();

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
    }
}

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
        $this->_T_Count = $this->room->stations->count();
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
                ->where('room_id', $this->room->id)
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

interface ModeInterface
{
    function getFlow();

    function getExaminee(array $serialnumber);

    function getNextExaminee(array $serialnumber);

}

