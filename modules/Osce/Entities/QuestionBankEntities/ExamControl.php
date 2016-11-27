<?php
/**
 * Created by PhpStorm.
 * @author tangjun <tangjun@163.com>
 * @date 2016年3月9日11:02:12
 * @copyright 2013-2015 MIS 163.com Inc. All Rights Reserved
 */
namespace Modules\Osce\Entities\QuestionBankEntities;
use Illuminate\Database\Eloquent\Model;
use DB;
use Modules\Osce\Entities\Exam;
use Modules\Osce\Entities\ExamAbsent;
use Modules\Osce\Entities\ExamDraftFlow;
use Modules\Osce\Entities\ExamGradation;
use Modules\Osce\Entities\ExamOrder;
use Modules\Osce\Entities\ExamPlan;
use Modules\Osce\Entities\ExamQueue;
use Modules\Osce\Entities\ExamResult;
use Modules\Osce\Entities\ExamScreening;
use Modules\Osce\Entities\ExamScreeningStudent;
use Modules\Osce\Entities\StationTeacher;
use Modules\Osce\Entities\StationVcr;
use Modules\Osce\Entities\Student;
use Modules\Osce\Repositories\Common;

/**考生答题时，正式试卷模型
 * Class Answer
 * @package Modules\Osce\Entities\QuestionBankEntities
 */
class ExamControl extends Model
{
    protected $connection = 'osce_mis';
    protected $table = 'exam_screening_student';
    public $timestamps = true;
    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $guarded = [];
    protected $hidden = [];
    protected $fillable = ['exam_screening_id', 'student_id', 'is_notity', 'is_signin', 'signin_dt', 'watch_id', 'create_user_id'];

    /**考试监控 - 正在考试信息
     * @method
     * @url /osce/
     * @access public
     * @author xumin <xumin@163.com>
     * @date
     * @copyright 2013-2015 MIS 163.com Inc. All Rights Reserved
     */
    public function getDoingExamList(){
        $DB = \DB::connection('osce_mis');
        $examModel = new Exam();
        //考试名称
        $exam = $examModel->where('status','=',1)->first();
        if(!empty($exam)){
            //统计该场考试的考站数量
            $stationCount = $examModel->leftJoin('exam_draft_flow', function($join){
                $join -> on('exam.id', '=', 'exam_draft_flow.exam_id');
            })->leftJoin('exam_draft', function($join){//考试;
                $join -> on('exam_draft_flow.id', '=','exam_draft.exam_draft_flow_id');
            })->leftJoin('station', function($join){//考试;
                $join -> on('exam_draft.station_id', '=','station.id');
            })->select('station.id')->groupBy('station.id')->where('exam.id',$exam->id)->get();

            $stationCount = count($stationCount);

            $examScreeningStudentModel = new ExamScreeningStudent();
            //统计学生数量
            $studentCount = Student::where('exam_id',$exam->id)->groupBy('student.id')->get();

            $studentCount = count($studentCount);

           //统计正在考试数量
            $doExamCount = $examScreeningStudentModel->leftJoin('student', function($join){//学生表
                $join -> on('student.id', '=', 'exam_screening_student.student_id');
            })->leftJoin('exam', function($join){
                $join -> on('exam.id', '=','student.exam_id');
            })->leftJoin('exam_queue', function($join){
                $join -> on('exam_queue.student_id', '=', 'student.id');
            })->leftJoin('station', function($join){
                $join -> on('exam_queue.station_id', '=', 'station.id');
            })->leftJoin('station_teacher', function($join){
                $join -> on('station.id', '=', 'station_teacher.station_id');
            })->groupBy('student.id')->where('exam.id',$exam->id)->where('exam_queue.status',2)->get();

            $doExamCount = count($doExamCount);
            //获取排考记录中该场考试所对应的学生信息
            $examPlan = ExamPlan::where('exam_id',$exam->id)->groupBy('student_id')->get()->toArray();

            //查询每个考生所所对应的场次数量
            $endExamCount = 0;
            foreach($examPlan as $key=>$val){
                //查询考生所对应的场次数量
                $count = count(ExamPlan::where('exam_id',$val['exam_id'])->where('student_id',$val['student_id'])->groupBy('exam_screening_id')->get()->toArray());
                //查询考生已完成的场次数量
                $finishCount = count(ExamScreeningStudent::where('student_id',$val['student_id'])->where('is_end',1)->groupBy('exam_screening_id')->get()->toArray());
                //查询考试迟到的场次数量
                $examAbsentCount = count(ExamMonitor::where('exam_id',$val['exam_id'])->where('student_id',$val['student_id'])->groupBy('exam_screening_id')->get()->toArray());
                if($finishCount+$examAbsentCount>=$count){
                    $endExamCount++;
                }
            }
            //正在考试列表
//            $a = \DB::connection('osce_mis');
//            $a->enableQueryLog();
            $examInfo = $examScreeningStudentModel->leftJoin('student', function($join){
                $join -> on('student.id', '=', 'exam_screening_student.student_id');
            })->leftJoin('exam', function($join){
                $join -> on('exam.id', '=','student.exam_id');
            })->leftJoin('exam_queue', function($join){
                $join -> on('exam_queue.student_id', '=', 'student.id');
            })->leftJoin('station', function($join){
                $join -> on('exam_queue.station_id', '=', 'station.id');
            })->leftJoin('station_teacher', function($join){
                $join -> on('station.id', '=', 'station_teacher.station_id');
            })->select(
                'exam.id as examId',//考试id
                'student.id as studentId',//考生id
                'student.name',//考生姓名
                'student.code',//学号
                'student.exam_sequence',//准考证号
                'student.idcard',//身份证号码
                'exam_screening_student.status',//考试状态
                'station.id as stationId',//考站id
                'station.name as stationName',//考站名称
                'station.type as stationType',//考站类型
                'station_teacher.user_id as userId',//老师id
                'exam_screening_student.id as examScreeningStudentId',//考试场次-学生关系id
                'exam_screening_student.is_end',//考试场次终止
                'exam_screening_student.exam_screening_id'//考试场次编号
            )->where('exam.status',1)
                ->where('exam_queue.status',2) //status=2正在考试
                ->groupBy('student.id')
                ->get();
//            $b = $a->getQueryLog();

            if(!empty($examInfo)&&count($examInfo)>0){
                foreach($examInfo as $key=>$val){
                    //获取该考生剩余考站数量
                    $remainExamQueueData = $this->getRemainExamQueueData($val['examId'],$val['studentId'],$val['exam_screening_id']);
                    $examInfo[$key]['remainStationCount']=$remainExamQueueData['remainStationCount'];
                    //查询正在考的这次考试是否有标记
                    $examMonitorModel = new ExamMonitor();

                    $examMonitorInfo= $examMonitorModel->where('station_id','=',$val['stationId'])
                        ->where('exam_id','=',$val['examId'])
                        ->where('student_id','=',$val['studentId'])
//                        ->whereIn('type',[1,2])
//                        ->whereNotIn('description',[1,2,3,4])
                        ->orderBy('id','desc')
                        ->first();
                    if(!empty($examMonitorInfo)){
                        $examInfo[$key]['type'] = $examMonitorInfo['type'];
                        $examInfo[$key]['description'] = $examMonitorInfo['description'];

                    }else{

                        $examInfo[$key]['type'] = -1;
                        $examInfo[$key]['description'] = -1;
                    }
                }
            }
//            dd($examInfo);
            return array(
                'examName'      =>$exam,     //考试名称
                'examInfo'      => $examInfo,    //正在考试列表
                'stationCount' =>$stationCount, //统计考站数量
                'studentCount' =>$studentCount, //统计学生数量
                'doExamCount'  =>$doExamCount,  //统计正在考试数量
                'endExamCount' =>$endExamCount, //统计已完成考试数量
            );
        }

    }

    /**获取该考生该场考试所有的考站数量和考试队列信息
     * @method
     * @url /osce/
     * @access public
     * @param $examId 考试id
     * @param $studentId 学生id
     * @return int
     * @author xumin <xumin@163.com>
     * @date
     * @copyright 2013-2015 MIS 163.com Inc. All Rights Reserved
     */
    public function getExamQueueData($examId,$studentId){
        $examQueueModel = new ExamQueue();
        $examQueueInfo = $examQueueModel->select('exam_id','exam_screening_id','student_id','station_id')
            ->where('student_id','=',$studentId)
            ->where('exam_id','=',$examId)
            ->get();
        return array(
            'stationCount' =>count($examQueueInfo),//这次考试该考生对应的考站数量
            'examQueueInfo' =>$examQueueInfo,//这次考试该考生对应的考试队列信息
        );
    }

    /**终止考试数据交互
     * @method
     * @url /osce/
     * @access public
     * @param $data
     * @author xumin <xumin@163.com>
     * @date
     * @copyright 2013-2015 MIS 163.com Inc. All Rights Reserved
     */
    public function stopExam($data){
        $DB = DB::connection('osce_mis');
        $DB->beginTransaction();

        try{
            $examResult  = new ExamResult();
            //获取该考生的考试队列信息
            $abnormal = 1;
            $examQueue = ExamQueue::where('student_id', $data['studentId'])
                ->where('exam_id',$data['examId'])
                ->whereNotIn('status',[3,4])->get();
            if (!empty($examQueue)){
                //更新考试队列中的考试监控标记(exam_queue)
                foreach ($examQueue as $val) {
                    //拿到阶段实例
                    $gardaMsg = ExamGradation::where('exam_id', $data['examId'])->where('order', $val->gradation_order)->first();
                    $msg = ExamDraftFlow::leftJoin('exam_draft', 'exam_draft.exam_draft_flow_id', '=', 'exam_draft_flow.id')
                        ->where('exam_draft_flow.exam_gradation_id', $gardaMsg->id)
                        ->where('exam_draft_flow.exam_id', $data['examId'])
                        ->where('exam_draft.room_id', $val->room_id)
                        ->select(['exam_draft.station_id','exam_draft.room_id'])
                        ->first();

                    if(empty($val->station_id)){
                        $val->station_id =  $msg->station_id;
                    }

                    //调用插入弃考记录表数据
                     $this->ExamMonitor($val->station_id ,$val ->room_id,$val->exam_screening_id,$data);

                    //更新exam_order表（考试学生排序）
                    $examOrder = ExamOrder::where('exam_id',$val->exam_id)->where('exam_screening_id',$val->exam_screening_id)->where('student_id',$val->student_id)->first();
                    if(!empty($examOrder)&&$examOrder->status!=2){
                        $examOrder->status = 3;
                        if(!$examOrder->save()){
                            throw new \Exception(' 更新考试学生排序表失败！',-102);
                        }
                    }
                    //更新exam_queue表（考试队列）
                    if($val->status!=3){
                        $val ->controlMark =  $data['description'];
                        if(!$val->save()){
                            throw new \Exception(' 更新考试队列表失败！',-103);
                        }
                    }
                    //更新exam_screening_student表（考试场次-学生关系表）
                    $result = ExamScreeningStudent::where('exam_screening_id',$val->exam_screening_id)->where('student_id',$val->student_id)->first();
                    if(!empty($result)&&$result->is_end!=1){
                        $result->is_end =1;
                        $result->status =$data['status'];
                        $result->description =$data['description'];
                        if(!$result->save()){
                            throw new \Exception(' 更新考试场次-学生关系表失败！',-104);
                        }
                    }
                    //更新房间当前组
                    Common::updateRoomCache($data['examId'],$val ->room_id);
                    //从缓存中 获取当前组考生队列
                    $key = 'current_room_id' . $val ->room_id .'_exam_id'.$data['examId'];
                    //从缓存中取出 当前组考生队列
                    $cacheExamQueue = \Cache::get($key);
                    \Log::info('异常操作拿到当前组缓存',[$cacheExamQueue]);
                    //检查当前组是否全是替考或者弃考
                    if(count($cacheExamQueue)>0){
                        foreach ($cacheExamQueue as $item){
                            if($item->controlMark = -1 ){
                                $abnormal = 2;
                                break;
                            }
                        }
                    }
                    if($abnormal == 1 ||$val->status !=0){
                        //结束队列
                        $val ->status = 3;
                        if($val->status ==0){
                        $val->station_id =  $msg->station_id;
                        }
                        if($val->save()){
//                            throw new \Exception('结束弃考考生队列失败！',-105);
                            // 向考试结果记录表(exam_result)插入数据未考考试分数
                            //调用创建成绩方法
                            $examResult->AbnormalScore($data['examId'],$val['exam_screening_id'],$data['studentId'],$data['userId'],$val['station_id']);
//                            $examResultData=array(
//                                'student_id'=>$data['studentId'],
//                                'exam_screening_id'=>$val['exam_screening_id'],
//                                'station_id'=>$val['station_id'],
//                                'begin_dt' => date('Y-m-d H:i:s', time()),
//                                'end_dt' => date('Y-m-d H:i:s', time()),
//                                'time'=>0,
//                                'score'=>0,
//                                'original_score'=>0,
//                                'teacher_id'=>$data['userId'],
//                                'flag'=>$data['description'],
//                            );
//                            if(!ExamResult::create($examResultData)){
//                                throw new \Exception(' 插入考试结果记录表失败！',-105);
//                            }
                        }
//
                    }
                }
            }
            $DB->commit();
            return true;
        }catch (\Exception $ex){
            $DB->rollback();
            throw $ex;
        }
    }


    //检查当前组考生是否都是

    //插入记录表数据
    private  function  ExamMonitor($stationId,$roomId,$examscreeningId,$data){
        //检查记录表是否有数据
        $examMonitor = ExamMonitor::where('exam_id',$data['examId'])
            ->where('student_id',$data['studentId'])
            ->where('room_id',$roomId)
            ->where('station_id',$stationId)
            ->where('exam_screening_id',$examscreeningId)
            ->first();
        //组装记录表数据
        $examMonitorData = array(
            'station_id'  =>$stationId,
            'exam_id'      =>$data['examId'],
            'student_id'  =>$data['studentId'],
            'type'         =>$data['type'],
            'description' =>$data['description'],
            'reason' =>$data['reason'],
            'exam_screening_id'=>$examscreeningId,
            'room_id'=>$roomId,
        );

        if(is_null($examMonitor)){
            //向监控标记学生替考记录表插入数据
            if(!ExamMonitor::create($examMonitorData)){
                throw new \Exception(' 向监控标记学生替考记录表插入数据失败！',-101);
            }
        }else{
            $examMonitor->type=$data['type'];
            $examMonitor->description=$data['description'];
            if(!$examMonitor->save()){
                throw new \Exception(' 向监控标记学生替考记录表插入数据失败！',-101);
            }
        }

        // 向考试结果记录表(exam_result)插入数据未考考试分数
//        $examResultData=array(
//            'student_id'=>$data['studentId'],
//            'exam_screening_id'=>$examscreeningId,
//            'station_id'=>$stationId,
//            'begin_dt' => date('Y-m-d H:i:s', time()),
//            'end_dt' => date('Y-m-d H:i:s', time()),
//            'time'=>0,
//            'score'=>0,
//            'original_score'=>0,
//            'teacher_id'=>$data['userId'],
//            'flag'=>$data['description'],
//        );
//        if(!ExamResult::create($examResultData)){
//            throw new \Exception(' 插入考试结果记录表失败！',-105);
//        }



    }
    /**
     * *迟到确认弃考*
     * @access public
     * @param $studentId
     * @return bool
     * @throws \Exception
     * @version 3.6
     * @author zhouqiang <zhouqiang@163.com>
     * @time 2016-06-04
     * @copyright 2013-2016 MIS 163.com Inc. All Rights Reserved
     */
    public function stopExamLate($examId,$studentId, $screen_id,$reason,$replace){
        $DB = DB::connection('osce_mis');
        $DB->beginTransaction();
        try{
            //如果最后都是异常考生就不进入队列直接结束考试
            $replace =  $this-> LateStudentAll($examId,$screen_id,$replace);
            //更新考试场次-学生关系表(exam_screening_student)
             $this->ScreeningStudent ($studentId,$screen_id);
            //检查排考是否只有一个学生 
             $ExamPlan = ExamPlan::where('exam_id','=',$examId)->get();
                $nowTime = time();
                //迟到学生未进队列下一个考生不补上 创建队列
                if($replace == 2){
                    if(count($ExamPlan)>1){
                    $examQue = new ExamQueue();
                    $controlMark = 1;
                    //创建考试队列
                    $examQue ->createExamQueue($examId, $studentId, $nowTime, $screen_id,$controlMark);
                }
            }
            //给迟到考生创建成绩
            $this->getCreatorResult($examId,$studentId,$screen_id,$studentId,$reason,$replace);
            //更新所有考试缓存
            Common::updateAllCache($examId,$screen_id);
            $DB->commit();
            return true;
        }catch (\Exception $ex){
            $DB->rollback();
            throw $ex;
        }
    }


    /**
     * 检查是否只有迟到的学生
     * @access public
     * @param $studentId
     * @return bool
     * @throws \Exception
     * @version 3.6
     * @author zhouqiang <zhouqiang@163.com>
     * @time 2016-06-04
     * @copyright 2013-2016 MIS 163.com Inc. All Rights Reserved
     */

    public function LateStudentAll($examId,$screen_id,$replace ){
        //检查排考是否只有一个学生
        $ExamPlan = ExamPlan::where('exam_id','=',$examId)
            ->where('exam_screening_id','=',$screen_id)
            ->groupBy('student_id')
            ->get();
        if($ExamPlan->isEmpty()){
            throw new \Exception('没有考试安排');
        }
        //查询考试迟到人数
        $LateStudent = ExamOrder::where('exam_id',$examId)
            ->where('status',4)
            ->where('exam_screening_id',$screen_id)
            ->get();
        if($LateStudent->isEmpty()){
            throw new \Exception('迟到考生标记异常');
        }
        //查询出场次考试完成的人数
        $FinishStudent = ExamQueue::where('exam_id',$examId)
            ->where('status',3)
            ->where('exam_screening_id',$screen_id)
            ->groupBy('student_id')
            ->get();
        if(count($ExamPlan) - count($FinishStudent) == count($LateStudent)){
            $replace = 1;
        }
        return $replace ;
    }



    /**
     * 处理学生与场次的关系
     * @access public
     * @param $studentId
     * @return bool
     * @throws \Exception
     * @version 3.6
     * @author zhouqiang <zhouqiang@163.com>
     * @time 2016-06-04
     * @copyright 2013-2016 MIS 163.com Inc. All Rights Reserved
     */

    private function ScreeningStudent ($studentId,$screen_id){
        //检查examScreeningStudent表是否有这条数据
        $ScreeningStudentResult  = ExamScreeningStudent::where('exam_screening_id','=',$screen_id)
            ->where('student_id','=',$studentId)
            ->first();
        //检查学生是否绑定腕表，没有就添加有就修改
        if(is_null($ScreeningStudentResult)){
            $examScreeningStudentData = array(
                'exam_screening_id'=>$screen_id,
                'student_id'=>$studentId,
                'is_end'=>1,
                'status'=>1,
                'description'=>1
            );
            //保存考试场次-学生关系表（exam_screening_student）
            $examScreeningStudentModel = new ExamScreeningStudent();
            $result = $examScreeningStudentModel->create($examScreeningStudentData);
            //创建队列
        }else{
            $ScreeningStudentResult->is_end = 1;
            $ScreeningStudentResult->description = 1;
            $result = $ScreeningStudentResult->save();
        }
        if (!$result) {
            throw new \Exception('结束考生考试失败！');
        }
        return true;
    }




    /**
     * 学生弃考创建成绩
     * @access public
     * @param $studentId
     * @return bool
     * @throws \Exception
     * @version 3.6
     * @author zhouqiang <zhouqiang@163.com>
     * @time 2016-06-04
     * @copyright 2013-2016 MIS 163.com Inc. All Rights Reserved
     */
    private  function getCreatorResult($examId,$studentId,$screen_id,$studentId,$reason,$replace){
        $emamPlanMsg=ExamPlan::where('exam_id',$examId)->where('exam_screening_id',$screen_id)->where('student_id',$studentId)->get();
        //查询时必考还是选考
        if(!is_null($emamPlanMsg)) {
            foreach ($emamPlanMsg as $val) {
                $gardaMsg = ExamGradation::where('exam_id', $examId)->where('order', $val->gradation_order)->first();
                $optional = ExamDraftFlow::where('exam_gradation_id', $gardaMsg->id)
                    ->where('exam_id', $examId)
                    ->select(['exam_draft_flow.optional'])
                    ->first();
                if($optional->optional == 1){
                    $this->MustExam($val->room_id,$gardaMsg->id,$examId,$screen_id,$studentId,$reason,$replace);

                }else{

                    $this->ElectExam($val->room_id,$gardaMsg->id,$examId,$screen_id,$studentId,$reason,$replace);
                }
            }
        }
    }

    /**
     *考站选考方法
     * @access public
     * @param $studentId
     * @return bool
     * @throws \Exception
     * @version 3.6
     * @author zhouqiang <zhouqiang@163.com>
     * @time 2016-06-04
     * @copyright 2013-2016 MIS 163.com Inc. All Rights Reserved
     */


    private function ElectExam($room_id,$gradationId,$examId,$screen_id,$studentId,$reason,$replace){

        $msg = ExamDraftFlow::leftJoin('exam_draft', 'exam_draft.exam_draft_flow_id', '=', 'exam_draft_flow.id')
            ->where('exam_draft_flow.exam_gradation_id', $gradationId)
            ->where('exam_draft_flow.exam_id', $examId)
            ->where('exam_draft.room_id', $room_id)
            ->select(['exam_draft.station_id','exam_draft.room_id'])
            ->first();

        //调用创建缺考记录方法
        $this->CreatorRecord($examId,$screen_id,$studentId,$msg->station_id,$msg->room_id,$reason,$replace);
    }

    private  function  CreatorRecord($examId,$screenId,$studentId,$stationId,$roomId,$reason,$replace){
        $ResultMsg = ExamResult::where('exam_screening_id', $screenId)
            ->where('student_id', $studentId)
            ->where('station_id', $stationId)
//            ->where('room_id', $roomId)
            ->first();
        if($replace == 1 ){
            if (is_null($ResultMsg)) {//排除已考的
                $teacher_id = StationTeacher::where('station_id', $stationId)->where('exam_id', $examId)->first();
                //将该考生的场次下成绩记为0
                //⑤ 向考试结果记录表(exam_result)插入数据
                $examResultData = array(
                    'student_id' => $studentId,
                    'exam_screening_id' => $screenId,
                    'time' => 0,
                    'score' => 0,
                    'original_score' => 0,
                    'begin_dt' => date('Y-m-d H:i:s', time()),
                    'end_dt' => date('Y-m-d H:i:s', time()),
                    'station_id' => $stationId,
                    'teacher_id' => $teacher_id->user_id,
                    'evaluate' =>$reason,
                    'flag' =>2,
                );
                if (!ExamResult::create($examResultData)) {
                    throw new \Exception(' 插入考试结果记录表失败！');
                }
            }
        }

        //④向监控标记学生替考记录表（exam_monitor）中插入数据
        if(!empty($stationId)){
            $examMonitorData=array(
                'station_id'=>$stationId,
                'room_id'=>$roomId,
                'exam_id'=>$examId,
                'student_id'=>$studentId,
                'type'=>2,
                'description'=>1,
                'reason'=>$reason,
                'exam_screening_id' => $screenId,
            );
       
            if(!ExamMonitor::create($examMonitorData)){
                throw new \Exception(' 插入监控标记学生替考记录表失败！');
            }
        }
    }

    //考站必考方法
    private function MustExam($room_id,$gradationId,$examId,$screen_id,$studentId,$reason,$replace){
        $msg = ExamDraftFlow::leftJoin('exam_draft', 'exam_draft.exam_draft_flow_id', '=', 'exam_draft_flow.id')
            ->where('exam_draft_flow.exam_gradation_id', $gradationId)
            ->where('exam_draft_flow.exam_id', $examId)
            ->where('exam_draft.room_id', $room_id)
            ->select(['exam_draft.station_id','exam_draft.room_id'])
            ->get();
        foreach ($msg as $val){
            $this->CreatorRecord($examId,$screen_id,$studentId,$val->station_id,$val->room_id,$reason,$replace);
        }
}

    /**获取该考生该场考试还没开考的剩余的考站数量和考试队列信息
     * @method
     * @url /osce/
     * @access public
     * @param $examId 考试id
     * @param $studentId 学生id
     * @param $examScreeningId 场次id
     * @return array
     * @author xumin <xumin@163.com>
     * @date
     * @copyright 2013-2015 MIS 163.com Inc. All Rights Reserved
     */
    public function getRemainExamQueueData($examId,$studentId,$examScreeningId){
        $examQueueModel = new ExamQueue();


        $examQueueInfo = $examQueueModel->select('exam_id','exam_screening_id','student_id','station_id')
            ->where('exam_id','=',$examId)
            ->where('student_id','=',$studentId)
            ->where('exam_screening_id',$examScreeningId)
            ->whereNotIn('status',[2,3,4])
            ->get();
        return array(
            'remainStationCount' =>count($examQueueInfo),//剩余考站数量
            'remainExamQueueInfo' =>$examQueueInfo,//剩余的考试队列信息
        );
    }


    /**获取摄像头信息
     * @method
     * @url /osce/
     * @access public
     * @param $examId 考试id
     * @param $stationId 考站id
     * @return mixed
     * @author xumin <xumin@163.com>
     * @date
     * @copyright 2013-2015 MIS 163.com Inc. All Rights Reserved
     */
    public function getVcrInfo($examId, $stationId)
    {
        $DB = DB::connection('osce_mis');
        $stationVcrModel = new StationVcr();
        $data = $stationVcrModel->leftJoin('vcr', function($join){
            $join -> on('station_vcr.vcr_id', '=', 'vcr.id');
        })->select(['vcr.id','vcr.name','vcr.ip','vcr.status','vcr.port','vcr.realport','vcr.channel','vcr.username','vcr.password'])
            ->where('station_vcr.exam_id', $examId)
            ->where('station_vcr.station_id', $stationId)
            ->first();
        return $data;
    }






}