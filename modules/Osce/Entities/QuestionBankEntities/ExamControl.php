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
        $examName = $examModel->select('name')->where('status','=',1)->first();

        //统计该场考试的考站数量
        $stationCount = count($examModel->leftJoin('exam_draft_flow', function($join){
            $join -> on('exam.id', '=', 'exam_draft_flow.exam_id');
        })->leftJoin('exam_draft', function($join){//考试;
            $join -> on('exam_draft_flow.id', '=','exam_draft.exam_draft_flow_id');
        })->select('exam_draft.station_id')->where('exam.status','=',1)->get());


        //统计学生数量
        $studentCount = count($examModel->leftJoin('student', function($join){
            $join -> on('exam.id', '=', 'student.exam_id');
        })->select('student.id')->where('exam.status','=',1)->get());
        //统计正在考试数量
        $doExamCount = count($examModel->leftJoin('exam_queue', function($join){
            $join -> on('exam.id', '=', 'exam_queue.exam_id');
        })->select('exam_queue.id')->where('exam.status','=',1)
            ->where('exam_queue.status','=',2)
            ->get());

        //统计已完成考试数量
        $endExamCount = count($examModel->leftJoin('exam_screening', function($join){
            $join -> on('exam.id', '=', 'exam_screening.exam_id');
        })->leftJoin('exam_screening_student', function($join){
            $join -> on('exam_screening_student.exam_screening_id', '=', 'exam_screening.id');
        })->select('exam_screening_student.id')->where('exam.status','=',1)
            ->where('exam_screening_student.is_end','=',1)
            ->get());

        //正在考试列表
        $examScreeningStudentModel = new ExamScreeningStudent();
        $examInfo = $examScreeningStudentModel->leftJoin('student', function($join){//学生表
                $join -> on('student.id', '=', 'exam_screening_student.student_id');
            })->leftJoin('exam', function($join){//考试;
                $join -> on('exam.id', '=','student.exam_id');
            })->leftJoin('exam_queue', function($join){//考试队列
                $join -> on('exam_queue.student_id', '=', 'student.id');
            })->leftJoin('station', function($join){//考站
                $join -> on('exam_queue.station_id', '=', 'station.id');
            })->leftJoin('station_teacher', function($join){//考站-老师关系表
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
            )->where('exam.status','=',1)
            ->where('exam_queue.status','=',2) //status=2正在考试
            ->get();



        //查询该考生剩余考站数量和该考生是否有标记
        if(!empty($examInfo)&&count($examInfo)>0){
            foreach($examInfo as $key=>$val){
                $remainExamQueueData = $this->getRemainExamQueueData($val['examId'],$val['studentId'],$val['exam_screening_id']);
                $examInfo[$key]['remainStationCount']=$remainExamQueueData['remainStationCount'];
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
            'examName'      =>$examName,     //考试名称
            'examInfo'      => $examInfo,    //正在考试列表
            'stationCount' =>$stationCount, //统计考站数量
            'studentCount' =>$studentCount, //统计学生数量
            'doExamCount'  =>$doExamCount,  //统计正在考试数量
            'endExamCount' =>$endExamCount, //统计已完成考试数量
        );
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

            //①更新考试队列中的考试监控标记(exam_queue)
            $examQueueModel = new ExamQueue();
            $result = $examQueueModel->where('exam_id','=',$data['examId'])
                ->where('student_id','=',$data['studentId'])
                ->where('exam_screening_id','=',$data['examScreeningId'])
                //->where('station_id','=',$data['stationId'])
                ->update(['controlMark'=>1]);
            if(!$result){
                throw new \Exception(' 更新考试队列中考试监控标记失败！');
            }

            //② 更新考试场次-学生关系表(exam_screening_student)
            $examScreeningStudentData = array(
                'status' => $data['status'],
                'description' => $data['description']
            );
            $examScreeningStudentModel = new ExamScreeningStudent();
            $result = $examScreeningStudentModel->where('id','=',$data['examScreeningStudentId'])->update($examScreeningStudentData);
            if (!$result) {
                throw new \Exception('更新考试场次-学生关系表失败！');
            }

            //③向监控标记学生替考记录表插入数据
            $examMonitorData=array(
                'exam_screening_id' =>$data['examScreeningId'],
                'station_id'         =>$data['stationId'],
                'exam_id'             =>$data['examId'],
                'student_id'         =>$data['studentId'],
                'type'                =>$data['type'],
                'description'        =>$data['description'],
            );
            if(!ExamMonitor::create($examMonitorData)){
                throw new \Exception(' 插入监控标记学生替考记录表失败！');
            }

            //获取该考生剩余还没考的考站信息
            $remainExamQueueData = $this->getRemainExamQueueData($data['examId'],$data['studentId'],$data['examScreeningId']);
            if(!empty($remainExamQueueData['remainExamQueueInfo'])&&count($remainExamQueueData['remainExamQueueInfo'])>0){
                //如果还有没考的考试信息，结束剩余未考考试，并将分数记为0
                foreach($remainExamQueueData['remainExamQueueInfo'] as $k=>$v){

                    //④ 更新该考生考试队列表中该考生剩余考站的状态（exam_queue）
                    $examQueueResult = $examQueueModel->where('exam_id','=',$v['exam_id'])
                        ->where('student_id','=',$v['student_id'])
                        ->where('exam_screening_id','=',$v['exam_screening_id'])
                        ->update(['status'=>3]);

                    if(!$examQueueResult){
                        throw new \Exception(' 更新剩余考试队列状态失败！');
                    }

                    //⑤ 向考试结果记录表(exam_result)插入数据未考考试分数
                    $examResultData=array(
                        'student_id'=>$v['studentId'],
                        'exam_screening_id'=>$v['exam_screening_id'],
                        'station_id'=>$v['station_id'],
                        'time'=>0,
                        'score'=>0,
                        'teacher_id'=>$data['userId'],
                    );
                    if(!ExamResult::create($examResultData)){
                        throw new \Exception(' 插入考试结果记录表失败！');
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

    public function stopExamLate($data){

        $DB = DB::connection('osce_mis');
        $DB->beginTransaction();
        try{
            $examQueueModel = new ExamQueue();
            //② 更新该考生考试队列表中该考生剩余考站的状态（exam_queue）
            //获取该考生所有的考站信息
            $examQueueData = $this->getExamQueueData($data['examId'],$data['studentId']);

            if(!empty($examQueueData['examQueueInfo'])&&count($examQueueData['examQueueInfo'])>0){
                foreach($examQueueData['examQueueInfo'] as $k=>$v){
                    $examQueueResult = $examQueueModel->where('exam_id','=',$v['exam_id'])
                        ->where('student_id','=',$v['student_id'])
                        ->where('exam_screening_id','=',$v['exam_screening_id'])
                        ->where('station_id','=',$v['station_id'])
                        ->update(['status'=>3]);
                    if(!$examQueueResult){
                        throw new \Exception(' 更新考试队列状态失败！');
                    }
                }
            }
            //③ 更新考试场次-学生关系表(exam_screening_student)
            $examScreeningStudentData = array(
                //'is_end' => 1,
                'status' => $data['status'],
                'description' => $data['description']
            );
            //保存考试场次-学生关系表（exam_screening_student）
            $examScreeningStudentModel = new ExamScreeningStudent();
            $result = $examScreeningStudentModel->where('id','=',$data['examScreeningStudentId'])->update($examScreeningStudentData);

            if (!$result) {
                throw new \Exception('更新考试场次-学生关系表失败！');
            }

            //将该考生的所有成绩记为0
            if(!empty($examQueueData['examQueueInfo'])&&count($examQueueData['examQueueInfo'])>0){
                foreach($examQueueData['examQueueInfo'] as $key=>$val){
                    //⑤ 向考试结果记录表(exam_result)插入数据
                    $data['userId']=StationTeacher::where('station_id',$val['station_id'])->where('exam_id',$val['exam_id'])->where('exam_screening_id',$val['exam_screening_id'])->pluck('id');
                    $examResultData=array(
                        'student_id'=>$data['studentId'],
                        'exam_screening_id'=>$val['exam_screening_id'],
                        'station_id'=>$val['station_id'],
                        'time'=>0,
                        'score'=>0,
                        'teacher_id'=>$data['userId'],
                    );
                    if(!ExamResult::create($examResultData)){
                        throw new \Exception(' 插入考试结果记录表失败！');
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

                }
            }
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
            ->whereNotIn('status',[2,3])
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