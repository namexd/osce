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
use Modules\Osce\Entities\ExamResult;
use Modules\Osce\Entities\ExamScreeningStudent;

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
  /*      //统计考站数量
        $stationCount = $examModel->leftJoin('station_teacher', function($join){
            $join -> on('exam.id', '=', 'station_teacher.exam_id');
        })->select(
            $DB->raw('count(station_teacher.station_id) as stationCount')
        )->where('status','=',1)->get();*/
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
            })->groupBy('student.id')->select(
            'student.id',//考生id
            'student.name',//考生姓名
            'student.code',//学号
            'student.exam_sequence',//准考证号
            'student.idcard',//身份证号码
            'exam_screening_student.status',//考试状态
            'station.id as stationId',//考站id
            'station.name as stationName',//考站名称
            'exam_screening_student.is_end',//考试场次终止
            'exam_screening_student.is_replace',//上报替考
            'exam_screening_student.is_give',//上报弃考
            'exam_order.status as examOrderStatus',
            $DB->raw('count(exam_queue.station_id)-1 as stationCount')//剩余考站数量
        )->where('exam.status','=',1)
            ->where('exam_queue.status','<>',3)
        ->get();
        return array(
            'examName'      =>$examName,     //考试名称
            'examInfo'      => $examInfo,    //正在考试列表
            'stationCount' =>$stationCount, //统计考站数量
            'studentCount' =>$studentCount, //统计学生数量
            'doExamCount'  =>$doExamCount,  //统计正在考试数量
            'endExamCount' =>$endExamCount, //统计已完成考试数量
        );
    }



























}