<?php
/**
 * Created by PhpStorm.
 * @author tangjun <tangjun@misrobot.com>
 * @date 2016-02-23 14:00
 * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
 */

namespace Modules\Osce\Repositories;

use Illuminate\Support\Facades\Redis;
use Maatwebsite\Excel\Collections\CellCollection;
use Modules\Osce\Entities\Drawlots\DrawlotsRepository;
use Modules\Osce\Entities\ExamDraft;
use Modules\Osce\Entities\ExamPlan;
use Modules\Osce\Entities\ExamQueue;
use Modules\Osce\Entities\ExamScreening;
use Modules\Osce\Entities\ExamScreeningStudent;
use Modules\Osce\Entities\ExamStationStatus;
use Modules\Osce\Entities\QuestionBankEntities\ExamPaperExamStation;
use Modules\Osce\Entities\Room;
use Modules\Osce\Entities\Student;
use Modules\Osce\Entities\TestResult;
use Modules\Osce\Entities\Watch;
use Modules\Osce\Entities\WatchLog;
use Modules\Osce\Repositories\BaseRepository;
use Modules\Osce\Entities\Exam;
use Modules\Osce\Entities\ExamResult;
use Modules\Osce\Entities\ExamStation;
use Modules\Osce\Entities\SubjectItem;
use Modules\Osce\Entities\Station;
use Modules\Osce\Entities\Subject;
use Modules\Osce\Entities\StationTeacher;

/**
 * Class StatisticsRepositories
 * @package Modules\Osce\Repositories
 */
class WatchReminderRepositories extends BaseRepository
{
    //当前考试实例
    protected $exam;
    //当前请求房间
    protected $room;
    //当前请求学生
    protected $student;
    //当前请求考站
    protected $station;
    //房间下考站数量
    protected $stationNum;
    //当前队列
    protected $nowQueue;
    //当前响应考站
    protected $nowStation;
    //响应房间学生列表
    protected $roomStudentList;
    //当前响应房间
    protected $nowRoom;
    //当前场次
    protected $examScreening;
    //腕表code
    protected $code;
    //封装data数组
    protected $data =[
        'code' => '',
        'willStudents' => '',
        'estTime' => '',
        'willRoomName' => '',
        'roomName' => '',
        'nextExamName' => '',
        'surplus' => '',
        'score' => '',
        'title' => '',
    ];


    public function setInitializeData($exam, $student, $room, $station)
    {
        $this->exam = $exam;
        $this->student = $student;
        $this->room = $room;
        $this->station = $station;
        $this->redis = Redis::connection('message');;

            $examScreeningModel = new ExamScreening();
            $examScreening = $examScreeningModel->getExamingScreening($exam->id);

            if (is_null($examScreening)) {
                $examScreening = $examScreeningModel->getNearestScreening($exam->id);
            }
            if (is_null($examScreening)) {
                throw new \Exception('没有找当前的考试场次');
            }
            $this->examScreening = $examScreening;
        return $this;
    }

    /**
     *判断考试模式
     * @method GET
     * @url /osce/api/student-watch/student-exam-reminder
     * @access public
     * @param Request $request get请求<br><br>
     * <b>get请求字段：</b>
     * * string     nfc_code    腕表nfc_code
     *
     * @return json
     *
     * @version 1.0
     * @author weihuiguo <weihuiguo@misrobot.com>
     * @date
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function getStudentExamReminder($exam, $student, $room, $station)
    {
        try {
            //初始化
            $this->setInitializeData($exam, $student, $room, $station);
            //dd($station);
            //判断考试模式
            if ($exam->sequence_mode == 1) {//考场模式
                $this->getRoomExamReminder($exam, $student, $this->examScreening);
            } else {//考站模式
                $this->getStationExamReminder();
            }
        } catch (\Exception $ex) {
            \Log::debug('腕表推送错误', [$ex]);
            throw $ex;
        }
    }


    /**
     * 考站模式
     */
    public function getStationExamReminder()
    {
        throw new \Exception('考站模式暂未开发');
    }

    /**
     * 考场模式
     */
    public function getRoomExamReminder($exam, $student, $examScreening)
    {


        //查看腕表是否绑定
        $watchStatus = $this->getWatchStatus();
        \Log::info('腕表状态',[$watchStatus->status ]);
        if ($watchStatus->status == 0) {
            $data = [
                'code' => -1, // 侯考状态（对应界面：前面还有多少考生，估计等待时间）
                'title' => '腕表未绑定',
            ];
            $this->publishmessage($watchStatus->code, $data, 'success');
            return response()->json(
                ['nfc_code' => $watchStatus->code, 'data' => $data]);
        }
        //根据exam、student对象查找队列数据
        $queueList = $this->getExamStudentQueueList($exam, $student, $examScreening);

        \Log::alert('当前学生所有队列', [$queueList]);
        //根据队列获取学生当前队列
        $queue = $this->getSutentNowQueue($queueList);
        \Log::alert('学生当前队列', [$queue,]);
        //根据当前队列获取当前状态,

        $status = $this->getNoticeStauts($queue, $queueList);
        \Log::alert('学生队列状态', [$status]);
        //并且根据当前状态选择相应操作
        switch ($status) {
            //待考
            case 0:
                $this->getWaitings();

                break;
            //通知去考场
            case 1:
                $this->getGOtoRoon();
                break;
            //抽签
            case 2:
                $this->getchoose();

                break;
            //开始考试
            case 3:
                $this->getStartExam();

                break;
            //结束考试
            case 4:
                $this->endExam();

                break;
            default:
                throw new \Exception('未定义的腕表状态');

        }


    }


    private function getWatchStatus()
    {
        //根据当前学生获取NFC——code
    
            $code = ExamScreeningStudent::leftJoin('watch', 'exam_screening_student.watch_id', '=', 'watch.id')
                ->where('exam_screening_student.exam_screening_id', $this->examScreening->id)
                ->where('exam_screening_student.student_id', $this->student->id)
                ->select(['watch.code', 'watch.status'])
                ->first();
        if(is_null($code)){
            \Log::debug('获取学生腕表信息出错',[$this->student->id]);
            throw  new \Exception('获取学生腕表信息失败');
        }
        return $code;
    }

    public function getNoticeStauts($queue, $queueList)
    {
//        if(!is_null($queue[0])){
//            $queue = $queue[0];
//        }
        //是否当前队列是否为结束考试
        if ($queue === false) {
            return 4;
        }

        //是否当前队列是否为考站考试正在进行
        if ($queue->status == 2) {
            //是，返回3
            return 3;
        }

        //是否当前队列是否为考站已抽签
        if ($queue->status == 1) {
            return 2;
        }

        //是否当前队列是否为待考
        if ($queue->status == 0) {
            $exam_station_station = $this->getTeacherStatus();
            if ($exam_station_station <= 0) {
                //准备好了，通知去考场
                return 1;
            } else {
                //没有准备好，通知等待
                return 0;

            }
        }


    }

    //判断老师是否准备完成
    private function getTeacherStatus()
    {
        //拿到所有的考站
        $stationLists = ExamStationStatus::where('exam_screening_id', $this->examScreening->id)
            ->get()->pluck('station_id')->toArray();

        $stationIds = ExamDraft::  leftJoin('exam_draft_flow', 'exam_draft_flow.id', '=', 'exam_draft.exam_draft_flow_id')
            ->where('exam_draft.room_id', '=', $this->room->id)
            ->whereIn('exam_draft.station_id', $stationLists)
            ->where('exam_draft_flow.exam_id', '=', $this->exam->id)
            ->get()
            ->pluck('station_id')
            ->toArray();
        //dd($stationIds ,$this->room->id);
        //判断房间是否准备好
        $exam_station_station = ExamStationStatus::where('exam_id', '=', $this->nowQueue->exam_id)
            ->whereIn('station_id', $stationIds)
            ->where('exam_screening_id', '=', $this->nowQueue->exam_screening_id)
            ->whereIn('status', [0, 4])
            ->count();
        \Log::alert('老师准备的数量', [$exam_station_station]);
        return $exam_station_station;
    }


    /**
     * @content：根据exam、student对象查找队列数据
     * @author：
     * @createDate：
     */
    public function getExamStudentQueueList($exam, $student, $examScreening)
    {
        $StudentQueueList = ExamQueue::where('exam_id', '=', $exam->id)
            ->where('exam_screening_id', '=', $examScreening->id)
            ->where('student_id', '=', $student->id)->orderBy('begin_dt', 'asc')->get();
        if ($StudentQueueList) {
            return $StudentQueueList;
        } else {
            return false;
        }
    }

    /**
     * @content：根据队列获取学生当前队列
     * @author：
     * @createDate：
     */
    public function getSutentNowQueue($queueList)
    {

        //获取当前队列
        //获取是否有一条正在进行的考试
        $queue = $this->queueWhere($queueList, 'status', 2);

        if (!$queue->isEmpty()) {
            //初始化当前队列
            \Log::debug('状态为2的队列', [$queue]);
            return $this->nowQueue = $queue->first();

        }

        //获取是否有一条已经抽签的考试
        \Log::info('获取状态为1队列前数据监视', [$queueList]);
        $queue = $this->queueWhere($queueList, 'status', 1);

        \Log::debug('状态为1的队列', [$queue]);
        if (!$queue->isEmpty()) {
            //初始化当前队列
            return $this->nowQueue = $queue->first();
        }

        //获取已经考完的队列集合
        $queue = $this->queueWhere($queueList, 'status', 3);
        \Log::debug('状态为3的队列', [$queue]);

        //判断是否考完
        if (count($queueList) == count($queue)) {
            //如果是，返回 false
            $this->nowQueue = '';
            return false;
        } else {
            //不是，待考
            \Log::info('学生队列', [$queueList]);

            $queue = $this->queueWhere($queueList, 'status', 0);
            \Log::info('学生状态为0的队列', [$queue]);

        }
        //初始化当前队列
        $this->nowQueue = $queue->first();
        //当初始化不能准确获取考站实例的时候补充初始化考站

        if (is_null($this->station)) {
            \Log::debug('当前队列', [$this->nowQueue]);
            $this->station = $this->nowQueue->station;   //??????  todo 需确定后修改；
        }
        //当初始化不能准确获取考场实例的时候补充初始化考场
        if (is_null($this->room)) {
            $this->room = $this->nowQueue->room;
        }

        if($this->room->id != $this->nowQueue->room_id){
            $this->room = $this->nowQueue->room;
        }

        return $this->nowQueue;

        //throw new \Exception('队列数据异常，找不到相应的各种状态数据');
    }

    private function queueWhere($collection, $find, $value, $big = false)
    {
        $data = [];
        \Log::alert('传入参数', [$collection]);
        foreach ($collection as $item) {
            if ($big) {
                if ($item->$find === $value) {
                    $data[] = $item;
                }
            } else {
                if (intval($item->$find) === intval($value)) {
                    $data[] = $item;
                }
            }
        }
        return collect($data);
    }

    /**
     * 待考
     */
    public function getWaitings()
    {

        // 初始化房间考站数量
        $drawlots = new DrawlotsRepository();
        $this->stationNum = count($drawlots->getStationNum($this->exam->id, $this->nowQueue->room_id, $this->examScreening->id));

        // todo 调用学生提示信息提示方法
        $data = $this->getStudentWatchInfo();
        //根据学生获取NFC _code
        $studentCode = $this->getWatchStatus();
        $this->publishmessage($studentCode->code, $data, 'success');

        return response()->json(
            ['nfc_code' => $studentCode->code, 'data' => $data, 'message' => 'success']
        );
    }

    /**
     * 通知去考场
     */
    public function getGOtoRoon()
    {
        // 初始化房间考站数量
        $drawlots = new DrawlotsRepository();
        $this->stationNum = count($drawlots->getStationNum($this->exam->id, $this->nowQueue->room_id, $this->examScreening->id));

        // todo 调用学生提示信息提示方法
        $data = $this->getStudentWatchInfo();
        //根据当前学生获取NFC——code
        $code = $this->getWatchStatus();
        //推送消息
        $this->publishmessage($code->code, $data, 'success');

        return response()->json(
            ['nfc_code' => $code->code, 'data' => $data, 'message' => 'success']
        );
    }
    /**
     *队列状态为零时具体的提示信息
     * @param WatchReminderRepositories
     * @return array
     * @internal param $room_id
     * @author zhouqiang
     */

    private function getStudentWatchInfo()
    {
        //查询当前学生是否已考过一个考场
        $studentFinishExam = $this->getStudentFinishRoom();
        //判定当前房间下考站是否都有空（同进同出）
        $stationStatus = ExamQueue::where('exam_id', '=', $this->exam->id)
            ->where('exam_screening_id', '=', $this->examScreening->id)
            ->where('room_id', '=', $this->room->id)
            ->whereIn('status', [1, 2])
            ->orderBy('begin_dt', 'asc')
            ->take($this->stationNum)
            ->get();
        //获取前面还有多少人 即当前学生在队列的位置
        $studentFront = $this->getStudentFrontNum();
        \Log::alert('学生前面的人数', [$studentFront]);
        //获取当前队列是否有考试中的人
        $willStudents = $this->getNowQueueExam();
        //获取将要去的考场
        $room = Room::find($this->nowQueue->room_id);
        $roomInfo = $this->getStudentNextExam();
        $examtimes = date('H:i', (strtotime($this->nowQueue->begin_dt)));
        if ($this->exam->same_time == 1) { //判断考试是否要求学生同进同出
            if ($stationStatus->isEmpty()) {//判断前面是否还有人在考试中
                //判断学生当前在队列的位置
                if ($studentFront < $this->stationNum) { // 判断前面考生人数是否够考站考试
                    \Log::alert('判断学生前面人数不够考试时', [$studentFront, $this->stationNum]);
                    // todo 通知学生去的地方
                    $data = $this->getStudentFinishExam($studentFinishExam, $roomInfo, $this->room); // todo 这个方法里应该判定里面有没有在考试的情况
                } else {

                    \Log::alert('判断学生前面人数够考试时', [$studentFront]);

                    //  todo 调用提示学生是去什么考场还是等待信息方法

                    $data = $this->getTeacherReady();
                }
            } else {
                //如果有考试的就提示等待
                $data['code'] = 1;  // 侯考状态（对应界面：前面还有多少考生，估计等待时间）
                $data['estTime'] = $examtimes;
                $data['willStudents'] = $willStudents;
                $data['willRoomName'] = $room->name;
                $data['title'] = '前面还有多少考生';
//                $data = $this->getTeacherReady();
            }
        } else {
            if (count($stationStatus) < $this->stationNum) { //判定房间考站是不是有空
                \Log::alert('学生前面人数', [$studentFront, $this->stationNum, count($stationStatus), $this->student->name]);
                if ($studentFront == 0 || $willStudents == 0) { //判定当前学生是不是第一个
                    // todo 通知学生去的地方
                    $data = $this->getStudentFinishExam($studentFinishExam, $roomInfo, $room);
                } else {
                    $data['code'] = 1;  // 侯考状态（对应界面：前面还有多少考生，估计等待时间）
                    $data['estTime'] = $examtimes;
                    $data['willStudents'] = $willStudents;
                    $data['willRoomName'] = $room->name;
                    $data['title'] = '前面还有多少考生';
                }
            } else {
                \Log::alert('学生前面人数', [$studentFront, $this->stationNum, count($stationStatus), $this->student->name, $this->nowQueue->begin_dt]);
                $data['code'] = 1;  // 侯考状态（对应界面：前面还有多少考生，估计等待时间）
                $data['estTime'] = $examtimes;
                $data['willStudents'] = $willStudents;
                $data['willRoomName'] = $room->name;
                $data['title'] = '前面还有多少考生';
            }
        }
        return $data;
    }

    /**
     *获取当前队列是否有考试中的人
     * @param WatchReminderRepositories
     * @return object
     * @internal param $room_id
     * @author zhouqiang
     */
    private function getNowQueueExam()
    {
        $studentDoingNum = ExamQueue::where('exam_id', '=', $this->exam->id)
            ->where('exam_screening_id', '=', $this->examScreening->id)
            ->where('room_id', '=', $this->nowQueue->room_id)
            ->whereIn('status', [1, 2])
            ->orderBy('begin_dt', 'asc')
            ->first();

        $studentFront = $this->getStudentFrontNum();
        $willStudents = 0;
        if ($studentFront < $this->stationNum) {
            if (!is_null($studentDoingNum)) {
                $willStudents = $studentFront + 1;
            }
            \Log::info('腕表推送出现等待推送中有前面学生小于考站数量的情况');
        } else {
            $willStudents = $studentFront;
        }
        if (!is_null($studentDoingNum)) {
            $willStudents = $studentFront + 1;
        }
        return $willStudents;
    }


    /**
     *获取前面还有多少人 即当前学生在队列的位置 即学生前面人数
     * @param WatchReminderRepositories
     * @return  int
     * @internal param $room_id
     * @author zhouqiang
     */
    private function getStudentFrontNum()
    {
        $time = $this->nowQueue->begin_dt;
        $studentFront = ExamQueue::where('exam_id', '=', $this->exam->id)
            ->where('exam_screening_id', '=', $this->examScreening->id)
            ->where('room_id', '=', $this->nowQueue->room_id)
            ->where('status', '=', 0)
            ->whereRaw("UNIX_TIMESTAMP(begin_dt) < UNIX_TIMESTAMP('$time')")
            ->orderBy('begin_dt', 'asc')
            ->count();
        return $studentFront;
    }

    //判定学生是不是刚绑定腕表
    private  function getDoExamStudent()
    {
        $studentDoingNum = ExamQueue::where('exam_id', '=', $this->exam->id)
            ->where('exam_screening_id', '=', $this->examScreening->id)
            ->where('room_id', '=', $this->nowQueue->room_id)
            ->whereIn('status', [1, 2])
            ->orderBy('begin_dt', 'asc')
            ->first();
        return $studentDoingNum;
    }

    //查询计划表里前面的人数
    private function getPlanStudentFront(){
        $time = $this->nowQueue->begin_dt;
        $studentFront = ExamPlan::where('exam_id', '=', $this->exam->id)
            ->where('exam_screening_id', '=', $this->examScreening->id)
            ->where('room_id', '=', $this->nowQueue->room_id)
            ->where('status', '=', 0)
            ->whereRaw("UNIX_TIMESTAMP(begin_dt) < UNIX_TIMESTAMP('$time')")
            ->orderBy('begin_dt', 'asc')
            ->count();
        return $studentFront;
    }

    /**
     * 判断老师是否准备好，和学生是否是当前第一个
     * @param WatchReminderRepositories
     * @return  array
     * @internal
     * @author zhouqiang
     */

    private function getTeacherReady()
    {
        //  todo 调用学生下一场方法
        $roomInfo = $this->getStudentNextExam();

        $examtimes = date('H:i', (strtotime($this->nowQueue->begin_dt)));
        // todo 调用判定学生前面是否有完成考试的方法
        $studentFinishExam = $this->getStudentFinishRoom();
        // todo 调用学生前面人数方法
        $studentFront = $this->getStudentFrontNum();

        // todo 调用学生前面等待人数方法
        $willStudents = $this->getNowQueueExam();
        \Log::alert('学生前面的人数',[$studentFront ,$willStudents]);
        if ($studentFront == 0 || $willStudents ==0) {
            // todo 通知学生去的考场
            $data = $this->getStudentFinishExam($studentFinishExam, $roomInfo, $this->room);
            \Log::info('判断学生应该去的考场834', [$data]);
        } else {
            $data['code'] = 1;  // 侯考状态（对应界面：前面还有多少考生，估计等待时间）
            $data['estTime'] = $examtimes;  // 侯考状态（对应界面：前面还有多少考生，估计等待时间）
            $data['willStudents'] = $willStudents;
            $data['willRoomName'] = $this->room->name;
            $data['title'] = '前面还有多少考生';
        }
        return $data;
    }



    /**
     * 查询当前学生是否已考过一个考场
     * @param WatchReminderRepositories
     * @return  object
     * @internal
     * @author zhouqiang
     */

    private function getStudentFinishRoom()
    {
        $studentFinishExam = ExamQueue::where('exam_id', '=', $this->exam->id)
            ->where('exam_screening_id', '=', $this->examScreening->id)
            ->where('student_id', '=', $this->student->id)
            ->where('status', '=', 3)
            ->first();
        return $studentFinishExam;
    }




    /**
     * 获取下一场考试信息
     * @param WatchReminderRepositories
     * @return  object
     * @internal
     * @author zhouqiang
     */

    private function getStudentNextExam()
    {
        $NextQueue = ExamQueue::leftjoin('room', function ($join) {
            $join->on('room.id', '=', 'exam_queue.room_id');
        })
            ->where('exam_queue.exam_id', '=', $this->exam->id)
            ->where('exam_queue.student_id', '=', $this->student->id)
            ->where('exam_queue.exam_screening_id', '=', $this->examScreening->id)
            ->where('exam_queue.status', '=', 0)
            ->orderBy('exam_queue.begin_dt', 'asc')
            ->first();
        \Log::alert('获取到的学生的下一场', [$NextQueue->name]);
        return $NextQueue;
    }


    /**
     * 判断考生是否有完成的考试 是否提示去下一场
     * @param WatchReminderRepositories
     * @return  array
     * @internal
     * @author zhouqiang
     */
    private function getStudentFinishExam($studentFinishExam, $roomInfo, $room)
    {
        $exam_station_station = $this->getTeacherStatus();
        // todo 判断老师是否准备好
        if ($exam_station_station == 0) { //老师准备好
            if (!is_null($studentFinishExam)) {
                $data['code'] = 5;  // 侯考状态（对应界面：请前往下一教室）
                $data['nextExamName'] = $roomInfo->name;
                $data['title'] = '上一场考试已完成,请进入下一考场' . $roomInfo->name;
            } else {
                $data['code'] = 2;  // 侯考状态（对应界面：前面还有多少考生，估计等待时间）
                $data['roomName'] = $room->name;
                $data['title'] = '请进入考场' . $room->name;
            }

        } else { //老师没有准备好
            $data['code'] = 0;  // 侯考状态（对应界面：准备中）
            $data['title'] = '准备中.......';
        }
        return $data;
    }


    /**
     * 抽签
     * @param WatchReminderRepositories
     * @return  redis
     * @internal
     * @author zhouqiang
     */
    public function getchoose()
    {
        //根据当前学生获取NFC——code
        $code = $this->getWatchStatus();
        //根据考试和学生对象获取当前学生所属考站
        \Log::info('抽签获得的考站id',[$this->nowQueue->station_id]);
        if(is_null($this->nowQueue->station_id)){
           \Log::alert('抽签失败',[$this->nowQueue]);
            throw  new \Exception('抽签失败',-100);
        }
        $studentStationName = $this->nowQueue->station->name;
        \Log::info('抽签获取到的考站名',[$studentStationName]);
        $data = [
            'code' => 3, // 抽签状态（对应界面：请到XX考站）
            'willStudents' => '',
            'estTime' => '',
            'willRoomName' => '',
            'roomName' => $studentStationName,
            'nextExamName' => '',
            'surplus' => '',
            'score' => '',
            'title' => '您将开始进行考试',
        ];
        $this->publishmessage($code->code, $data, 'success');
        return response()->json(
            ['nfc_code' => $code->code, 'data' => $data, 'message' => 'success']
        );
    }

    /**
     * 开始考试
     */
    public function getStartExam()
    {
        //根据当前学生获取NFC——code
        $code = $this->getWatchStatus();

        //根据考试和学生对象获取当前队列
//        $nowQueue = $this->nowQueue;
        //计算当前考试时长
        //1.理论考试
//        if($this->station->type == 3){
//            $paper = ExamPaperExamStation::leftjoin('exam_paper',function($paper){
//                $paper->on('exam_paper.id','=','exam_paper_exam_station.exam_paper_id');
//            })->where('exam_paper_exam_station.exam_id','=',$this->exam->id)
//                ->where('exam_paper_exam_station.station_id','=',$this->station->id)->first();
//            $time = $paper->length;
//        }
//        else{
        //技能或SP
//            $ExamDraft = ExamDraft::join('exam_draft_flow', 'exam_draft_flow.id', '=', 'exam_draft.exam_draft_flow_id')
//                ->where('exam_draft_flow.exam_id', '=', $this->exam->id)
//                ->where('exam_draft.station_id', '=', $this->station->id)
//                ->first();
//            \Log::alert('拿到科目数据', [$ExamDraft]);
//            if (!is_null($ExamDraft)) {
//                $subject = Subject::where('id', $ExamDraft->subject_id)->first();
//
//                if (!is_null($subject)) {
//                    \Log::alert('科目时间',[$subject]);
//                    $time = $subject->mins;
//                }
//            }
//        }
        //dd($time);
        $surplus = strtotime($this->nowQueue->end_dt) - time();
        if ($surplus <= 0) {
            $surplus = 0;
        }

        $data = [
            'code' => 4, //  // 侯考状态（对应界面：考试中还剩多少时间）
            'willStudents' => '',
            'estTime' => '',
            'willRoomName' => '',
            'roomName' => '',
            'nextExamName' => '',
            'surplus' => $surplus,
            'score' => '',
            'title' => '正在考试中......',
        ];

        $this->publishmessage($code->code, $data, 'success');
        return response()->json(
            ['nfc_code' => $code->code, 'data' => $data, 'message' => 'success']
        );
    }


    /**
     * 结束考试
     */
    public function endExam()
    {
        //获取当前学生
        $student = $this->student;
        //获取当前场次
        $screen = $this->examScreening;
        //判定是否实时发布成绩
        $data = [
            'code' => 6, //  // 侯考状态（对应界面：考试中还剩多少时间）
            'willStudents' => '',
            'estTime' => '',
            'willRoomName' => '',
            'roomName' => '',
            'nextExamName' => '',
            'surplus' => '',
            'score' => '',
            'title' => '',
        ];

        $data['code'] = 6;
        $data['score'] = '';
        $data['title'] = '场次考试已完成,请归还腕表';

        if ($this->exam->real_push == 1 && $this->exam->status == 2) {
            $TestResultModel = new TestResult();
            $score = $TestResultModel->AcquireExam($student->id, $screen->id);

            $data['code'] = 6;
            $data['score'] = $score;
            $data['title'] = '考试完成，总成绩为';
        } elseif ($this->exam->status == 2) {
            $data['code'] = 6;
            $data['score'] = '';
            $data['title'] = '考试完成';
        }

        $watchNfcCode = ExamScreeningStudent::leftJoin('watch', 'exam_screening_student.watch_id', '=', 'watch.id')
            ->where('exam_screening_student.exam_screening_id', $screen->id)
            ->where('exam_screening_student.student_id', $student->id)
            ->select(['watch.code'])
            ->first();//获取学生对应的nfc_code
        if (is_null($watchNfcCode)) {
            throw new \Exception('未找到对应的腕表nfc_code');
        }

        $this->publishmessage($watchNfcCode->code, $data, $data['title']);
        return response()->json(
            ['nfc_code' => $watchNfcCode, 'data' => $data, 'message' => 'success']
        );
    }


    /**
     * 推送
     */
    private function publishmessage($watchNfcCode, $data, $message)
    {
        \Log::debug('腕表推送结果调试记录', [$message, $data, $watchNfcCode, md5($_SERVER['HTTP_HOST']) . 'watch_message']);

        $json = [
            'code'  => $watchNfcCode,
            'data'      => $data,
            'message'   => $message,
        ];
        Common::watchPublish(md5($_SERVER['HTTP_HOST']) . 'watch_message', $json);

//        $this->redis->publish(md5($_SERVER['HTTP_HOST']) . 'watch_message', json_encode([
//            'nfc_code' => $watchNfcCode,
//            'data' => $data,
//            'message' => $message,
//        ]));
        return response()->json(
            ['nfc_code' => $watchNfcCode, 'data' => $data, 'message' => 'success']
        );
    }

    /**
     * 判断当前场次下的考站是否完全准备好
     */
    public function getScreenStatus()
    {
        //当前场次下的考站数量
        $stationNum = ExamStationStatus::where('exam_screening_id', '=', $this->examScreening->id)
            ->where('status', '=', 0)->get();
        if ($stationNum->isEmpty()) {
            return true;
        } else {
            return false;
        }
    }

    public function getWatchPublish($examId=null,$studentId=null, $stationId=null, $roomId=null)
    {

        $exam = Exam::doingExam($examId);  //拿到考试实例
        $student = null;
        $station = null;
        $room = null;
        if (!is_null($studentId)) {
            $student = Student::find($studentId); //拿到考生实例
        }
        if (!is_null($stationId)) {
            $station = Station::find($stationId);//拿到考站实例
//           Common::valueIsNull($station, -1, '获取考站实例失败');
        }

        if (!is_null($roomId)) {
            $room = Room::find($roomId);//拿到考场实例
        }
  
        \Log::debug('传送给腕表的数据', [$exam, $student, $room, $station]);
        $this->getStudentExamReminder($exam, $student, $room, $station );

//       return response()->json(
//           ['nfc_code' => $watchNfcCode, 'data' => $data, 'message' => 'success']
//       );

    }


}