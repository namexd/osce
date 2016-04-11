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
use Modules\Osce\Entities\ExamPlanRecord;
use Modules\Osce\Entities\ExamQueue;
use Modules\Osce\Entities\ExamResult;
use Modules\Osce\Entities\ExamScreeningStudent;
use Modules\Osce\Entities\StationVcr;

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
        //统计考站数量
        $stationCount = count($examModel->leftJoin('station_teacher', function($join){
            $join -> on('exam.id', '=', 'station_teacher.exam_id');
        })->select('station_teacher.station_id')->where('exam.status','=',1)->get());
        //统计学生数量
        $studentCount = count($examModel->leftJoin('student', function($join){
            $join -> on('exam.id', '=', 'student.exam_id');
        })->select('student.id')->where('exam.status','=',1)->get());
        //统计正在考试数量
        $doExamCount = count($examModel->leftJoin('exam_queue', function($join){
            $join -> on('exam.id', '=', 'exam_queue.exam_id');
        })->select('exam_queue.id')->where('exam.status','=',1)
            ->where('exam_queue.status','<>',3)
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
            })->leftJoin('exam_order', function($join){//考试学生排序
                $join -> on('exam_order.student_id', '=', 'student.id');
            })->leftJoin('station', function($join){//考站
                $join -> on('exam_queue.station_id', '=', 'station.id');
            })->leftJoin('station_teacher', function($join){//考站-老师关系表
                $join -> on('station.id', '=', 'station_teacher.station_id');
            })/*->leftJoin('exam_monitor', function($join){//考站-老师关系表
                $join -> on('station.id', '=', 'exam_monitor.student_id');
            })*/->groupBy('student.id')->select(
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
    /*        'exam_screening_student.is_replace',//上报替考
            'exam_screening_student.is_give',//上报弃考
            'exam_screening_student.description',//考试终止原因*/
            'exam_screening_student.exam_screening_id',//考试场次编号
            'exam_order.status as examOrderStatus'//考试学生排序状态
        )->where('exam.status','=',1)
            ->where('exam_queue.status','<>',3)
        ->get();



        //查询该考生剩余考站数量和该考生是否有标记
        if(!empty($examInfo)&&count($examInfo)>0){
            foreach($examInfo as $key=>$val){
                $remainExamQueueData = $this->getRemainExamQueueData($val['examId'],$val['studentId'],$val['stationId']);
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
                ->where('station_id','=',$data['stationId'])
                ->update(['controlMark'=>1]);
            if(!$result){
                throw new \Exception(' 更新考试队列中考试监控标记失败！');
            }

            //② 更新该考生考试队列表中该考生剩余考站的状态（exam_queue）

            //获取该考生剩余的考站信息
            $remainExamQueueData = $this->getRemainExamQueueData($data['examId'],$data['studentId'],$data['stationId']);
            if(!empty($remainExamQueueData['examQueueInfo'])&&count($remainExamQueueData['examQueueInfo'])>0){
                foreach($remainExamQueueData['examQueueInfo'] as $k=>$v){
                    $examQueueResult = $examQueueModel->where('exam_id','=',$v['exam_id'])
                        ->where('student_id','=',$v['student_id'])
                        ->where('exam_screening_id','=',$v['exam_screening_id'])
                        ->where('station_id','=',$v['station_id'])
                        ->update(['status'=>3]);
                    if(!$examQueueResult){
                        throw new \Exception(' 更新剩余考试队列状态失败！');
                    }
                }
            }


            //③ 更新考试场次-学生关系表(exam_screening_student)
            $examScreeningStudentData = array(
                'is_end' => 1,
                'status' => $data['status'],
                'description' => $data['description']
            );
            //保存考试场次-学生关系表（exam_screening_student）
            $examScreeningStudentModel = new ExamScreeningStudent();
            $result = $examScreeningStudentModel->where('id','=',$data['examScreeningStudentId'])->update($examScreeningStudentData);

            if (!$result) {
                throw new \Exception('更新考试场次-学生关系表失败！');
            }


            //③ 向考试结果记录表(exam_result) 和 监控标记学生替考记录表（exam_monitor）插入数据
           //若该考生还有其他考站，则将其他考站的分数记录为0
            if(!empty($remainExamQueueData['remainExamQueueInfo'])&&count($remainExamQueueData['remainExamQueueInfo'])>0){
                foreach($remainExamQueueData['remainExamQueueInfo'] as $key=>$val){
                    //向考试结果记录表插入数据
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
                    //监控标记学生替考记录表
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
                }
            }
            $DB->commit();
            return true;
        }catch (\Exception $ex){
            $DB->rollback();
            throw $ex;
        }
    }

    /**获取该考生该场考试剩余的考站数量和考试队列信息
     * @method
     * @url /osce/
     * @access public
     * @param $examId 考试id
     * @param $studentId 考生id
     * @param $stationId 正在进行的考站id
     * @return array
     * @author xumin <xumin@misrobot.com>
     * @date
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function getRemainExamQueueData($examId,$studentId,$stationId){
        $examQueueModel = new ExamQueue();
        $examQueueInfo = $examQueueModel->select('exam_id','exam_screening_id','student_id','station_id')
            ->where('exam_id','=',$examId)
            ->where('student_id','=',$studentId)
            ->where('station_id','<>',$stationId)
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