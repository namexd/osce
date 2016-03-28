<?php
/**
 * Created by PhpStorm.
 * User: j5110
 * Date: 2016/3/23
 * Time: 15:42
 */

namespace Modules\Osce\Entities\ExamMidway;


use Modules\Osce\Entities\Exam;
use Modules\Osce\Entities\ExamMidway;

use Modules\Osce\Entities\ExamQueue;
use Modules\Osce\Entities\ExamRecordFlows;
use Modules\Osce\Entities\Station;
use Modules\Osce\Repositories\Common;
use Modules\Osce\Entities\RoomStation;
use Auth;

class Drawlots
{
    private $student;

    private $teacherId;

    private $exam;

    private $roomId;

    private $mode;

    public function __construct($student, $teacherId, $exam, $roomId)
    {
        $this->student = $student;
        $this->teacherId = $teacherId;
        $this->exam = $exam;
        $this->roomId = $roomId;
    }

    public function mode($mode)
    {
        $this->mode = $mode;
    }

    public function drawLots()
    {
        $connection = \DB::connection('osce_mis');
        $connection->beginTransaction();
        try {
            //得知当前学生是否已经抽签
            $temp = ExamQueue::where('student_id', $this->student->id)
                ->where('exam_id', $this->exam->id)
                ->whereIn('status', [1, 2])
                ->first();
            if (!is_null($temp)) {
                return Station::findOrFail($temp->station_id);
            }

            //判断目前是否可以在这个考场考试
            if ($this->exam->sequence_mode == 2) {
                $mode = new StationMode($this->teacherId, $this->exam);
            } elseif ($this->exam->sequence_mode == 1) {
                $mode = new RoomMode($this->teacherId, $this->exam);
            }
            $examinee = new Examinee($this->exam, ['id' => $this->teacherId]);
            $examinee->setMode($mode);
            $student = $examinee->examinee();
            $studentIds = $student->pluck('student_id');
            if ($studentIds->search($this->student->id) === false) {
                throw new \Exception('当前考生不应该在此处抽签', -1);
            }

            list($object, $array) = $this->mode->station($this->student, $this->exam);

            if (!$object->save()) {
                throw new \Exception('抽签失败！', -20);
            }
            $obj = $this->judgeTime($this->student->id);

            $array['after_begin_dt'] = $obj->begin_dt;
            $array['after_end_dt'] = $obj->end_dt;

            if (!ExamRecordFlows::create($array)) {
                throw new \Exception('系统错误！', -30);
            }

            //将阻塞状态变成0
            if (!ExamQueue::where('exam_id', $this->exam->id)
                ->where('student_id', $this->student->id)
                ->update(['blocking' => 0])
            ) {
                throw new \Exception('抽签失败！请重试', -2);
            }

            $connection->commit();
            return Station::findOrFail($obj->station_id);
        } catch (\Exception $ex) {
            $connection->rollBack();
            throw $ex;
        }
    }

    /**
     * 推回时间
     * @param $studentId
     * @return mixed
     * @throws \Exception
     * @author Jiangzhiheng
     * @time 2016-03-25 10:15
     */
    public function judgeTime($studentId)
    {
        //获取当前时间
        $date = date('Y-m-d H:i;s');

        //将当前时间与队列表的时间比较，如果比队列表的时间早，就用队列表的时间，否则就整体延后
        $studentObj = ExamQueue::where('student_id', $studentId)->where('status', 1)->first();
        if (!$studentObj) {
            throw new \Exception('当前没有符合条件的队列！', -1000);
        }
        $studentBeginTime = $studentObj->begin_dt;
        $studentEndTime = $studentObj->end_dt;
        if (strtotime($date) > strtotime($studentBeginTime)) {
            $diff = strtotime($date) - strtotime($studentBeginTime);
            $studentObjs = ExamQueue::where('student_id', $studentId)->where('status', '<', 2)->get();
            foreach ($studentObjs as $studentObj) {
                $studentObj->begin_dt = date('Y-m-d H:i:s', strtotime($studentBeginTime) + $diff);
                $studentObj->end_dt = date('Y-m-d H:i:s', strtotime($studentEndTime) + $diff);
                if (!$studentObj->save()) {
                    throw new \Exception('抽签失败！', -1001);
                }
            }
        }

        return $studentObj;
    }
}

class DrawStationMode implements DrawModeInterface
{
    /**
     * 返回station的实例
     * @param $student
     * @param $exam
     * @return mixed
     * @throws \Exception
     * @author Jiangzhiheng
     * @time 2016-03-23 14:38
     */
    public function station($student, $exam)
    {
        // TODO: Implement station() method.
        try {
            $examQueue = ExamQueue::where('student_id', $student->id)
                ->where('exam_id', $exam->id)
                ->where('status', 0)
                ->orderBy('begin_dt', 'asc')
                ->get();
            Common::objIsEmpty($examQueue, -2, '当前考生不应该在此处抽签');

            //获得他应该要去的考站id
            $tempObj = $examQueue->first();

            //得到当前这个考站是否有人在考
            if (ExamQueue::where('station_id', $tempObj->station_id)
                ->where('exam_id', $exam->id)
                ->where('blocking', 0)
                ->get()->isEmpty()
            ) {
                $stationId = $tempObj->station_id;
                if ($tempObj->status != 0) {
                    throw new \Exception('该考生数据错误！', -3);
                }

                $tempObj->status = 1;
                $array = [
                    'exam_queue_id' => $tempObj->id,
                    'before_status' => 0,
                    'before_begin_dt' => $tempObj->begin_dt,
                    'before_end_dt' => $tempObj->end_dt,
                    'before_room_id' => $tempObj->room_dt,
                    'before_station_id' => $tempObj->station_id,
                    'after_status' => 1,
                    'after_room_id' => $tempObj->room_dt,
                    'after_station_id' => $tempObj->station_id,
                    'ctrl_desc' => '抽签',
                    'created_user_id' => Auth::user()->id,
                ];

            } else { //如果本来去的考站被占用了,就给一个同流程的考站
                $model = ExamQueue::where('serialnumber', $tempObj->serialnumber)
                    ->where('blocking', 1)
                    ->where('exam_id', $exam->id)
                    ->first();

                $tempObj->status = 1;
                $room = RoomStation::where('station_id', $model->station_id)->first();
                $tempObj->room_id = $room->room_id;

                if (!is_null($model)) {
                    //将信息插入记录流水表
                    $array = [
                        'exam_queue_id' => $tempObj->id,
                        'before_status' => 0,
                        'before_begin_dt' => $tempObj->begin_dt,
                        'before_end_dt' => $tempObj->end_dt,
                        'before_room_id' => $tempObj->room_dt,
                        'before_station_id' => $tempObj->station_id,
                        'after_status' => 1,
                        'after_room_id' => $model->room_dt,
                        'after_station_id' => $model->station_id,
                        'ctrl_desc' => '抽签',
                        'created_user_id' => Auth::user()->id,
                    ];
                } else {
                    throw new \Exception('当前没有考站可供该考生考试', -5);
                }

            }
            //查出考站的信息

            return [$tempObj, $array];
        } catch (\Exception $ex) {
            throw $ex;
        }
    }
}

class DrawRoomMode implements DrawModeInterface
{
    /**
     * 返回station的实例
     * @param $student
     * @param $exam
     * @return mixed
     * @throws \Exception
     * @author Jiangzhiheng
     * @time 2016-03-23 15:06
     */
    public function station($student, $exam)
    {
        // TODO: Implement station() method.
        //获取当前考生应该什么考场和流程
        $object = ExamQueue::where('student_id', $student->id)
            ->where('exam_id', $exam->id)
            ->where('status', 0)
            ->orderBy('begin_dt', 'asc')
            ->first();

        //获得这个考场下面有哪些考站
        $stationIds = RoomStation::where('room_id', $object->room_id)->get()->pluck('station_id');

        //得到目前这个考场下面有哪些考站正在使用
        $diff = $this->diffStationId($exam, $object, $stationIds);

        //为该考生分配一个属于该考场的空的考站
        if ($diff->count() != 0) {
            list($station, $array) = $this->drawlot($student, $exam, $diff, $object);
            return [$station, $array];
        } else { //获取同一流程下面的其他考场下的考站，看有没有空
            $emptyRoomId = ExamQueue::where('serialnumber', $object->serialnumber)
                ->where('exam_id', $exam->id)
                ->where('blocking', 1)
                ->get()
                ->pluck('room_id');

            if (!$emptyRoomId->isEmpty()) {
                //获得这个考场下面有哪些考站
                $stationIds = RoomStation::whereIn('room_id', $emptyRoomId->toArray())
                    ->get()->pluck('station_id');
                $diff = $this->diffStationId($exam, $object, $stationIds);

                if ($diff->count() != 0) {
                    list($station, $array) = $this->drawlot($student, $exam, $diff, $object);
                    return [$station, $array];
                } else {
                    throw new \Exception('当前没有考站可供该考生考试', -3);
                }

            } else {
                throw new \Exception('当前没有考站可供该考生考试', -4);
            }
        }
    }

    /**
     * @param $exam
     * @param $object
     * @param $stationIds
     * @author Jiangzhiheng
     * @time
     */
    private function diffStationId($exam, $object, $stationIds)
    {
        //得到目前这个考场下面有哪些考站正在使用
        $usedIds = ExamQueue::where('exam_id', $exam->id)
            ->where('room_id', $object->room_id)
            ->where('blocking', 0)
            ->get()
            ->pluck('station_id');

        return collect(array_diff($stationIds->toArray(), $usedIds->toArray()));
    }

    /**
     * @param $student
     * @param $exam
     * @param $diff
     * @param $object
     * @return array
     * @return array
     * @throws \Exception
     * @author Jiangzhiheng
     * @time
     */
    private function drawlot($student, $exam, $diff, $object)
    {
        $array = [
            'exam_queue_id' => $object->id,
            'before_status' => 0,
            'before_begin_dt' => $object->begin_dt,
            'before_end_dt' => $object->end_dt,
            'before_room_id' => $object->room_id,
            'before_station_id' => $object->station_id,
            'ctrl_desc' => '抽签',
            'created_user_id' => Auth::user()->id
        ];

        $object->station_id = $diff->random();
        $room = RoomStation::where('station_id', $object->station_id)->first();
        $object->status = 1;
        $object->room_id = $room->room_id;



        $array['after_status'] = 1;
        $array['after_room_id'] = $object->room_id;
        $array['after_station_id'] = $object->station_id;


        return [$object, $array];
    }


}

interface DrawModeInterface
{
    public function station($student, $exam);
}
