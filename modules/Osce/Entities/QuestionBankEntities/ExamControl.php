<?php
/**
 * Created by PhpStorm.
 * @author tangjun <tangjun@misrobot.com>
 * @date 2016年3月9日11:02:12
 * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
 */
namespace Modules\Osce\Entities\QuestionBankEntities;
use Illuminate\Database\Eloquent\Model;
use DB;
use Modules\Osce\Entities\Exam;
use Modules\Osce\Entities\ExamDraftFlow;
use Modules\Osce\Entities\ExamGradation;
use Modules\Osce\Entities\ExamOrder;
use Modules\Osce\Entities\ExamPlan;
use Modules\Osce\Entities\ExamQueue;
use Modules\Osce\Entities\ExamResult;
use Modules\Osce\Entities\ExamScreeningStudent;
use Modules\Osce\Entities\StationTeacher;
use Modules\Osce\Entities\StationVcr;
use Modules\Osce\Entities\Student;

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
     * @author xumin <xumin@misrobot.com>
     * @date
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function getDoingExamList(){
        $DB = \DB::connection('osce_mis');
        $examModel = new Exam();
        //考试名称
        $exam = $examModel->where('status','=',1)->first();
        if(!empty($exam)){
            //统计该场考试的考站数量
            $stationCount = count($examModel->leftJoin('exam_draft_flow', function($join){
                $join -> on('exam.id', '=', 'exam_draft_flow.exam_id');
            })->leftJoin('exam_draft', function($join){//考试;
                $join -> on('exam_draft_flow.id', '=','exam_draft.exam_draft_flow_id');
            })->leftJoin('station', function($join){//考试;
                $join -> on('exam_draft.station_id', '=','station.id');
            })->select('station.id')->groupBy('station.id')->where('exam.status','=',1)->get());

            $examScreeningStudentModel = new ExamScreeningStudent();
            //统计学生数量
            $studentCount = $examScreeningStudentModel->leftJoin('student', function($join){//学生表
                $join -> on('student.id', '=', 'exam_screening_student.student_id');
            })->leftJoin('exam', function($join){
                $join -> on('exam.id', '=','student.exam_id');
            })->leftJoin('exam_queue', function($join){
                $join -> on('exam_queue.student_id', '=', 'student.id');
            })->leftJoin('station', function($join){
                $join -> on('exam_queue.station_id', '=', 'station.id');
            })->leftJoin('station_teacher', function($join){
                $join -> on('station.id', '=', 'station_teacher.station_id');
            })->groupBy('student.id')->where('exam.status',1)->count();

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
            })->groupBy('student.id')->where('exam.status',1)->where('exam_queue.status',2)->count();


            //统计已完成考试数量
            $endExamCount = $examScreeningStudentModel->leftJoin('student', function($join){
                $join -> on('student.id', '=', 'exam_screening_student.student_id');
            })->leftJoin('exam', function($join){
                $join -> on('exam.id', '=','student.exam_id');
            })->leftJoin('exam_queue', function($join){
                $join -> on('exam_queue.student_id', '=', 'student.id');
            })->leftJoin('station', function($join){
                $join -> on('exam_queue.station_id', '=', 'station.id');
            })->leftJoin('station_teacher', function($join){
                $join -> on('station.id', '=', 'station_teacher.station_id');
            })->groupBy('student.id')->where('exam.status',1)->where('exam_screening_student.is_end',1)->count();


            //正在考试列表
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
            })->groupBy('student.id')->select(
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
                ->get();
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
     * @author xumin <xumin@misrobot.com>
     * @date
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
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
     * @author xumin <xumin@misrobot.com>
     * @date
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function stopExam($data){
        $DB = DB::connection('osce_mis');
        $DB->beginTransaction();
        try{
            //获取该考生的考试队列信息
            $examQueue = ExamQueue::where('student_id', $data['studentId'])
                ->where('exam_id',$data['examId'])
                ->whereNotIn('status',[3,4])->get();

            if (!empty($examQueue)){
                //更新考试队列中的考试监控标记(exam_queue)
                foreach ($examQueue as $val) {
                    $examMonitor = ExamMonitor::where('exam_id',$val->exam_id)->where('student_id',$val->student_id)->where('exam_screening_id',$val->exam_screening_id)->first();
                    if(empty($examMonitor)){
                        //③向监控标记学生替考记录表插入数据
                        $examMonitorData = array(
                            'station_id'  =>$val->station_id,
                            'exam_id'      =>$val->exam_id,
                            'student_id'  =>$val->student_id,
                            'type'         =>$data['type'],
                            'description' =>$data['description'],
                            'exam_screening_id'=>$val->exam_screening_id,

                        );
                        if(!ExamMonitor::create($examMonitorData)){
                            throw new \Exception(' 向监控标记学生替考记录表插入数据失败！',-101);
                        }
                    }

                    //更新exam_order表（考试学生排序）
                    $examOrder = ExamOrder::where('exam_id',$val->exam_id)->where('exam_screening_id',$val->exam_screening_id)->where('student_id',$val->student_id)->first();
                    if(!empty($examOrder)){
                        $examOrderData = array(
                            'status'=>2 //已解绑
                        );
                        if(!ExamOrder::where('id',$examOrder->id)->update($examOrderData)){
                            throw new \Exception(' 更新考试学生排序表失败！',-102);
                        }
                    }
                }
            }/*else{
                throw new \Exception(' 没有该考生的考试队列信息！',-105);
            }*/

            //获取该考生剩余还没考的考站信息
            $remainExamQueueData = $this->getRemainExamQueueData($data['examId'],$data['studentId'],$data['examScreeningId']);
            if(!empty($remainExamQueueData['remainExamQueueInfo'])&&count($remainExamQueueData['remainExamQueueInfo'])>0){
                //如果还有没考的考试信息，结束剩余未考考试，并将分数记为0
                foreach($remainExamQueueData['remainExamQueueInfo'] as $k=>$v){

                    //更新exam_queue表（考试队列）
                    $examQueueData= array(
                        'status'=>3,
                        'blocking'=>1,
                        'controlMark'=>1
                    );
                    if(!ExamQueue::where('id',$v->id)->update($examQueueData)){
                        throw new \Exception(' 更新考试队列表失败！',-103);
                    }

                    //更新exam_screening_student表（考试场次-学生关系表）
                    $result = ExamScreeningStudent::where('exam_screening_id',$v->exam_screening_id)->where('student_id',$v->student_id)->first();
                    if(!empty($result)){
                        $examScreeningStudentData = array(
                            'is_end'=>1,
                            'status' => $data['status'],
                            'description' => $data['description']
                        );
                        if(!ExamScreeningStudent::where('id',$result->id)->update($examScreeningStudentData)){
                            throw new \Exception(' 更新考试场次-学生关系表失败！',-104);
                        }
                    }/*else{
                        throw new \Exception('没有该考生对应的场次！',-103);
                    }*/

                    // 向考试结果记录表(exam_result)插入数据未考考试分数
                    $examResultData=array(
                        'student_id'=>$v['studentId'],
                        'exam_screening_id'=>$v['exam_screening_id'],
                        'station_id'=>$v['station_id'],
                        'time'=>0,
                        'score'=>0,
                        'teacher_id'=>$data['userId'],
                    );
                    if(!ExamResult::create($examResultData)){
                        throw new \Exception(' 插入考试结果记录表失败！',-105);
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



    /*迟到确认弃考*/

    public function stopExamLate($data,$screen_id){

        $DB = DB::connection('osce_mis');
        $DB->beginTransaction();
        try{
            //迟到学生未进对了
            //③ 更新考试场次-学生关系表(exam_screening_student)
            $examScreeningStudentData = array(
                'exam_screening_id'=>$screen_id,
                'student_id'=>$data['studentId'],
                'is_end'=>1,
                'status'=>1,
                'description'=>1
            );
            //保存考试场次-学生关系表（exam_screening_student）
            $examScreeningStudentModel = new ExamScreeningStudent();
            $result = $examScreeningStudentModel->create($examScreeningStudentData);

            if (!$result) {
                throw new \Exception('结束考生考试失败！');
            }

                  $emamPlanMsg=ExamPlan::where('exam_id',$data['examId'])->where('exam_screening_id',$screen_id)->where('student_id',$data['studentId'])->get();
                  if(!is_null($emamPlanMsg)) {
                      foreach ($emamPlanMsg as $val) {
                          $gardaMsg = ExamGradation::where('exam_id', $data['examId'])->where('order', $val->gradation_order)->first();
                          $msg = ExamDraftFlow::leftJoin('exam_draft', 'exam_draft.exam_draft_flow_id', '=', 'exam_draft_flow.id')
                              ->where('exam_draft_flow.exam_gradation_id', $gardaMsg->id)
                              ->where('exam_draft_flow.exam_id', $data['examId'])
                              ->where('exam_draft.room_id', $val->room_id)
                              ->select(['exam_draft.station_id'])
                              ->first();
                          $queueMsg = ExamQueue::where('exam_id', $data['examId'])->where('exam_screening_id', $screen_id)->where('student_id', $data['studentId'])->where('room_id', $val->room_id)->first();
                          if (is_null($queueMsg)) {//排除已考的
                              $teacher_id = StationTeacher::where('station_id', $msg->station_id)->where('exam_id', $data['examId'])->first();
                              //将该考生的场次下成绩记为0
                              //⑤ 向考试结果记录表(exam_result)插入数据
                              $examResultData = array(
                                  'student_id' => $data['studentId'],
                                  'exam_screening_id' => $screen_id,
                                  'time' => 0,
                                  'score' => 0,
                                  'begin_dt' => date('Y-m-d H:i:s', time()),
                                  'end_dt' => date('Y-m-d H:i:s', time()),
                                  'station_id' => $msg->station_id,
                                  'teacher_id' => $teacher_id->user_id,
                                  'operation' => 0, 'skilled' => 0, 'patient' => 0, 'affinity' => 0,
                              );
                              if (!ExamResult::create($examResultData)) {
                                  throw new \Exception(' 插入考试结果记录表失败！');
                              }
                          }
                      }
                  }
   /*                 //④向监控标记学生替考记录表（exam_monitor）中插入数据
                    if(!empty($val['station_id'])){
                        $examMonitorData=array(
                            'station_id'=>$val['station_id'],
                            'exam_id'=>$val['examId'],
                            'student_id'=>$val['studentId'],
                            'type'=>$data['type'],
                            'description'=>$data['description'],
                        );
                        if(!ExamMonitor::create($examMonitorData)){
                            throw new \Exception(' 插入监控标记学生替考记录表失败！');
                        }
                    }*/

            $DB->commit();
            return true;
        }catch (\Exception $ex){
            $DB->rollback();
            throw $ex;
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
     * @author xumin <xumin@misrobot.com>
     * @date
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function getRemainExamQueueData($examId,$studentId,$examScreeningId){
        $examQueueModel = new ExamQueue();
        $examQueueInfo = $examQueueModel->select('exam_id','exam_screening_id','student_id','station_id')
            ->where('exam_id','=',$examId)
            ->where('student_id','=',$studentId)
            ->where('exam_screening_id','<>',$examScreeningId)
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
     * @author xumin <xumin@misrobot.com>
     * @date
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
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