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
use Modules\Osce\Entities\QuestionBankEntities\ExamPaper;
use Modules\Osce\Http\Controllers\CommonController;
use Modules\Osce\Repositories\Common;
use Symfony\Component\VarDumper\Dumper\DataDumperInterface;

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
        'exam_id', 'exam_screening_id', 'student_id', 'station_id',
        'room_id', 'begin_dt', 'end_dt', 'status', 'created_user_id',
        'flow_id', 'serialnumber', 'group', 'blocking',
        'gradation_order', 'controlMark', 'next_num'
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

    public function student()
    {
        return $this->hasOne('\Modules\Osce\Entities\Student', 'id', 'student_id');
    }

    public function scopeUsedStations($query, $screenId, $roomId)
    {
        return $query->where($this->table . '.exam_screening_id', $screenId)
            ->where($this->table . '.room_id', $roomId)
            ->whereNotIn('status', [0, 3]);

    }

    protected $statuValues = [
        0 => '绑定腕表',
        1 => '抽签',
        2 => '正在考试',
        3 => '结束考试',
        4 => '缺考',
    ];

    public function getStudent($mode, $exam_id)
    {
        $ExamQueue  = new ExamQueue();
        //获取对应场次
        $screeningId = $ExamQueue->getExamScreeningId($exam_id);

        switch ($mode){
            case 1: return $this->getWaitRoom($exam_id, $screeningId);
                    break;
            case 2: return $this->getWaitStation($exam_id, $screeningId);
                    break;
            default: throw new \Exception('没有对应的考试模式');
        }
    }

    //获取候考教室
    protected function getWaitRoom($exam_id, $screeningId)
    {
        $exam_id = intval($exam_id);
        $pageSize= config('osce.page.size')? : 4;
        //获取到该考试阶段下场次下所有的房间
        $examRoomList = ExamDraft::leftJoin('exam_draft_flow', 'exam_draft_flow.id', '=', 'exam_draft.exam_draft_flow_id')
                                 ->where('exam_draft_flow.exam_id', '=', $exam_id)
                                 ->groupBy('exam_draft.room_id')
                                 ->paginate($pageSize);
        $data = [];
        foreach ($examRoomList as $examFlowRoom)
        {
            $roomName  = $examFlowRoom->room->name;
            $room_id   = $examFlowRoom->room_id;
            $ExamQueue = new ExamQueue();
            $students  = $ExamQueue->getWaitStudentRoom($room_id, $exam_id, $screeningId);

            foreach ($students as $examQueue) {
                foreach ($examQueue->student as $student) {
                    $data[$roomName][] = $student;
                }
            }
        }

        return $data;
    }

    //获取候考考站
    protected function getWaitStation($exam_id, $screeningId)
    {
        $examRoomList = ExamDraft::leftJoin('exam_draft_flow', 'exam_draft_flow.id', '=', 'exam_draft.exam_draft_flow_id')
                        ->where('exam_draft_flow.exam_id', '=',$exam_id)
                        ->groupBy('exam_draft.station_id')
                        ->paginate(config('osce.page.size'));
        $data = [];
        foreach ($examRoomList as $examFlowStation)
        {
            $stationName = $examFlowStation->station->name;
            $station_id  = $examFlowStation->station_id;
            $ExamQueue   = new ExamQueue();
            $students    = $ExamQueue->getWaitStudentStation($station_id, $exam_id, $screeningId);
            foreach ($students as $ExamQueue)
            {
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
    public function StudentExamQueue($studentId, $examscreeningId)
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

        $exam = Exam::doingExam();
        $examScreeningModel = new ExamScreening();
        $examScreening = $examScreeningModel->getExamingScreening($exam->id);
        if (is_null($examScreening)) {
            $examScreening = $examScreeningModel->getNearestScreening($exam->id);
        }
        $exam_screen_id = $examScreening->id;
        return $this->where('student_id', '=', $studentId)
            ->where('exam_id', '=', $exam->id)
            ->where('exam_screening_id', '=', $exam_screen_id)
            ->orderBy('begin_dt', 'asc')
            ->get();
    }

    public function getPagination()
    {
        return $this->paginate(config('msc.page_size'));
    }

    static public function
    getStudentExamineeId($room_id, $examId,$stations, $exam_screening_id){
        //先判定该学生是否抽过签

            $queueing = ExamQueue::where('exam_queue.status', '<', 3)
                ->where('exam_queue.exam_id', $examId)
                ->where('exam_queue.room_id', $room_id)
                ->where('exam_queue.exam_screening_id', $exam_screening_id)
                ->orderBy('exam_queue.begin_dt', 'asc')
                ->orderBy('exam_queue.next_num', 'asc')
                ->groupBy('exam_queue.student_id')
                ->take(count($stations))
                ->get();
        return $queueing;
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
    static public function examineeByRoomId($room_id, $examId, $stations, $exam_screening_id)
    {
        try {
//                $ExamDraftFlow=ExamDraftFlow::leftJoin('exam_draft','exam_draft_flow.id','=','exam_draft.exam_draft_flow_id')
//                    ->leftJoin('exam_gradation','exam_gradation.id','=','exam_draft_flow.exam_gradation_id')
//                ->where('exam_draft.room_id',$room_id)
//                ->where('exam_draft_flow.exam_id',$examId)
//                ->where('exam_gradation.exam_id',$examId)
//                ->first();
            \Log::debug('examineeByRoomId', [$room_id, $examId, $stations, $exam_screening_id]);
            $queueing = ExamQueue::leftJoin('student', 'student.id', '=', 'exam_queue.student_id')
                ->where('exam_queue.room_id', $room_id)
                ->whereIn('exam_queue.status', [1, 2])
                ->where('student.exam_id', $examId)
                ->where('exam_queue.exam_screening_id', $exam_screening_id)
                ->where('exam_queue.blocking', 0)
//                ->where('exam_queue.locks', '<>', $room_id)
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
                ->orderBy('exam_queue.id', 'asc')
                ->groupBy('student.id')
                ->get();
            \Log::debug('num', [$queueing->count(), count($stations)]);
            if ($queueing->count() == count($stations)) {//没有正在考试的
//                return ExamQueue::leftJoin('student', 'student.id', '=', 'exam_queue.student_id')
//                    ->where('exam_queue.room_id', $room_id)
//                    ->where('exam_queue.status', '<', 3)
//                    ->where('exam_queue.exam_id', '=', $examId)
//                    ->where('student.exam_id', $examId)
//                    ->where('exam_queue.exam_screening_id', $exam_screening_id)
//                    //->where('exam_queue.blocking', 1)
//                    ->select(
//                        'student.id as student_id',
//                        'student.name as student_name',
//                        'student.user_id as student_user_id',
//                        'student.idcard as student_idcard',
//                        'student.mobile as student_mobile',
//                        'student.code as student_code',
//                        'student.avator as student_avator',
//                        'student.description as student_description',
//                        'exam_queue.id as exam_queue_id',
//                        'exam_queue.room_id as room_id',
//                        'exam_queue.station_id as station_id'
//                    )
//                    ->orderBy('exam_queue.next_num', 'asc')
//                    ->orderBy('exam_queue.begin_dt', 'asc')
//                    ->groupBy('student.id')
//                    ->take(count($stations))
//                    ->get();
                return $queueing;
            } elseif ($queueing->count() < count($stations)) {//不正常中断存在在考试的学生
                $temp = ExamQueue::leftJoin('student', 'student.id', '=', 'exam_queue.student_id')
                    ->where('exam_queue.room_id', $room_id)
                    ->where('exam_queue.blocking', 1)
                    ->where('exam_queue.status', '<', 3)
                    ->where('exam_queue.exam_id', '=', $examId)
                    ->where('exam_queue.exam_screening_id', $exam_screening_id)
                    ->where('student.exam_id', $examId)
//                    ->where('exam_queue.locks', '<>', $room_id)
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
                    ->orderBy('exam_queue.id', 'asc')
                    ->groupBy('student.id')
                    ->take(count($stations) - $queueing->count())
                    ->get();
                //dd($queueing, $temp->all(), $queueing->merge($temp));
                $data   =   [];
                foreach ($queueing as $item)
                {
                    $data[]=    $item;
                }
                foreach ($temp as $item)
                {
                    $data[]=    $item;
                }
                
                return collect($data);//$queueing->merge($temp);
            } else {
                \Log::error('needNumTooBig', [$queueing->count()]);
                return $queueing->take(count($stations));
            }
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    static public function examineeByStationId($stationId, $examId, $exam_screening_id)
    {
//        $ExamDraftFlow=ExamDraftFlow::leftJoin('exam_draft','exam_draft_flow.id','=','exam_draft.exam_draft_flow_id')
//            ->where('exam_draft.station_id',$stationId)
//            ->where('exam_draft_flow.exam_id',$examId)
//            ->first();
        $queueing = ExamQueue::leftJoin('student', 'student.id', '=', 'exam_queue.student_id')
            ->where('exam_queue.station_id', $stationId)
            ->where('exam_queue.status', '=', 2)
            ->where('exam_queue.exam_screening_id', $exam_screening_id)
            ->where('student.exam_id', $examId)
            ->first();
        if (is_null($queueing)) {//没有正在考试的
            return ExamQueue::leftJoin('student', 'student.id', '=', 'exam_queue.student_id')
                ->where('exam_queue.station_id', $stationId)
                ->where('exam_queue.status', '<', 3)
                ->where('student.exam_id', $examId)
                ->where('exam_queue.exam_screening_id', $exam_screening_id)
                //->where('exam_queue.blocking', 1)
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
                ->orderBy('exam_queue.id', 'asc')
                ->take(1)
                ->get();
        } else {
            return ExamQueue::leftJoin('student', 'student.id', '=', 'exam_queue.student_id')
                ->where('exam_queue.station_id', $stationId)
                ->where('exam_queue.status', '<', 3)
                ->where('exam_queue.exam_screening_id', $exam_screening_id)
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
                ->orderBy('exam_queue.id', 'asc')
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
    static public function nextExamineeByRoomId($room_id, $examId, $station, $exam_screening_id)
    {
        try {
            $ExamDraftFlow = ExamDraftFlow::leftJoin('exam_draft', 'exam_draft_flow.id', '=', 'exam_draft.exam_draft_flow_id')
                ->where('exam_draft.room_id', $room_id)
                ->where('exam_draft_flow.exam_id', $examId)
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
                ->orderBy('exam_queue.id', 'asc')
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

    static public function nextExamineeByStationId($stationId, $examId, $exam_screening_id)
    {
        try {
            $ExamDraftFlow = ExamDraftFlow::leftJoin('exam_draft', 'exam_draft_flow.id', '=', 'exam_draft.exam_draft_flow_id')
                ->where('exam_draft.station_id', $stationId)
                ->where('exam_draft_flow.exam_id', $examId)
                ->first();
            return ExamQueue::leftJoin('student', 'student.id', '=', 'exam_queue.student_id')
                ->where('exam_queue.station_id', $stationId)
                ->where('exam_queue.status', '<', 3)
                ->where('exam_queue.exam_id', $examId)
                ->where('exam_queue.blocking', 1)
                ->where('exam_queue.exam_screening_id', $exam_screening_id)
                ->orderBy('exam_queue.next_num', 'asc')
                ->orderBy('exam_queue.begin_dt', 'asc')
                ->orderBy('exam_queue.id', 'asc')
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


    public function AlterTimeStatus($studentId, $stationId, $nowTime, $teacherId, $examscreeningId)

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
                ->whereIn('exam_screening_id', $examscreeningId)
                ->whereIn('status', [0, 1, 2])
                ->first();
            //dd($examQueue);
            if (is_null($examQueue)) {
                throw new \Exception('该学生还没有抽签', -105);
            }
            if ($examQueue->status == 2) {
                return true;
            }


            $lateTime = $nowTime - strtotime($examQueue->begin_dt);
            //判断考生的迟到时间
            if ($lateTime < 0) {
                $lateTime = 0;
            }

            \Log::alert('开始考试改变学生的队列',[$examQueue->id,$examQueue->student_id,$examQueue->status]);
            //修改队列状态
            $examQueue->status = 2;
            $examQueue->begin_dt = date('Y-m-d H:i:s', $nowTime);  //???

            //$examQueue->stick=null;
            if ($examQueue->save()) {
                $studentTimes = ExamQueue::where('student_id', '=', $studentId)
                    ->whereIn('exam_queue.status', [0, 1, 2]) //只查状态为2的
                    ->whereIn('exam_screening_id', $examscreeningId)
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
                $stationTime = $this->stationTime($nowQueue->station_id, $exam->id);

                //拿到状态为三的队列
                $endQueue = ExamQueue::where('exam_id', '=', $exam->id)
                    ->where('student_id', '=', $studentId)
                    ->whereIn('exam_screening_id', $examscreeningId)
                    ->where('status', '=', 3)
                    ->get();
                foreach ($studentTimes as $key => $item) {
                    foreach ($endQueue as $endQueueTime) {
                        if (strtotime($endQueueTime->begin_dt) > strtotime($item->begin_dt)) {
                            throw new \Exception('当前队列开始时间不正确', -104);
                        }
                    }

                    /* //考试排序模式
                      if ($exam->sequence_mode == 2) {
                          $stationTime = $item->station->mins ? $item->station->mins : 0;
                      } else {
                          //这是已考场安排的需拿到room_id
                          $stationTime = $this->getRoomStationMaxTime($item->room_id);
                      }*/
                    \Log::alert('获取到的标准时间',[$stationTime]);
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
                            ->where('exam_id', $exam->id)
                            ->where('station_id', '=', $stationId)
                            ->where('status', '=', 2)
                            ->first();
                        if (is_null($ExamTime)) {
                            throw new \Exception('没有找到对应的队列信息', -102);
                        }

                        $ExamTime->begin_dt = date('Y-m-d H:i:s', $nowTime);
                        $ExamTime->end_dt = date('Y-m-d H:i:s', $nowTime + $stationTime * 60);

                        \Log::alert('改变的时间',[$ExamTime->begin_dt,$ExamTime->end_dt,$ExamTime->id]);
                        if (!$ExamTime->save()) {
                            throw new \Exception('队列时间更新失败', -100);
                        }
                    }
                }
            } else {
                throw new \Exception('队列状态更新失败', -101);

            }
            //更新exam_station_status（考试-场次-考站状态表）status为3
            $examStationStatus = ExamStationStatus::where('exam_id',$exam->id)->where('exam_screening_id',$examscreeningId)->where('station_id',$stationId)->first();
            if(!empty($examStationStatus)){
                $examStationStatus->status = 3;
                $examStationStatus->save();
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

    /**获取标准考试时间
     * @method
     * @url /osce/
     * @access public
     * @param $station_id 考站id
     * @param $exam_id 考试id
     * @author xumin <xumin@misrobot.com>
     * @date
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function stationTime($station_id, $exam_id)
    {
        $stationTime = 0;
        $station = Station::where('id', $station_id)->first();

        if (!empty($station)) {
            if ($station->type == 3) {
                //理论站
                $paper = ExamPaper::where('id', $station->paper_id)->first();
                if (!empty($paper)) {
                    $stationTime = $paper->length;
                }
            } else {
                $ExamDraft = ExamDraft::join('exam_draft_flow', 'exam_draft_flow.id', '=', 'exam_draft.exam_draft_flow_id')
                    ->where('exam_draft_flow.exam_id', '=', $exam_id)
                    ->where('exam_draft.station_id', '=', $station->id)
                    ->first();
                \Log::alert('subject', [$ExamDraft]);
                if (!is_null($ExamDraft)) {
                    $subject = Subject::where('id', $ExamDraft->subject_id)->first();

                    if (!is_null($subject)) {
                        \Log::alert('科目',[$subject]);
                        $stationTime = $subject->mins;
                    }
                }
            }
        }
        return $stationTime;
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
                ->where('exam_screening_id', $examScreeningId)
                ->orderBy('begin_dt', 'asc')->get();

            if ($examObj->isEmpty()) {
                //通过$examId, $studentId还有$examScreeningId在plan表中找到对应的数据
                $examPlan = ExamPlan::where('exam_id', '=', $examId)
                    ->where('exam_screening_id', $examScreeningId)
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
                        "exam_id" => $item->exam_id,
                        "exam_screening_id" => $item->exam_screening_id,
                        "student_id" => $item->student_id,
                        "station_id" => $item->station_id,
                        "room_id" => $item->room_id,
                        "gradation_order" => $item->gradation_order,
                        "begin_dt" => $item->begin_dt,
                        "end_dt" => $item->end_dt,
                        "status" => $item->status,
                        "created_user_id" => $item->created_user_id,
                        "flow_id" => $item->flow_id,
                        "serialnumber" => $item->serialnumber,
                        "group" => $item->group,
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
            $examId = Exam::doingExam();
            $examScreeningModel = new ExamScreening();
            $examScreening = $examScreeningModel->getExamingScreening($examId->id);
            if (is_null($examScreening)) {
                $examScreening = $examScreeningModel->getNearestScreening($examId->id);
                $examScreening->status = 1;
            }
            $exam_screen_id = $examScreening->id;
            //通过学生id找到对应的examScreeningStudent实例
            $examScreening = ExamScreeningStudent::where('student_id', $studentId)->where('exam_screening_id', '=', $exam_screen_id)->first();

            \Log::alert('结束考试场次信息',[$exam_screen_id,$examScreening->id]);
            if (is_null($examScreening)) {
                throw new \Exception('没找到对应的学生编号', 2100);
            }

            //拿到$examScreeningId和$studentId

            $examScreeningId = $examScreening->exam_screening_id;

            //获取考生正在进行考试的队列信息
            $queue = ExamQueue::where('student_id', $studentId)
                ->where('exam_screening_id', $examScreeningId)
                ->where('station_id', $stationId)
//                ->whereIn('status', [0,1,2])
                ->first();
            if (empty($queue)) {
                throw new \Exception('没有找到符合要求的学生', 2200);
            }


            \Log::alert('结束考试改变学生的队列',[$queue->id,$queue->student_id,$queue->status]);
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
     *
     *
     *
     *
     *    $builder = $this->leftJoin('exam_draft_flow',
    function ($join) {
    $join->on('exam_draft_flow.exam_id', '=', 'exam_queue.exam_id');
    })
    ->leftJoin('student', function ($join) {
    $join->on('student.id', '=', 'exam_queue.student_id');
    })
    ->leftJoin('exam_draft', function ($join) {
    $join->on('exam_draft_flow.id', '=', 'exam_draft.exam_draft_flow_id');
    })
     */
    public function getWaitStudentStation($station_id = '', $exam_id = '', $screeningId)
    {
        $builder = $this->leftJoin('student',
            function ($join) {
                $join->on('student.id', '=', 'exam_queue.student_id');
            })
//            ->leftJoin('exam_draft_flow', function ($join) {
//                $join->on('exam_draft_flow.exam_id', '=', 'exam_queue.exam_id');
//            })
//            ->leftJoin('exam_draft', function ($join) {
//                $join->on('exam_draft_flow.id', '=', 'exam_draft.exam_draft_flow_id');
//            })
//            ->where('exam_draft.station_id', '=', $station_id)
//            ->where('exam_draft_flow.exam_id', '=', $exam_id)
            ->where('exam_queue.exam_screening_id', '=', $screeningId)
            ->where('exam_queue.station_id', '=', $station_id)
            ->where('exam_queue.exam_id', '=', $exam_id)
            ->where('exam_queue.status', '=', 0)
            ->orderBy('exam_queue.begin_dt', 'asc')
            ->orderBy('student.id', 'asc')
            ->select(['student.name as name', 'exam_queue.student_id', 'exam_queue.begin_dt'])
            ->distinct()->take(4)->get();

        if (count($builder) != 0) {
            foreach ($builder as &$item) {
                //获取同一个人，在一场考试队列中是否有更早的考试
                $result = $this->where('exam_id', '=', $exam_id)->where('student_id', '=', $item->student_id)
                               ->where('status', '=', 0)->where('exam_screening_id', '=', $screeningId)
                               ->whereRaw('unix_timestamp(begin_dt) < ?', [strtotime($item->begin_dt)])->first();
                if ($result) {
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
    public function getWaitStudentRoom($room_id = '', $exam_id = '', $screeningId)
    {
        //获取当前正在考试的考试场次
        $screen_id =  $this->getExamScreeningId($exam_id);

        $builder = $this->leftJoin('student', function ($join) {
                $join->on('student.id', '=', 'exam_queue.student_id');
            })
//            ->leftJoin('exam_draft_flow',
//            function ($join) {
//                $join->on('exam_draft_flow.exam_id', '=', 'exam_queue.exam_id');
//            })
//            ->leftJoin('exam_draft', function ($join) {
//                $join->on('exam_draft_flow.id', '=', 'exam_draft.exam_draft_flow_id');
//            })
//            ->where('exam_draft.room_id', '=', $room_id)
//            ->where('exam_draft_flow.exam_id', '=', $exam_id)
            ->where('exam_queue.room_id', '=', $room_id)
            ->where('exam_queue.exam_id', '=', $exam_id)
            ->where('exam_queue.exam_screening_id', '=', $screen_id)
            ->where('exam_queue.status', '=', 0)
            ->orderBy('exam_queue.begin_dt', 'asc')
            ->orderBy('student.id', 'asc')
            ->select(['student.name as name', 'exam_queue.student_id', 'exam_queue.begin_dt'])
            ->distinct()->take(4)->get();

        if (count($builder) != 0) {
            foreach ($builder as &$item) {
                //获取同一个人，在一场考试队列中是否有更早的考试
                $result = $this->where('exam_id', '=', $exam_id)->where('student_id', '=', $item->student_id)
                               ->where('status', '=', 0)->where('exam_screening_id', '=', $screen_id)
                               ->whereRaw('unix_timestamp(begin_dt) < ?', [strtotime($item->begin_dt)])->first();
                if ($result) {
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
            $queue->end_dt =  $date;
            $queue->status =  3;
            $queue->blocking = 1;
            if(!$queue->save()){
                throw new \Exception('状态修改失败！请重试', -101);
            }

            //更改考站的准备状态
            $examStationStatus = ExamStationStatus::where('station_id', $queue->station_id)
                ->where('exam_id', $queue->exam_id)
                ->where('exam_screening_id', $queue->exam_screening_id)
                ->first();
            $examStationStatus->status = 4;
            if (!$examStationStatus->save()) {
                throw new \Exception('考站准备状态失败！', -102);
            }
            $connection->commit();
            return $queue;
        } catch (\Exception $ex) {
            $connection->rollBack();
            throw $ex;
        }
    }

    /**
     * 获取 考站/考场 分页
     * @param $exam_id
     * @param $screeningId
     * @param int $pageSize
     * @param string $mode
     * @return mixed
     * @author zhoufuxiang <zhoufuxiang@misrobot.com>
     * @date   2016-05-05
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function getPageSize($exam_id, $screeningId, $pageSize = 4, $mode = 'station_id')
    {
        return $this->where('exam_id', '=', $exam_id)->where('exam_screening_id', '=', $screeningId)
                    ->where('status', '=', 0)->groupBy($mode)
                    ->orderBy('begin_dt', 'asc')->orderBy('id', 'asc')
                    ->paginate($pageSize);
    }

    /**
     * 获取候考考站对应学生列表
     * @param $exam_id
     * @param $screeningId
     * @param int $pageSize
     * @return array
     * @author zhoufuxiang <zhoufuxiang@misrobot.com>
     * @date   2016-05-05
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function getWaitStationStudents($exam_id, $screeningId, $pageSize = 4)
    {
        $examRoomLists = $this->getPageSize($exam_id, $screeningId, $pageSize);
//        $examRoomLists = ExamDraft::leftJoin('exam_draft_flow', 'exam_draft_flow.id', '=', 'exam_draft.exam_draft_flow_id')
//                        ->where('exam_draft_flow.exam_id', '=', $exam_id)
//                        ->groupBy('exam_draft.station_id')
//                        ->paginate($pageSize);

        $data = [];
        foreach ($examRoomLists as $examRoomList)
        {
            $stationName = $examRoomList->station->name;
            $station_id = $examRoomList->station_id;
            $ExamQueue = new ExamQueue();
            $students = $ExamQueue->getWaitStudentStation($station_id, $exam_id, $screeningId);

            if($students->isEmpty())
            {
                $data[$station_id]['name']    = $stationName;
                $data[$station_id]['student'] = collect([]);
            }else
            {
                foreach ($students as $student) {
                    if ($student->name == '') {
                        $student->name = '';
                    }
                    $data[$station_id]['name'] = $stationName;
                    $data[$station_id]['student'][] = $student;
                }
            }
        }
        $data = array_values($data);

        return $data;
    }

    /**
     * 获取候考考场对应学生列表
     */
    public function getWaitRoomStudents($exam_id, $screeningId, $pageSize = 4)
    {
        //获取到该考试下所有的房间
        $examRoomLists = $this->getPageSize($exam_id, $screeningId, $pageSize, 'room_id');

//        $examRoomLists = ExamDraft::leftJoin('exam_draft_flow', 'exam_draft_flow.id', '=', 'exam_draft.exam_draft_flow_id')
//                        ->where('exam_draft_flow.exam_id', '=', $exam_id)
//                        ->groupBy('exam_draft.room_id')
//                        ->paginate($pageSize);

        $data = [];
        foreach ($examRoomLists as $examRoomList)
        {
            $roomName = $examRoomList->room->name;
            $room_id  = $examRoomList->room_id;
            $ExamQueue= new ExamQueue();
            $students = $ExamQueue->getWaitStudentRoom($room_id, $exam_id, $screeningId);

            if($students->isEmpty()){
                $data[$room_id]['name']    = $roomName;
                $data[$room_id]['student'] = collect([]);
            }else
            {
                foreach ($students as $student) {
                    if ($student->name == '') {
                        $student->name = '';
                    }
                    $data[$room_id]['name']      = $roomName;
                    $data[$room_id]['student'][] = $student;
                }

            }
        }

        $data = array_values($data);

        return $data;
    }


    //exam_station
    public function examstation()
    {
        return $this->hasOne('Modules\Osce\Entities\ExamStation', 'station_id', 'station_id');
    }


    //查找学生队列中的考试

    public function getExamingData($examId, $studentId)
    {
        $builder = $this->where('exam_queue.exam_id', $examId)->where('exam_queue.student_id', $studentId)->where('station.type', '=', 3)->leftjoin('exam', function ($exam) {
            $exam->on('exam.id', '=', 'exam_queue.exam_id');
        })->leftjoin('station', function ($exam) {
            $exam->on('station.id', '=', 'exam_queue.station_id');
        })->select('exam.id', 'exam.name', 'exam_queue.station_id', 'exam_queue.status', 'exam_queue.room_id', 'station.paper_id')->orderBy('exam_queue.begin_dt', 'asc')->get();
        return $builder;
    }

    //获取考生的考站数量
    public function getStationNum($studentId)
    {
        $DB = \DB::connection('osce_mis');
        $builder = $this->where('student_id', '=', $studentId)->where('status', '!=', 3)->select(
            $DB->raw('count(station_id) as station_num')
        )->first();
        return $builder;
    }

    //查看学生当前状态
    public function getExamineeStatus($examing, $studentId)
    {
        $builder = $this->where('exam_id', '=', $examing)->where('student_id', '=', $studentId)->first();
        return $builder;
    }


    //获取场次id
    public function getExamScreeningId($exam_id)
    {
        try{
            $screenModel    =   new ExamScreening();
            $examScreening  =   $screenModel ->getExamingScreening($exam_id);

            if(is_null($examScreening))
            {
                $examScreening = $screenModel->getNearestScreening($exam_id);
            }
            if (is_null($examScreening)) {
                return null;
//            return \Response::json(array('code' => 2));     //没有对应的开考场次 —— 考试场次没有(1、0)
            }
            $screen_id    = $examScreening->id;


            //拿到oder表里的考试场次 todo 周强 2016-4-30
            $OderExamScreeningId = ExamOrder::where('exam_id','=',$exam_id)->groupBy('exam_screening_id')->get()->pluck('exam_screening_id')->toArray();
            if(!in_array($screen_id,$OderExamScreeningId)){
                $screen_id = ExamOrder::where('exam_id','=',$exam_id)
                    ->where('status','=',1)
                    ->OrderBy('begin_dt', 'asc')
                    ->first();
                $screen_id = $screen_id->exam_screening_id;
            }
            return $screen_id;
        }
        catch (\Exception $ex)
        {
            $screen_id = null;
        }
    }

    /**
     * 腕表解绑，获取学生本次考试所有考场考试情况列表
     * @param $code
     * @return object
     *
     * @author wt <wangtao@misrobot.com>
     * @date   2016-05-7
     * @copyright 2013-2016 MIS misrobot.com Inc. All Rights Reserved
     */

    public function getStudentScreenRoomResultList($code){
        $screenStudent=new ExamScreeningStudent();
        $screen= new ExamScreening();
        $exam=Exam::doingExam();//当前考试id
        $screenId=$screen->getScreenID($exam->id);//获取当前场次id
        $studentMsg=$screenStudent->getStudentByWatchCode($code,$screenId);//获取腕表对应的学生信息

        if(is_null($studentMsg)){
            throw new \Exception('找不到对应的学生id');
        }
        $student_id=$studentMsg->student_id;
        $studentMsgList=$this->where('exam_screening_id',$screenId)
             ->where('exam_id',$exam->id)
             ->where('student_id',$student_id)
             ->select(['id','exam_screening_id','room_id','student_id','status','exam_id'])
             ->orderBy('begin_dt')->get();//获取学生信息列表
        if(!is_null($studentMsgList)){
            $flag=false;
           
            foreach($studentMsgList as $key=>$val){//数据整理
                $room=$val->room;
                $studentMsgList[$key]['room_name']=$room->name;
                if($val->status<3){
                    $flag=true;
                    $studentMsgList[$key]['result']='未上传';
                }else{
                    $studentMsgList[$key]['status']=3;//c++判断已上传成数量绩标识
                    $studentMsgList[$key]['result']='已上传';
                }
            }
            $list=$studentMsgList->toArray();
            foreach ($list as $key=>$val){//去除不返回数据
                unset($list[$key]['room']);
            }
            if(!$flag){//本次所有考场考试考完解绑
                return [];
            }else{
                return $list;
            }

        }
    }

    public function getStudentWatchMovement($exam_id,$student_id,$examScreening,$beginTime =null)
    {

        //拿到当前学生队列信息
        $studentQueue = $this->where('exam_id', '=', $exam_id)
            ->where('exam_screening_id', '=', $examScreening->id)
            ->where('student_id', '=',$student_id)
            ->whereIn('status', [0,1,2])
            ->orderBy('begin_dt', 'asc')
            ->get();
        $data = [];

        if(!empty($beginTime)){
            foreach ($studentQueue as $item){
                $time = $beginTime;
                $studentFront = $this->where('exam_id', '=', $exam_id)
                    ->where('exam_screening_id', '=', $examScreening->id)
                    ->where('room_id','=',$item->room_id)
                    ->where('status', '=', 0)
                    ->whereRaw("UNIX_TIMESTAMP(begin_dt) != UNIX_TIMESTAMP('$time')")
                    ->orderBy('begin_dt', 'asc')
                    ->get();
                foreach ($studentFront as $value)
                    $data[] = [
                        $value->student_id,
                    ];
            }
        }else{
            foreach ($studentQueue as $item){
                $time =  $item ->begin_dt;
                $studentFront = $this->where('exam_id', '=', $exam_id)
                    ->where('exam_screening_id', '=', $examScreening->id)
                    ->where('room_id','=',$item->room_id)
                    ->where('status', '=', 0)
                    ->whereRaw("UNIX_TIMESTAMP(begin_dt) >= UNIX_TIMESTAMP('$time')")
                    ->orderBy('begin_dt', 'asc')
                    ->get();
                foreach ($studentFront as $value)
                    $data[] = [
                        $value->student_id,
                    ];
            }
        }


        return $data;
    }




}