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
use Modules\Osce\Http\Controllers\CommonController;
use Modules\Osce\Repositories\Common;

class ExamQueue extends CommonModel
{
    protected $connection   = 'osce_mis';
    protected $table        = 'exam_queue';
    public    $timestamps   = true;
    protected $primaryKey   = 'id';
    public    $incrementing = true;
    protected $guarded      = [];
    protected $hidden       = [];
    protected $fillable     = [
        'exam_id', 'exam_screening_id', 'student_id', 'station_id',
        'room_id', 'begin_dt', 'end_dt', 'status', 'created_user_id',
        'flow_id', 'serialnumber', 'group', 'blocking',
        'gradation_order', 'controlMark', 'next_num'
    ];

    public $search          = [];

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
//        $exam = Exam::find($exam_id);
        if ($mode == 1) {
            return $this->getWaitRoom($exam_id);

        } elseif ($mode == 2) {
            return $this->getWaitStation($exam_id);
        }

    }

    //获取候考教室
    protected function getWaitRoom($exam_id)
    {
        $examFlowRoomList = ExamFlowRoom::where('exam_id', '=', $exam_id)->paginate(config('osce.page_size'));
        $data = [];
        foreach ($examFlowRoomList as $examFlowRoom) {
            $roomName = $examFlowRoom->room->name;
            $room_id = $examFlowRoom->room_id;
//            $students = $examFlowRoom->queueStudent()->where('exam_id', '=', $exam->id)->get();
            $ExamQueue = new ExamQueue();
            $students = $ExamQueue->getWaitStudentRoom($room_id, $exam_id);
            foreach ($students as $examQueue) {
                foreach ($examQueue->student as $student) {
                    $data[$roomName][] = $student;
                }
            }
        }

        return $data;
    }

    //获取候考考站
    protected function getWaitStation($exam_id)
    {
        $examFlowStationList = ExamFlowStation::where('exam_id', '=', $exam_id)->paginate(config('osce.page_size'));
        $data = [];
        foreach ($examFlowStationList as $examFlowStation) {
            $stationName = $examFlowStation->station->name;
            $station_id = $examFlowStation->station_id;
            $ExamQueue = new ExamQueue();
            $students = $ExamQueue->getWaitStudentStation($station_id, $exam_id);
//            $students = $examFlowStation->queueStation()->where('exam_id', '=', $exam->id)->get();
            foreach ($students as $ExamQueue) {
                foreach ($ExamQueue->student as $student) {
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
    public function StudentExamQueue($studentId,$examscreeningId)
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
        if(!empty($examscreeningId)){

            return $this->where('student_id', '=', $studentId)
                ->whereIn('exam_screening_id',$examscreeningId)
                ->orderBy('begin_dt', 'asc')
                ->get();
        }else{
            return $this->where('student_id', '=', $studentId)
                ->orderBy('begin_dt', 'asc')
                ->get();
        }

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
    static public function examineeByRoomId($room_id, $examId, $stations,$exam_screening_id)
    {
        try {

//                $ExamDraftFlow=ExamDraftFlow::leftJoin('exam_draft','exam_draft_flow.id','=','exam_draft.exam_draft_flow_id')
//                    ->leftJoin('exam_gradation','exam_gradation.id','=','exam_draft_flow.exam_gradation_id')
//                ->where('exam_draft.room_id',$room_id)
//                ->where('exam_draft_flow.exam_id',$examId)
//                ->where('exam_gradation.exam_id',$examId)
//                ->first();

            $queueing=ExamQueue::leftJoin('student', 'student.id', '=', 'exam_queue.student_id')
                ->where('exam_queue.room_id', $room_id)
                ->where('exam_queue.status', '=', 2)
                ->where('student.exam_id', $examId)
                ->where('exam_queue.exam_screening_id',$exam_screening_id)
                ->where('exam_queue.blocking', 0)
                ->groupBy('student.id')
               ->first();
            if(is_null($queueing)){//没有正在考试的
                return ExamQueue::leftJoin('student', 'student.id', '=', 'exam_queue.student_id')
                    ->where('exam_queue.room_id', $room_id)
                    ->where('exam_queue.status', '<', 3)
                    ->where('exam_queue.exam_id', '=',$examId)
                    ->where('student.exam_id', $examId)
                    ->where('exam_queue.exam_screening_id',$exam_screening_id)
                    ->where('exam_queue.blocking', 1)
                    ->select(
                        'student.id as student_id',
                        'student.name as student_name',
                        'student.user_id as student_user_id',
                        'student.idcard as student_idcard',
                        'student.mobile as student_mobile',
                        'student.code as student_code',
                        'student.avator as student_avator',
                        'student.description as student_description',
                        'exam_queue.id as exam_queue_id',
                        'exam_queue.room_id as room_id',
                        'exam_queue.station_id as station_id'
                    )
                    ->orderBy('exam_queue.next_num', 'asc')
                    ->orderBy('exam_queue.begin_dt', 'asc')
                    ->orderBy('exam_queue.updated_at', 'asc')
                    ->groupBy('student.id')
                    ->take(count($stations))
                    ->get();
            }else{//不正常中断存在在考试的学生
                return ExamQueue::leftJoin('student', 'student.id', '=', 'exam_queue.student_id')
                    ->where('exam_queue.room_id', $room_id)
                    ->where('exam_queue.status', '<', 3)
                    ->where('exam_queue.exam_id', '=',$examId)
                    ->where('exam_queue.exam_screening_id',$exam_screening_id)
                    ->where('student.exam_id', $examId)
                    ->select(
                        'student.id as student_id',
                        'student.name as student_name',
                        'student.user_id as student_user_id',
                        'student.idcard as student_idcard',
                        'student.mobile as student_mobile',
                        'student.code as student_code',
                        'student.avator as student_avator',
                        'student.description as student_description',
                        'exam_queue.id as exam_queue_id',
                        'exam_queue.room_id as room_id',
                        'exam_queue.station_id as station_id'
                    )
                    ->orderBy('exam_queue.next_num', 'asc')
                    ->orderBy('exam_queue.begin_dt', 'asc')
                    ->orderBy('exam_queue.updated_at', 'asc')
                    ->groupBy('student.id')
                    ->take(count($stations))
                    ->get();
            }


        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    static public function examineeByStationId($stationId, $examId,$exam_screening_id)
    {
//        $ExamDraftFlow=ExamDraftFlow::leftJoin('exam_draft','exam_draft_flow.id','=','exam_draft.exam_draft_flow_id')
//            ->where('exam_draft.station_id',$stationId)
//            ->where('exam_draft_flow.exam_id',$examId)
//            ->first();
        $queueing=ExamQueue::leftJoin('student', 'student.id', '=', 'exam_queue.student_id')
            ->where('exam_queue.station_id', $stationId)
            ->where('exam_queue.status', '=', 2)
            ->where('exam_queue.exam_screening_id',$exam_screening_id)
            ->where('student.exam_id', $examId)
           ->first();
        if(is_null($queueing)) {//没有正在考试的
            return ExamQueue::leftJoin('student', 'student.id', '=', 'exam_queue.student_id')
                ->where('exam_queue.station_id', $stationId)
                ->where('exam_queue.status', '<', 3)
                ->where('student.exam_id', $examId)
                ->where('exam_queue.exam_screening_id',$exam_screening_id)
                ->where('exam_queue.blocking', 1)
                ->select(
                    'student.id as student_id',
                    'student.name as student_name',
                    'student.user_id as student_user_id',
                    'student.idcard as student_idcard',
                    'student.mobile as student_mobile',
                    'student.code as student_code',
                    'student.avator as student_avator',
                    'student.description as student_description',
                    'exam_queue.room_id as room_id',
                    'exam_queue.station_id as station_id',
                    'exam_queue.id as exam_queue_id'
                )
                ->orderBy('exam_queue.next_num', 'asc')
                ->orderBy('exam_queue.begin_dt', 'asc')
                ->orderBy('exam_queue.updated_at', 'asc')
                ->take(1)
                ->get();
        }else{
            return ExamQueue::leftJoin('student', 'student.id', '=', 'exam_queue.student_id')
                ->where('exam_queue.station_id', $stationId)
                ->where('exam_queue.status', '<', 3)
                ->where('exam_queue.exam_screening_id',$exam_screening_id)
                ->where('student.exam_id', $examId)
                ->select(
                    'student.id as student_id',
                    'student.name as student_name',
                    'student.user_id as student_user_id',
                    'student.idcard as student_idcard',
                    'student.mobile as student_mobile',
                    'student.code as student_code',
                    'student.avator as student_avator',
                    'student.description as student_description',
                    'exam_queue.room_id as room_id',
                    'exam_queue.station_id as station_id',
                    'exam_queue.id as exam_queue_id'
                )
                ->orderBy('exam_queue.next_num', 'asc')
                ->orderBy('exam_queue.begin_dt', 'asc')
                ->orderBy('exam_queue.updated_at', 'asc')
                ->take(1)
                ->get();
        }

    }

    /**
     * 从队列里取出下一组考生的接口
     * @param $room_id
     * @param $examId
     * @param $station
     * @return
     * @throws \Exception
     * @author Jiangzhiheng
     */
    static public function nextExamineeByRoomId($room_id, $examId, $station,$exam_screening_id)
    {
        try {
            $ExamDraftFlow=ExamDraftFlow::leftJoin('exam_draft','exam_draft_flow.id','=','exam_draft.exam_draft_flow_id')
                ->where('exam_draft.room_id',$room_id)
                ->where('exam_draft_flow.exam_id',$examId)
                ->first();
            return ExamQueue::leftJoin('student', 'student.id', '=', 'exam_queue.student_id')
                ->where('exam_queue.room_id', $room_id)
                ->where('exam_queue.status', '<', 3)
                ->where('exam_queue.exam_id', $examId)
                ->where('exam_queue.exam_screening_id', $exam_screening_id)
                ->where('exam_queue.blocking', 1)
                ->skip(count($station))
                ->take(count($station))
                ->orderBy('exam_queue.next_num', 'asc')
                ->orderBy('exam_queue.begin_dt', 'asc')
                ->orderBy('exam_queue.updated_at', 'asc')
                ->select(
                    'student.id as student_id',
                    'student.name as student_name',
                    'student.code as student_code',
                    'exam_queue.room_id as room_id',
                    'exam_queue.station_id as station_id'
                )
                ->groupBy('student.id')
                ->get();
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    static public function nextExamineeByStationId($stationId, $examId,$exam_screening_id)
    {
        try {
            $ExamDraftFlow=ExamDraftFlow::leftJoin('exam_draft','exam_draft_flow.id','=','exam_draft.exam_draft_flow_id')
                ->where('exam_draft.station_id',$stationId)
                ->where('exam_draft_flow.exam_id',$examId)
                ->first();
            return ExamQueue::leftJoin('student', 'student.id', '=', 'exam_queue.student_id')
                ->where('exam_queue.station_id', $stationId)
                ->where('exam_queue.status', '<', 3)
                ->where('exam_queue.exam_id', $examId)
                ->where('exam_queue.blocking', 1)
                ->where('exam_queue.exam_screening_id', $exam_screening_id)
                ->orderBy('exam_queue.next_num', 'asc')
                ->orderBy('exam_queue.begin_dt', 'asc')
                ->orderBy('exam_queue.updated_at', 'asc')
                ->skip(1)//TODO 可能要改
                ->take(1)
                ->select(
                    'student.id as student_id',
                    'student.name as student_name',
                    'student.code as student_code',
                    'exam_queue.room_id as room_id',
                    'exam_queue.station_id as station_id'
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
     * @param $studentId $stationId
     * @param $stationId
     * @param $nowTime
     * @param $teacherId
     * @return bool
     * @throws \Exception
     * @author  zhouqiang
     */

    public function AlterTimeStatus($studentId, $stationId, $nowTime,$teacherId)
    {
        //开启事务
        $connection = DB::connection($this->connection);
        $connection->beginTransaction();
        try {
            //拿到正在考的考试
            $exam = Exam::where('status', '=', 1)->first();

            //查询学生是否已开始考试
            //dd($studentId);
            $examQueue = ExamQueue::where('student_id', '=', $studentId)
                ->where('exam_id', '=', $exam->id)
                ->where('station_id', '=', $stationId)
                ->whereIn('status', [0,1,2])
                ->first();
            //dd($examQueue);
            if(is_null($examQueue)){
                throw new \Exception('该学生还没有抽签', -105);
            }
            if ($examQueue->status == 2) {
                return true;
            }
            //修改队列状态
            $examQueue->status=2;
            //$examQueue->stick=null;
            if ($examQueue->save()) {
                    ExamQueue::where('student_id', '=', $studentId)->where('exam_id',$exam->id)->update(['blocking'=>0]);//设置阻塞
                $studentTimes = ExamQueue::where('student_id', '=', $studentId)
                    ->whereIn('exam_queue.status', [0,1, 2])
                    ->orderBy('begin_dt', 'asc')
                    ->get();
                $nowQueue = null;
                foreach ($studentTimes as $stationTime) {
                    if ($stationTime->status == 2) {
                        $nowQueue = $stationTime;
                        break;
                    }
                }
                if (is_null($nowQueue)) {
                    throw new \Exception('进入考试失败', -103);
                }
                $lateTime = $nowTime - strtotime($nowQueue->begin_dt);
                //判断考生的迟到时间
                if($lateTime<0){
                    $lateTime=0;
                }
                //拿到状态为三的队列
                $endQueue =ExamQueue::where('exam_id','=',$exam->id)
                    ->where('student_id', '=', $studentId)
                    ->where('status','=',3)
                    ->get();

                foreach ($studentTimes as $key => $item) {
                    foreach($endQueue as $endQueueTime){
                       if( strtotime($endQueueTime->begin_dt)>strtotime($item->begin_dt)){
                           throw new \Exception('当前队列开始时间不正确',-104);
                       }
                    }
                    //考试排序模式
                    if ($exam->sequence_mode == 2) {
                        $stationTime = $item->station->mins ? $item->station->mins : 0;
                    } else {
                        //这是已考场安排的需拿到room_id
                        $stationTime = $this->getRoomStationMaxTime($item->room_id);
                    }

                    if ($nowTime > strtotime($item->begin_dt) + (config('osce.begin_dt_buffer') * 60)) {
                        if ($item->status == 2) {
                            $item->begin_dt = date('Y-m-d H:i:s', $nowTime);
                            $item->end_dt = date('Y-m-d H:i:s', $nowTime + $stationTime * 60);
                        } else {

                            $item->begin_dt = date('Y-m-d H:i:s', strtotime($item->begin_dt) + $lateTime);
                            $item->end_dt = date('Y-m-d H:i:s', strtotime($item->end_dt) + $lateTime);
                        }
                        \Log::info('begin_exam', ['begin_dt' => $item->begin_dt, 'end_dt' => $item->end_dt]);
                        if (!$item->save()) {
                            throw new \Exception('队列时间更新失败', -100);
                        }
                    } else {
                        //查询到考站的标准时间
                        $ExamTime = ExamQueue::where('student_id', '=', $studentId)
                            ->where('exam_id',$exam->id)
                            ->where('station_id', '=', $stationId)
                            ->where('status', '=', 2)
                            ->first();
                        if (is_null($ExamTime)) {
                            throw new \Exception('没有找到对应的队列信息', -102);
                        }
                        $ExamTime->begin_dt = date('Y-m-d H:i:s', $nowTime);
                        $ExamTime->end_dt = date('Y-m-d H:i:s', $nowTime + $stationTime * 60);
                        if (!$ExamTime->save()) {
                            throw new \Exception('队列时间更新失败', -100);
                        }
                    }
                }
            } else {
                throw new \Exception('队列状态更新失败', -101);

            }
            // 调用锚点方法
//            CommonController::storeAnchor($stationId, $studentId, $exam->id, $teacherId, [$nowTime]);
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
                        ->orderBy('begin_dt', 'asc')->get();
            
            if ($examObj->isEmpty()) {
                //通过$examId, $studentId还有$examScreeningId在plan表中找到对应的数据
                $examPlan = ExamPlan::where('exam_id', '=', $examId)
                                    ->where('student_id', '=', $studentId)
                                    ->orderBy('begin_dt', 'asc')->get();

                if ($examPlan->isEmpty()) {
                    throw new \Exception('该学生的考试场次有误，请核实！');
                }
                //将当前的时间与计划表的时间减去缓冲时间做对比，如果是比计划的时间小，就直接用计划的时间。
                //如果时间戳比计划表的时间大，就用当前的时间加上缓冲时间
                //config('osce.begin_dt_buffer')为缓冲时间
                //获得当前时间比计划时间晚了多少
                //$difference = $time - (strtotime($examPlan[0]->begin_dt) - (config('osce.begin_dt_buffer') * 60));


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
                //循环创建考试队列
                foreach ($examPlan as $item) {
                    if ($difference > 0) {
                        $item->begin_dt = date('Y-m-d H:i:s', strtotime($item->begin_dt) + $difference);
                        $item->end_dt = date('Y-m-d H:i:s', strtotime($item->end_dt) + $difference);
                    }
                    $item->status = 0;//抽完签

                    $data = [
                        "exam_id"           => $item->exam_id,
                        "exam_screening_id" => $item->exam_screening_id,
                        "student_id"        => $item->student_id,
                        "station_id"        => $item->station_id,
                        "room_id"           => $item->room_id,
                        "gradation_order"   => $item->gradation_order,
                        "begin_dt"          => $item->begin_dt,
                        "end_dt"            => $item->end_dt,
                        "status"            => $item->status,
                        "created_user_id"   => $item->created_user_id,
                        "flow_id"           => $item->flow_id,
                        "serialnumber"      => $item->serialnumber,
                        "group"             => $item->group,
                    ];
                    //将数据插入数据库
                    $result = ExamQueue::create($data);

                    if (!$result) {
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
     * @param $stationId
     * @return
     * @throws \Exception
     * @author Jiangzhiheng
     */
    static public function findQueueIdByStudentId($studentId, $stationId)
    {
        try {
            //修改场次状态
            $examId =Exam::doingExam();
            $examScreeningModel = new ExamScreening();
            $examScreening      = $examScreeningModel -> getExamingScreening($examId->id);
            if(is_null($examScreening))
            {
                $examScreening  = $examScreeningModel -> getNearestScreening($examId->id);
                $examScreening  ->status = 1;
                //场次开考（场次状态变为1）
                if(!$examScreening -> save())
                {
                    throw new \Exception('场次开考失败！');
                }
            }
            $exam_screen_id = $examScreening->id;


            //通过学生id找到对应的examScreeningStudent实例
            $examScreening = ExamScreeningStudent::where('student_id', $studentId)->where('exam_screening_id','=', $exam_screen_id)->first();

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
            })
            ->leftJoin('student', function ($join) {
                $join->on('student.id', '=', 'exam_queue.student_id');
            })
            ->where('exam_queue.station_id', '=', $station_id)
            ->where('exam_queue.exam_id', '=', $exam_id)
            ->where('exam_queue.status', '=', 0)
            ->orderBy('exam_queue.begin_dt', 'asc')
            ->orderBy('student.id', 'asc')
            ->select(['student.name as name', 'exam_queue.student_id', 'exam_queue.begin_dt'])
            ->distinct()->take(4)->get();

        if(count($builder) != 0){
            foreach ($builder as &$item) {
                //获取同一个人，在一场考试队列中是否有更早的考试
                $result = $this ->where('exam_id', '=', $exam_id)->where('student_id', '=', $item->student_id)->where('status', '=', 0)
                                ->whereRaw('unix_timestamp(begin_dt) < ?', [strtotime($item->begin_dt)])->first();
                if($result){
                    $item->name = '';
                }
            }
        }

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
            })
            ->leftJoin('student', function ($join) {
                $join->on('student.id', '=', 'exam_queue.student_id');
            })
            ->where('exam_queue.room_id', '=', $room_id)
            ->where('exam_queue.exam_id', '=', $exam_id)
            ->where('exam_queue.status', '=', 0)
            ->orderBy('exam_queue.begin_dt', 'asc')
            ->orderBy('student.id', 'asc')
            ->select(['student.name as name', 'exam_queue.student_id', 'exam_queue.begin_dt'])
            ->distinct()->take(4)->get();

        if(count($builder) != 0){
            foreach ($builder as &$item) {
                //获取同一个人，在一场考试队列中是否有更早的考试
                $result = $this ->where('exam_id', '=', $exam_id)->where('student_id', '=', $item->student_id)->where('status', '=', 0)
                                ->whereRaw('unix_timestamp(begin_dt) < ?', [strtotime($item->begin_dt)])->first();
                if($result){
                    $item->name = '';
                }
            }
        }

        return $builder;
    }


    /**
     * 结束学生队列考试
     * @param $studentId 学生id
     * @param null $stationId 考站id
     * @param null $teacherId 教师id
     * @return Object 返回队列表对应的对象
     * @throws \Exception
     * @author Jiangzhiheng
     */

    static public function endStudentQueueExam($studentId, $stationId = null, $teacherId = null)
    {

        $connection = \DB::connection('osce_mis');
        $connection->beginTransaction();
        try {
            //获取当前的服务器时间
            $date = date('Y-m-d H:i:s');

            //找到对应的方法找到queue实例
            $queue = ExamQueue::findQueueIdByStudentId($studentId, $stationId);

            //拿到阶段序号
            $gradationOrder = ExamScreening::find($queue->exam_screening_id);


            //拿到阶段id
            $examGradationId = ExamGradation::where('exam_id',$queue->exam_id)->where('order',$gradationOrder)->first();

            if(!empty($examGradationId)){
                $examDraftFlowData = ExamDraftFlow::where('exam_id',$queue->exam_id)->where('exam_gradation_id',$examGradationId->id)->where('optional',0)->first();
                //如果有选考，更新所有状态
                if(!empty($examDraftFlowData)){
                    //拿到属于该场考试该阶段的所有场次id
                    $examscreeningId = ExamScreening::where('exam_id','=',$queue->exam_id)->where('gradation_order','=',$gradationOrder->gradation_order)->get()->pluck('id');
                    $data = array(
                        'status' =>3,
                        'end_dt' =>$date,
                        'blocking' =>1,
                    );
                    //更新属于该阶段的所有考生状态
                    $result = ExamQueue::where('student_id', $studentId)
                        ->whereIn('exam_screening_id', $examscreeningId)
                        ->update($data);
                    if (!$result) {
                        throw new \Exception('状态修改失败！请重试', 2000);
                    }
                }
            }

            /*
             * 判断status状态
             * 如果是2的话，就说明是第一次访问，修改状态
             * 如果是3，就是明是重复访问，返回已经修改过的值
             */
            if ($queue->status == 2) {
                //修改状态
                $queue->status = 3;
                $queue->end_dt = $date;
                $result = $queue->save();
                if (!$result) {
                    throw new \Exception('状态修改失败！请重试', 2000);
                } else {
                    /*
                     * 将考试结束的时间写进锚点表里
                     */
//                    CommonController::storeAnchor($queue->station_id, $queue->student_id, $queue->exam_id,
//                        $teacherId, [strtotime($date)]);

                    //将该学生的阻塞状态变成1
                    if (!ExamQueue::where('exam_id', $queue->exam_id)
                        ->where('student_id', $studentId)
                        ->update(['blocking' => 1])
                    ) {
                        throw new \Exception('阻塞状态修改失败！请重试', -2);
                    }
                }
                $connection->commit();
                return $queue;
            } elseif ($queue->status == 3) { //通过传入的station_id进行多次点击结束考试的适配
                return $queue;
            } else {
                throw new \Exception('系统错误，请重试', -888);
            }

        } catch (\Exception $ex) {
            $connection->rollBack();
            throw $ex;
        }
    }

    /**
     * 获取 考站/考场 分页
     */
    public function getPageSize($exam_id, $pageSize = 4)
    {
        return $this->where('exam_id', $exam_id)->groupBy('station_id')->paginate($pageSize);
    }

    /**
     * 获取候考考站对应学生列表
     */
    public function getWaitStationStudents($exam_id, $pageSize = 4)
    {
        $examFlowStationList = ExamFlowStation::where('exam_id', '=', $exam_id)->paginate($pageSize);
        $data = [];
        foreach ($examFlowStationList as $examFlowStation) {
            $stationName = $examFlowStation->station->name;
            $station_id  = $examFlowStation->station_id;
            $ExamQueue   = new ExamQueue();
            $students    = $ExamQueue->getWaitStudentStation($station_id, $exam_id);
            foreach ($students as $ExamQueue) {
                foreach ($ExamQueue->student as $student) {
                    if($ExamQueue->name == ''){
                        $student->name = '';
                    }
                    $data[$stationName]['name']      = $stationName;
                    $data[$stationName]['student'][] = $student;
                }
            }
        }
        $data = array_values($data);

        return $data;
    }

    /**
     * 获取候考考场对应学生列表
     */
    public function getWaitRoomStudents($exam_id, $pageSize = 4)
    {
        $examFlowRoomList = ExamFlowRoom::where('exam_id', '=', $exam_id)->paginate($pageSize);
        $data = [];
        foreach ($examFlowRoomList as $examFlowRoom)
        {
            $roomName = $examFlowRoom->room->name;
            $room_id  = $examFlowRoom->room_id;
            $ExamQueue= new ExamQueue();
            $students = $ExamQueue->getWaitStudentRoom($room_id, $exam_id);
            foreach ($students as $examQueue) {
                foreach ($examQueue->student as $student) {
                    if($examQueue->name == ''){
                        $student->name = '';
                    }
                    $data[$roomName]['name']      = $roomName;
                    $data[$roomName]['student'][] = $student;
                }
            }
        }
        $data = array_values($data);

        return $data;
    }


    //exam_station
    public function examstation(){
        return $this->hasOne('Modules\Osce\Entities\ExamStation', 'station_id', 'station_id');
    }



    //查找学生队列中的考试
    public function getExamingData($examId,$studentId){
        $builder = $this->whereIn('exam_queue.exam_id',$examId)->where('exam_queue.student_id',$studentId)->where('station.type','=',3)->leftjoin('exam',function($exam){
            $exam->on('exam.id','=','exam_queue.exam_id');
        })->leftjoin('station',function($exam){
            $exam->on('station.id','=','exam_queue.station_id');
        })->select('exam.id','exam.name','exam_queue.station_id','exam_queue.status','exam_queue.room_id')->get();

        return $builder;
    }

    //获取考生的考站数量
    public function getStationNum($studentId){
        $DB = \DB::connection('osce_mis');
        $builder = $this->where('student_id','=',$studentId)->where('status','!=',3)->select(
            $DB->raw('count(station_id) as station_num')
        )->first();
        return $builder;
    }

    //查看学生当前状态
    public function
    getExamineeStatus($examing,$studentId){
        $builder = $this->where('exam_id','=',$examing)->where('student_id','=',$studentId)->first();
        return $builder;
    }
}