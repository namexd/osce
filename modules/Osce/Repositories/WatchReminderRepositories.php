<?php
/**
 * Created by PhpStorm.
 * @author tangjun <tangjun@misrobot.com>
 * @date 2016-02-23 14:00
 * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
 */

namespace Modules\Osce\Repositories;
use Illuminate\Support\Facades\Redis;
use Modules\Osce\Entities\Drawlots\DrawlotsRepository;
use Modules\Osce\Entities\ExamQueue;
use Modules\Osce\Entities\ExamScreening;
use Modules\Osce\Entities\ExamScreeningStudent;
use Modules\Osce\Entities\ExamStationStatus;
use Modules\Osce\Entities\QuestionBankEntities\ExamPaperExamStation;
use Modules\Osce\Entities\Room;
use Modules\Osce\Entities\Student;
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
class WatchReminderRepositories  extends BaseRepository
{
    //当前考试实例
    protected $exan;
    //当前请求房间
    protected $room;
    //当前请求学生
    protected $student;
    //当前请求考站
    protected $station;
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


    public function setInitializeData($exam,$student,$room,$station){
        $this->exam     =   $exam;
        $this->student  =   $student;
        $this->room     =   $room;
        $this->station =   $station;
        $this->redis    =   Redis::connection('message');;
        $examScreeningModel     =   new ExamScreening();
        $examScreening          =   $examScreeningModel  ->getExamingScreening($exam->id);
        if(is_null($examScreening))
        {
            $examScreening          =   $examScreeningModel  ->getNearestScreening($exam->id);
        }
        if(is_null($examScreening))
        {
            throw new \Exception('没有找当前的考试场次');
        }
        $this->examScreening =   $examScreening;
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
    public function getStudentExamReminder($exam,$student,$room,$station){
        //初始化
        $this->setInitializeData($exam,$student,$room,$station);
        //判断考试模式
        if($exam->sequence_mode==1){//考场模式
            $this->getRoomExamReminder($exam,$student,$this->nowQueue->exam_screening_id);
        }
        else
        {//考站模式
            $this->getStationExamReminder();
        }
    }


    /**
        考站模式
     */
    public function getStationExamReminder(){
        throw new \Exception('考站模式暂未开发');
    }

    /**
    考场模式
     */
    public function getRoomExamReminder($exam,$student,$examScreening){
        //根据exam、student对象查找队列数据
        $queueList  =   $this->getExamStudentQueueList($exam,$student,$examScreening);
        //根据队列获取学生当前队列
        $queue  =   $this->getSutentNowQueue($queueList);
        //根据当前队列获取当前状态,
        $status = $this->getNoticeStauts($queue,$queueList);
        //并且根据当前状态选择相应操作
        switch ($status){
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
                    $this-> getStartExam();
                    break;
            //结束考试
            case 4:
                    $this->endExam();
                    break;
            default:
                    throw new \Exception('未定义的腕表状态');

        }


    }

    public function getNoticeStauts($queue,$queueList){
        //是否当前队列是否为考站考试正在进行
        if($queue->status == 2){
            //是，返回3
            return  3;
        }
        //是否当前队列是否为考站已抽签
        if($queue->status == 1){
            return  2;
        }

        //是否当前队列是否为待考
        if($queue->status == 0){
            return  0;
        }

        //是否当前队列是否为结束考试
        if($queue   === false)
        {
            return  4;
        }

        //判断房间是否准备好
        $exam_station_station = ExamStationStatus::where('exam_id','=',$queue->exam_id)->where('station_id','=',$queue->station_id)->where('exam_screening_id','=',$queue->exam_screening_id)->first();
        if($exam_station_station->status == 1)
        {
            //准备好了，通知去考场
            return 1;
        }
        else
        {
            //没有准备好，通知等待
            return 0;

        }
    }
    /**
     * @content：根据exam、student对象查找队列数据
     * @author：
     * @createDate：
     */
    public function getExamStudentQueueList($exam,$student,$examScreening){
        $StudentQueueList = ExamQueue::where('exam_id','=',$exam->id)
                            ->where('exam_screening_id','=',$examScreening->id)
                            ->where('student_id','=',$student->id)->get();
        if($StudentQueueList){
            return $StudentQueueList;
        }else{
            return false;
        }
    }

    /**
     * @content：根据队列获取学生当前队列
     * @author：
     * @createDate：
     */
    public function getSutentNowQueue($queueList){
        //获取当前队列
        //获取是否有一条正在进行的考试
        $queue  =   $queueList->where('status',2);
        if($queue->isEmpty())
        {
            //初始化当前队列
            $this->nowQueue =   $queue->first();
            return $queue;
        }



        //获取是否有一条已经抽签的考试
        $queue  =   $queueList->where('status',1);
        if($queue->isEmpty())
        {
            //初始化当前队列
            $this->nowQueue =   $queue->first();
            return $queue;
        }
        //获取已经考完的队列集合
        $queue  =   $queueList->where('status',3);
        //判断是否考完
        if(count($queueList) == count($queue)){
            //如果是，返回 false
            $this->nowQueue =   '';
            return false;
        }else{
            //不是，待考
            $queue = $queue->sortBy('begin_dt','asc');
        }

        //初始化当前队列
        $this->nowQueue =   $queue->first();

        //当初始化不能准确获取考站实例的时候补充初始化考站
        if(is_null($this->station))
        {
            $this->station  =   $this->nowQueue ->station;
        }
        //当初始化不能准确获取考场实例的时候补充初始化考场
        if(is_null($this->room))
        {
            $this->room     =   $this->nowQueue ->room;
        }

        return $this->nowQueue;

        //throw new \Exception('队列数据异常，找不到相应的各种状态数据');
    }
    /**
     * 待考
     */
    public function getWaitings(){
        //获取初始化的当前队列
        $queue = $this->nowQueue;

        $exam = $this->exam;

        //当前房间的考站数量
        $drawlots = new DrawlotsRepository();
        $stationNum = count($drawlots->getStationNum($this->exam->id, $this->room->id, $this->examScreening->id));

        //获取当前同组学生清单
        $studentQueueList = ExamQueue::where('exam_id','=',$exam->id)
                                ->where('exam_screening_id','=',$this->examScreening->id)
                                ->where('room_id','=',$this->room->id)
                                ->where('status',0)
                                ->orderBy('begin_dt','asc')
                                ->take($stationNum)
                                ->get();


        //获取前面还有多少人
        $studentFront = ExamQueue::where('exam_id','=',$exam->id)
            ->where('exam_screening_id','=',$this->examScreening->id)
            ->where('room_id','=',$this->room->id)
            ->where('status',0)
            ->where('student_id','<',$this->student->id)
            ->orderBy('begin_dt','asc')
            ->count();
        if($studentFront <= $stationNum){
            \Log::info('腕表推送出现等待推送中有前面学生小于考站数量的情况');
        }else{
            $willStudents = $studentFront;
        }

        //获取将要去的考场
        $room = Room::find($this->nowQueue->room_id);
        $data = [
            'code' => 1, // 侯考状态（对应界面：前面还有多少考生，估计等待时间）
            'title' => '考生等待信息',
            'willStudents' => $willStudents,
            'estTime' => $this->nowQueue->begin_dt,
            'willRoomName' => $room->name,

        ];

        foreach($studentQueueList as $queueList){
            //根据学生获取NFC _code
            $studentCode = WatchLog::where('student_id','=',$queueList->student_id)
                            ->leftjoin('watch')->select('code')->first()->pluck('code');
            $array[] = $studentCode;
            $this->publishmessage($studentCode,$data,'success');
        }




        return response()->json(
            ['nfc_code' => $array, 'data' => $data, 'message' => 'success']
        );
    }

    /**
     * 通知去考场
     */
    public function getGOtoRoon(){
        //获取初始化的当前队列
        $queue = $this->nowQueue;

        $exam = $this->exam;

        //当前房间的考站数量
        $drawlots = new DrawlotsRepository();
        $stationNum = count($drawlots->getStationNum($this->exam->id, $this->room->id, $this->examScreening->id));

        //获取当前同组学生清单
        $studentQueueList = ExamQueue::where('exam_id','=',$exam->id)
            ->where('exam_screening_id','=',$this->examScreening->id)
            ->where('room_id','=',$this->room->id)
            ->where('status',0)
            ->orderBy('begin_dt','asc')
            ->take($stationNum)
            ->get();
        foreach($studentQueueList as $queueList){
            //根据学生获取NFC _code
            $studentCode[] = WatchLog::where('student_id','=',$queueList->student_id)
                ->leftjoin('watch')->select('code')->first()->pluck('code');
        }

        //获取当前场次老师是否准备好，判断是否通知去考场
        $ScreenStatus = $this->getScreenStatus();

        //获取将要去的考场
        $room = Room::find($this->nowQueue->room_id);
        $data = [
            'code' => 1, // 侯考状态（对应界面：前面还有多少考生，估计等待时间）
            'title' => '您将开始进行考试',
            'willStudents' => '',
            'estTime' => '',
            'willRoomName' => $room->name,

        ];

        //推送消息
        if($ScreenStatus){

                $data = [];
                $data['title'] = '';
                foreach($studentCode as $code){
                    $this->publishmessage($code,$data,'success');
                }

                return response()->json(
                    ['nfc_code' => $studentCode, 'data' => $data, 'message' => 'success']
                );

        }else{
            return response()->json(
                ['nfc_code' => '', 'data' => '', 'message' => 'error']
            );
        }




    }


    /**
     * 抽签
     */
    public function getchoose(){
        //根据当前学生获取NFC——code
        $code = WatchLog::where('student_id','=',$this->student->id)->leftjoin('watch')->first()->pluck('code');
        //根据考试和学生对象获取当前学生所属考站
        $studentStationName = Station::where('id','=',$this->nowQueue->station_id)->first()->pluck('name');
        $data = [
            'code' => 1, // 侯考状态（对应界面：前面还有多少考生，估计等待时间）
            'title' => '您将开始进行考试',
            'willStudents' => '',
            'estTime' => '',
            'willRoomName' => $studentStationName,

        ];

        $this->publishmessage($code,$data,'success');
        return response()->json(
            ['nfc_code' => $code, 'data' => $data, 'message' => 'success']
        );
    }

    /**
     * 开始考试
     */
    public function getStartExam(){
        //根据当前学生获取NFC——code
        $code = WatchLog::where('student_id','=',$this->student->id)->leftjoin('watch')->first()->pluck('code');
        //根据考试和学生对象获取当前队列
        $nowQueue = $this->nowQueue;
        //计算当前考试时长
        //1.理论考试
        if($this->station->type == 1){
            $time = ExamPaperExamStation::where('exam_id','=',$this->exam->id)->where('station_id','=',$this->station->id)->first()->pluck('length');
        }else{
            //技能或SP
            $time = $this->station->mins;
        }
        $data = [
            'code' => 1, // 侯考状态（对应界面：前面还有多少考生，估计等待时间）
            'title' => '您将开始进行考试',
            'willStudents' => '',
            'estTime' => $time,
            'willRoomName' => '',

        ];

        $this->publishmessage($code,$data,'success');
        return response()->json(
            ['nfc_code' => $code, 'data' => $data, 'message' => 'error']
        );
    }


    /**
     * 结束考试
     */
    public function endExam(){
        //获取当前学生
         $student=$this->student;
        //获取当前场次
        $screen=$this->examScreening;
        //推送消息
        $data = [];
        $data['title'] = '考试已完成,请及时归还腕表';
        $data['code'] = '6';
        $watchNfcCode= ExamScreeningStudent::leftJoin('watch','exam_screening_student.watch_id','=','watch.id')
                              ->where('exam_screening_student.exam_screening_id',$screen->id)
                              ->where('exam_screening_student.student_id',$student->id)
                              ->select(['watch.code'])
                              ->first();//获取学生对应的nfc_code
        if(is_null($watchNfcCode)){
            throw new \Exception('未找到对应的腕表nfc_code');
        }
        $this->publishmessage($watchNfcCode->code,$data,$data['title']);
        return response()->json(
            ['nfc_code' => $watchNfcCode, 'data' => $data, 'message' => 'success']
        );
    }


    /**
     * 推送
     */
    private function publishmessage($watchNfcCode,$data,$message){
        $this->redis->publish(md5($_SERVER['HTTP_HOST']) . 'watch_message', json_encode([
            'nfc_code' => $watchNfcCode,
            'data' => $data,
            'message' => $message,
        ]));
    }

    /**
     * 判断当前场次下的考站是否完全准备好
     */
    public function getScreenStatus(){
        //当前场次下的考站数量
        $stationNum = ExamStationStatus::where('exam_screening_id','=',$this->examScreening->id)
                                        ->where('status','=',0)->get();
        if($stationNum->isEmpty()){
            return true;
        }else{
            return false;
        }
    }

   public  function getWatchPublish($studentId, $stationId, $roomId){

       $exam = Exam::doingExam();  //拿到考试实例
       $student = null;
       $station = null;
       $room = null;
       if(!is_null($studentId)){
           $student = Student::find($studentId); //拿到考生实例
       }
       if(!is_null($stationId)){
           $station = Station::find($stationId);//拿到考站实例
//           Common::valueIsNull($station, -1, '获取考站实例失败');
       }

       if(!is_null($roomId)){
           $room = Room::find($roomId);//拿到考场实例
       }
        $this->getStudentExamReminder($exam,$student,$station,$room);

   }


}