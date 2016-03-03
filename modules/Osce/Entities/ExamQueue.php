<?php
/**
 * Created by PhpStorm.
 * User: zhouchong
 * Date: 2016/1/14 0014
 * Time: 15:19
 */
namespace Modules\Osce\Entities;

use DB;
use Doctrine\Common\Persistence\ObjectManager;
use Modules\Osce\Entities\Exam;

class ExamQueue extends CommonModel
{
    protected $connection = 'osce_mis';
    protected $table = 'exam_queue';
    public $timestamps = true;
    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $guarded = [];
    protected $hidden = [];
    protected $fillable = [
        'exam_id',
        'exam_screening_id',
        'student_id',
        'station_id',
        'room_id',
        'begin_dt',
        'end_dt',
        'status',
        'created_user_id'
    ];
    public $search = [];

    public function station()
    {
        return $this->hasOne('\Modules\Osce\Entities\Station', 'id', 'station_id');
    }

    public function room()
    {
        return $this->hasOne('\Modules\Osce\Entities\Room', 'id', 'room_id');
    }


    public function examScreening()
    {
        return $this->hasOne('\Modules\Osce\Entities\ExamScreening', 'id', 'exam_screening_id');
    }

    public function exam()
    {
        return $this->hasOne('\Modules\Osce\Entities\Exam', 'id', 'exam_id');
    }


    protected $statuValues = [
        0 => '抽完签',
        1 => '候考',
        2 => '正在考试',
        3 => '结束考试',
        4 => '缺考',
    ];

    public function student()
    {
        return $this->hasMany('\Modules\Osce\Entities\Student', 'id', 'student_id');
    }

    public function getStudent($mode, $exam_id)
    {
        $exam = Exam::find($exam_id);
        if ($mode == 1) {
            return $this->getWaitRoom($exam);

        } elseif ($mode == 2) {
            return $this->getWaitStation($exam);
        }

    }

    //获取候考教室
    protected function getWaitRoom($exam)
    {
        $examFlowRoomList = ExamFlowRoom::where('exam_id', '=', $exam->id)->paginate(config('osce.page_size'));
        $data = [];
        foreach ($examFlowRoomList as $examFlowRoom) {
            $roomName = $examFlowRoom->room->name;
            $room_id = $examFlowRoom->room_id;
//            $students = $examFlowRoom->queueStudent()->where('exam_id', '=', $exam->id)->get();
            $ExamQueue = new ExamQueue();
            $students = $ExamQueue->getWaitStudentRoom($room_id, $exam->id);
            foreach ($students as $examQueue) {
                foreach ($examQueue->student as $student) {
//                  $student->roomName=$roomName;
                    $data[$roomName][] = $student;
                }
            }
        }
        return $data;
    }

    //获取候考考站
    protected function getWaitStation($exam)
    {
        $examFlowStationList = ExamFlowStation::where('exam_id', '=', $exam->id)->paginate(config('osce.page_size'));
        $data = [];
        foreach ($examFlowStationList as $examFlowStation) {
            $stationName = $examFlowStation->station->name;
            $station_id = $examFlowStation->station_id;
            $ExamQueue = new ExamQueue();
            $students = $ExamQueue->getWaitStudentStation($station_id, $exam->id);
//            $students = $examFlowStation->queueStation()->where('exam_id', '=', $exam->id)->get();
            foreach ($students as $ExamQueue) {
                foreach ($ExamQueue->student as $student) {
//                   $student->stationName=$stationName;
                    $data[$stationName][] = $student;
                }
            }
        }

        return $data;
    }

    /**
     * 学生队列  腕表考试信息
     * @param $studentId
     * @return
     * @throws \Exception
     * @author zhouqiang
     */
    public function StudentExamQueue($studentId)
    {
//        $todayStart = date('Y-m-d 00:00:00');
//        $todayEnd = date('Y-m-d 23:59:59');
//        return ExamQueue::leftJoin('room', function ($join) {
//            $join->on('room.id', '=', 'exam_queue.room_id');
//
//        })->leftJoin('station', function ($join) {
//
//            $join->on('station.id', '=', 'exam_queue.station_id');
//
//        })->leftJoin('student', function ($join) {
//
//            $join->on('student.id', '=', 'exam_queue.student_id');
//        })
//            ->where($this->table . '.student_id', '=', $studentId)
//            ->whereRaw("UNIX_TIMESTAMP(exam_queue.begin_dt) > UNIX_TIMESTAMP('$todayStart')
//         AND UNIX_TIMESTAMP(exam_queue.end_dt) < UNIX_TIMESTAMP('$todayEnd')")
////            ->whereIn('exam_queue.status', [1, 2])
//            ->orderBy('begin_dt', 'asc')
//            ->select([
//                'room.name as room_name',
//                'student.name as name',
//                'exam_queue.begin_dt as begin_dt',
//                'exam_queue.end_dt as end_dt',
//                'exam_queue.room_id as room_id',
//                'exam_queue.station_id as station_id',
//                'exam_queue.status as status',
//                'exam_queue.id as id',
//                'station.mins as mins',
//                'exam_queue.exam_id as exam_id'
//            ])->get();
        return $this->where('student_id', '=', $studentId)
            ->orderBy('begin_dt', 'asc')
            ->get();
    }


    public function getPagination()
    {
        return $this->paginate(config('msc.page_size'));
    }

    /**
     * 鏍规嵁room_id鏉ヨ幏鍙栧搴旂殑鑰冪敓鍒楄〃
     * @param $room_id
     * @param $examId
     * @param $stationNum
     * @return
     * @throws \Exception
     * @author Jiangzhiheng
     */
    static public function examineeByRoomId($room_id, $examId, $stations)
    {
        try {
            return ExamQueue::leftJoin('student', 'student.id', '=', 'exam_queue.student_id')
                ->where('exam_queue.room_id', $room_id)
                ->where('exam_queue.status', '<', 3)
                ->where('student.exam_id', $examId)
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
                ->take(count($stations))
                ->get();
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    static public function examineeByStationId($stationId, $examId)
    {
        return ExamQueue::leftJoin('student', 'student.id', '=', 'exam_queue.student_id')
            ->where('exam_queue.station_id', $stationId)
            ->where('exam_queue.status', '<', 3)
            ->where('student.exam_id', $examId)
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
            ->take(1)
            ->get();
    }

    /**
     * 从队列里取出下一组考生的接口
     * @param $room_id
     * @param $stationNum
     * @return
     * @throws \Exception
     * @author Jiangzhiheng
     */
    static public function nextExamineeByRoomId($room_id, $examId, $station)
    {
        try {
            return ExamQueue::leftJoin('student', 'student.id', '=', 'exam_queue.student_id')
                ->where('exam_queue.room_id', $room_id)
                ->where('exam_queue.status', '<', 3)
                ->where('exam_queue.exam_id', $examId)
                ->skip(count($station))
                ->take(count($station))
                ->orderBy('exam_queue.begin_dt', 'asc')
                ->select(
                    'student.id as student_id',
                    'student.name as student_name',
                    'student.code as student_code'
                )
                ->groupBy('student.id')
                ->get();
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    static public function nextExamineeByStationId($stationId, $examId)
    {
        try {
            return ExamQueue::leftJoin('student', 'student.id', '=', 'exam_queue.student_id')
                ->where('exam_queue.station_id', $stationId)
                ->where('exam_queue.status', '<', 3)
                ->where('exam_queue.exam_id', $examId)
                ->orderBy('exam_queue.begin_dt', 'asc')
                ->skip(1)//TODO 可能要改
                ->take(1)
                ->select(
                    'student.id as student_id',
                    'student.name as student_name',
                    'student.code as student_code'
                )
                ->groupBy('student.id')
                ->get();
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    /**
     * 学生腕表信息 下一场考试信息判断
     * @param $room_id
     * @return
     * @throws \Exception
     * @author zhouqiang
     */
    public function nextQueue($examQueueCollect, $nowTime)
    {
        $nowQueue = $this->nowQueue($examQueueCollect, $nowTime);
//        dd($nowQueue);
        $queueLeave = [];
        foreach ($examQueueCollect as $examQueue) {

            if (strtotime($examQueue->begin_dt) > strtotime($nowQueue->end_dt)) {
                $queueLeave[$examQueue->begin_dt] = $examQueue;
            }
        }
        ksort($queueLeave);
        return array_shift($queueLeave);
    }

    /**
     * 开始考试时，改变时间和状态
     * @param  $studentId $stationId
     * @return
     * @throws  \Exception
     * @author  zhouqiang
     */

    public function AlterTimeStatus($studentId, $stationId, $nowTime)
    {
        //开启事务
        $connection = DB::connection($this->connection);
        $connection->beginTransaction();
        try {
            //拿到正在考的考试
            $exam = Exam::where('status', '=', 1)->first();

//                查询学生是否已开始考试
            $examQueue = ExamQueue::where('student_id', '=', $studentId)->where('station_id', '=', $stationId)->first();
            if ($examQueue->status == 2) {
                return true;
            }


//            $status = ExamQueue::where('student_id', '=', $studentId)->where('station_id', '=', $stationId)
            $status = $examQueue->update(['status' => 2]);

            if ($status) {
                $studentTimes = ExamQueue::where('student_id', '=', $studentId)
                    ->whereIn('exam_queue.status', [0, 2])
                    ->orderBy('begin_dt', 'asc')
                    ->get();

                $nowQueues  =   $studentTimes->where('exam_queue.status',2);
                $nowQueue   =   $nowQueues  ->  shift();
                $lateTime   =   $nowTime  - strtotime($nowQueue   ->  begin_dt);
                \Log::alert($lateTime.'迟到时间');

                foreach ($studentTimes as $key=>$item) {
                    if ($exam->sequence_mode == 2) {

                        $stationTime = $item->station->mins ? $item->station->mins : 0;
                        \Log::alert($stationTime.'以考站安排');
                    } else {
                        //这是已考场安排的需拿到room_id
                        $stationTime    =   $this   ->  getRoomStationMaxTime($item->room_id);
                        \Log::alert($stationTime.'以考场安排');
                    }

                    if ($nowTime > strtotime($item->begin_dt) + (config('osce.begin_dt_buffer') * 60)) {
                        if ($item->status == 2) {
                            $item->begin_dt = date('Y-m-d H:i:s', $nowTime);
                            $item->end_dt = date('Y-m-d H:i:s', $nowTime + $stationTime * 60);
                        } else {
                            $item->begin_dt = date('Y-m-d H:i:s', strtotime($item->begin_dt) + $lateTime);
                            $item->end_dt = date('Y-m-d H:i:s', strtotime($item->end_dt) + $lateTime);
                        }
                        if (!$item->save()) {
                            throw new \Exception('队列时间更新失败', -100);
                        }
                    } else {
                        //查询到考站的标准时间
                        $ExamTime = ExamQueue::where('student_id', '=', $studentId)->where('station_id', '=', $stationId)->first();
                        if (is_null($ExamTime)) {
                            throw new \Exception('没有找到对应的队列信息', -104);
                        }
                        $ExamTime->begin_dt = date('Y-m-d H:i:s', $nowTime);
                        $ExamTime->end_dt = date('Y-m-d H:i:s', $nowTime + $stationTime * 60);
                        if (!$ExamTime->save()) {
                            throw new \Exception('队列时间更新失败', -101);
                        }
                    }
                }
            } else {
                throw new \Exception('队列状态更新失败', -102);

            }
            $connection->commit();
            return true;
        } catch (\Exception $ex) {
            $connection->rollBack();
            throw $ex;
        }

    }

    private function getRoomStationMaxTime($roomdId)
    {
        $tempStations = RoomStation::where('room_id', '=', $roomdId)->get();
        $mins = 0;
        //循环数组，找到mins最大的值
        foreach ($tempStations as $v) {
            $station = $v->station;
            if (is_null($station)) {
                continue;
                //todo::暂时跳过不处理
            }
            $mins = $station->mins > $mins ? $station->mins : $mins;
        }
        return $mins;
    }

    /**
     * 结束考试时，改变时间和状态
     * @param  $studentId
     * @return
     * @throws  \Exception
     * @author  zhouqiang
     */
    public function EndExamAlterStatus($studentId, $stationId, $nowTime)
    {
        $nowTime = date('Y-m-d H:i:s', $nowTime);
        $endExam = ExamQueue::where('student_id', '=', $studentId)
            ->where('station_id', '=', $stationId)
            ->update(['end_dt' => $nowTime, 'status' => 3]);
        return $endExam;
    }


    /**
     * 将数据写入exam_queue
     * @param $examId
     * @param $studentId
     * @param $time  当前时间戳
     * @param $examScreeningId
     * @throws \Exception
     * @author Jiangzhiheng
     */
    public function createExamQueue($examId, $studentId, $time, $examScreeningId)
    {
        try {
            //先查看exam_queue表中是否已经有了数据，防止脏数据
            $examObj = ExamQueue::where('exam_id', $examId)
                ->where('student_id', $studentId)
                ->orderBy('begin_dt', 'asc')
                ->get();
            if ($examObj->isEmpty()) {
                //通过$examId, $studentId还有$examScreeningId在plan表中找到对应的数据
                $objs = ExamPlan::where('exam_id', $examId)
                    ->where('student_id', $studentId)
                    ->orderBy('begin_dt', 'asc')
                    ->get();
                if ($objs->isEmpty()) {
                    throw new \Exception('该学生的考试场次有误，请核实！');
                }
                //将当前的时间与计划表的时间减去缓冲时间做对比，如果是比计划的时间小，就直接用计划的时间。
                //如果时间戳比计划表的时间大，就用当前的时间加上缓冲时间
                //config('osce.begin_dt_buffer')为缓冲时间
                //获得当前时间比计划时间晚了多少
                //$difference = $time - (strtotime($objs[0]->begin_dt) - (config('osce.begin_dt_buffer') * 60));


                $examScreening = ExamScreening::find($examScreeningId);
                if (!$examScreening->real_start_dt) {
                    $nowTime = $time;
                    if (strtotime($examScreening->begin_dt) <= $nowTime) {
                        $examScreening->real_start_dt = date('Y-m-d H:i:s', $nowTime);
                        if (!$examScreening->save()) {
                            throw new \Exception('开始考试失败');
                        }
                    }
                }
                $difference = strtotime($examScreening->real_start_dt) - strtotime($examScreening->begin_dt);
                if ($difference < 0) {
                    $difference = 0;
                }
                foreach ($objs as $item) {
                    if ($difference > 0) {
                        $item->begin_dt = date('Y-m-d H:i:s', strtotime($item->begin_dt) + $difference);
                        $item->end_dt = date('Y-m-d H:i:s', strtotime($item->end_dt) + $difference);
                    }

                    $item->status = 0;

                    //将数据插入数据库
                    if (!ExamQueue::create($item->toArray())) {
                        throw new \Exception('该名学生的与腕表的录入失败！');
                    };
                }
            }

        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    /**
     * 通过学生id找到对应的队列实例
     * @param $studentId
     * @return
     * @throws \Exception
     * @author Jiangzhiheng
     */
    static public function findQueueIdByStudentId($studentId, $stationId)
    {
        try {
            //通过学生id找到对应的examScreeningStudent实例
            $examScreening = ExamScreeningStudent::where('student_id', $studentId)->first();

            if (is_null($examScreening)) {
                throw new \Exception('没找到对应的学生编号', 2100);
            }

            //拿到$examScreeningId和$studentId
            $examScreeningId = $examScreening->exam_screening_id;
            //得到queue实例
            if (is_null($stationId)) {
                $queue = ExamQueue::where('student_id', $studentId)
                    ->where('exam_screening_id', $examScreeningId)
                    ->where('status', 2)
                    ->first();
            } else {
                $queue = ExamQueue::where('student_id', $studentId)
                    ->where('station_id', $stationId)
                    ->where('exam_screening_id', $examScreeningId)
                    ->first();
            }
            if (is_null($queue)) {
                throw new \Exception('没有找到符合要求的学生', 2200);
            }

            return $queue;
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    /**
     * 通过考试考场id获取候考学生
     * @param $station_id $exam_id
     * @return
     * @throws \Exception
     * @author zhouchong
     */
    public function getWaitStudentStation($station_id = '', $exam_id = '')
    {

        $builder = $this->leftJoin('exam_flow_station',
            function ($join) {
                $join->on('exam_queue.station_id', '=', 'exam_flow_station.station_id');
            })->leftJoin('student',
            function ($join) {
                $join->on('student.id', '=', 'exam_queue.student_id');
            })->where('exam_queue.station_id', '=', $station_id)->where('exam_queue.exam_id', '=',
            $exam_id)->where('exam_queue.status', '=', 0)
            ->orderBy('begin_dt', 'asc')
            ->select([
                'student.name as name',
                'student.id as student_id',
            ])->distinct()->take(4)->get();

        return $builder;
    }


    /**
     * 通过考试考场id获取候考学生
     * @param $room_id $exam_id
     * @return
     * @throws \Exception
     * @author zhouchong
     */
    public function getWaitStudentRoom($room_id = '', $exam_id = '')
    {

        $builder = $this->leftJoin('exam_flow_room',
            function ($join) {
                $join->on('exam_queue.room_id', '=', 'exam_flow_room.room_id');
            })->leftJoin('student',
            function ($join) {
                $join->on('student.id', '=', 'exam_queue.student_id');
            })->where('exam_queue.room_id', '=', $room_id)->where('exam_queue.exam_id', '=',
            $exam_id)->where('exam_queue.status', '=', 0)
            ->orderBy('begin_dt', 'asc')
            ->select([
                'student.name as name',
                'student.id as student_id',
            ])->distinct()->take(4)->get();
        return $builder;
    }


    /**
     * 结束学生队列考试
     * @param $studentId 学生id
     * @param null $stationId
     * @return Object 返回队列表对应的对象
     * @throws \Exception
     * @author Jiangzhiheng
     */

    static public function endStudentQueueExam($studentId, $stationId = null)
    {
        try {
            //获取当前的服务器时间
            $date = date('Y-m-d H:i:s');

            //找到对应的方法找到queue实例
            $queue = ExamQueue::findQueueIdByStudentId($studentId, $stationId);

            /*
             * 判断status状态
             * 如果是2的话，就说明是第一次访问，修改状态
             * 如果是3，就是明是重复访问，返回已经修改过的值
             */
            if ($queue->status == 2) {
                //修改状态
                $queue->status = 3;
                $queue->end_dt = $date;
                if (!$queue->save()) {
                    throw new \Exception('状态修改失败！请重试', 2000);
                }
                return $queue;
            } elseif ($queue->status == 3) {
                return $queue;
            } else {
                throw new \Exception('系统错误，请重试', -888);
            }
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

}