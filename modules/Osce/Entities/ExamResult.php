<?php
/**
 * Created by PhpStorm.
 * User: j5110
 * Date: 2016/1/26
 * Time: 19:22
 */

namespace Modules\Osce\Entities;


use App\Entities\User;
use App\Repositories\Common;
use DB;

class ExamResult extends CommonModel
{
    protected $connection = 'osce_mis';
    protected $table = 'exam_result';
    public $timestamps = true;
    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $guarded = [];
    protected $hidden = [];
    protected $fillable = ['student_id', 'exam_screening_id', 'station_id', 'end_dt', 'begin_dt', 'time',
        'create_user_id', 'score', 'score_dt', 'teacher_id','evaluate','operation','skilled','patient','affinity'];

    public function examScreening(){
        return $this->hasOne('\Modules\Osce\Entities\ExamScreening','id','exam_screening_id');
    }

    public function student(){
        return $this->hasOne('\Modules\Osce\Entities\Student','id','student_id');
    }

    public function teacher(){
        return $this->hasOne('\Modules\Osce\Entities\Teacher','id','teacher_id');
    }

    public function getResultList($examId,$stationId,$name){

        $builder=$this->leftJoin('student', function($join){
            $join -> on('student.id', '=', 'exam_result.student_id');
        })-> leftJoin('exam_screening', function($join){
            $join -> on('exam_result.exam_screening_id', '=', 'exam_screening.id');
        })-> leftJoin('exam', function($join){
            $join -> on('exam_screening.exam_id', '=', 'exam.id');
        })-> leftJoin('station', function($join){
            $join -> on('exam_result.station_id', '=', 'station.id');
        });

        if($examId){
            $builder=$builder->where('exam.id',$examId);
        }
        if($stationId){
            $builder=$builder->where('station.id',$stationId);
        }
        if($name){
            $builder=$builder->where('student.name', 'like', '%'.$name.'%');
        }

        $builder=$builder->select([
            'exam_result.id as id',
            'exam.name as exam_name',
            'exam_result.begin_dt as begin_dt',
            'exam_result.end_dt as end_dt',
            'exam_result.time as time',
            'exam_result.score as score',
            'student.name as student_name',
            'station.name as station_name',
        ])->paginate(config('osce.page_size'));

        $data=$builder;
        return $data;
    }

    /**
     * 考试成绩实时推送
     */
    public function examResultPush($student_id, $url = '')
    {
        try {
            //考生信息
            $student  = Student::where('id', $student_id)->select(['user_id','exam_id', 'name'])->first();
            if(!$student){
                throw new \Exception(' 没有找到该考生信息！');
            }
            //对应考试信息
            $exam = Exam::where('id', $student->exam_id)->select(['name'])->first();
            if(!$exam){
                throw new \Exception(' 没有找到对应考试信息！');
            }
            //用户信息
            $userInfo = User::where('id', $student->user_id)->select(['name', 'openid'])->first();
            if($userInfo){
                if(!empty($userInfo->openid)){
                    //查询总成绩
                    $testResult = new TestResult();
                    $examResult = $testResult->AcquireExam($student_id);
                    //成绩详情url地址
                    if($url == ''){
                        $url = route('osce.wechat.student-exam-query.getResultsQueryIndex',['exam_id'=>$student->exam_id,'student_id'=>$student_id]);
                    }
                    $msgData = [
                        [
                            'title' => '考试成绩查看',
                            'desc'  => $userInfo->name.'同学的 '.$exam->name.' 考试的总成绩为：'.$examResult.'分',
                            'url'   => $url,
                        ],
                    ];
                    $message = Common::CreateWeiXinMessage($msgData);
                    Common::sendWeiXin($userInfo->openid, $message);    //单发

                }else{
                    throw new \Exception($userInfo->name.' 没有关联微信号');
                }
            }else{
                throw new \Exception(' 没有找到该考生对应的用户信息！');
            }

        } catch (\Exception $ex) {
            throw $ex;
        }
    }


    public function getResultDetail($id){

        $builder=$this-> leftJoin('exam_screening', function($join){
            $join -> on('exam_result.exam_screening_id', '=', 'exam_screening.id');
        })-> leftJoin('exam', function($join){
            $join -> on('exam_screening.exam_id', '=', 'exam.id');
        })-> leftJoin('station', function($join){
            $join -> on('exam_result.station_id', '=', 'station.id');
        })-> leftJoin('subject', function($join){
            $join -> on('subject.id', '=', 'station.subject_id');
        });

        $builder=$builder->where('exam_result.id',$id);
        $builder=$builder->select([
            'exam_result.id as id',
            'exam.name as exam_name',
            'exam.id as exam_id',
            'exam_result.begin_dt as begin_dt',
            'exam_result.end_dt as end_dt',
            'exam_result.time as time',
            'exam_result.score as score',
            'exam_result.station_id as station_id',
            'exam_result.student_id as student_id',
            'exam_result.teacher_id as teacher_id',
            'exam_result.evaluate as evaluate',
            'exam_result.operation as operation',
            'exam_result.skilled as skilled',
            'exam_result.patient as patient',
            'exam_result.affinity as affinity',
            'subject.title as subject_title',
            'subject.id as subject_id',
        ])->get();

        return $builder;
    }

//    public function getStudent($stationId,$subjectId){
//
//        $builder=$this-> leftJoin('station', function($join){
//            $join -> on('station.id', '=', 'exam_result.station_id');
//        })-> leftJoin('subject', function($join){
//            $join -> on('subject.id', '=', 'station.subject_id');
//        });
//
//        $builder=$builder->select([
//            'exam_result.student_id as student_id'
//        ])->get();
//
//        return $builder;
//    }




    /**
     *  微信端学生每一个考站成绩查询
     * @param Request $request
     * @author zhouqiang
     * @return \Illuminate\View\View
     */


    public function stationInfo($examScreeningIds){

     $builder=$this->leftJoin('station', function($join){
         $join -> on('station.id', '=', 'exam_result.station_id');
     })-> leftJoin('teacher', function($join){
         $join -> on('teacher.id', '=', 'exam_result.teacher_id');
     })
      ->whereIn('exam_result.exam_screening_id',$examScreeningIds)
         ->select([
         'exam_result.id as exam_result_id ',
         'exam_result.station_id as id',
         'exam_result.score as score',
         'exam_result.time as time',
         'teacher.name as grade_teacher',
         'station.type as type',
         'station.name as station_name',
         'exam_result.exam_screening_id as exam_screening_id',
         'station.id as station_id'

     ])
         ->get();

     return $builder;
    }

    /**
     *  pc端学生成绩查询
     * @param $studentId
     * @return \Illuminate\View\View
     * @author zhouqiang
     */

    public function getstudentData($studentId){

        $builder=$this->leftJoin('student', function($join){
            $join -> on('student.id', '=', 'exam_result.student_id');
        })-> leftJoin('teacher', function($join){
            $join -> on('teacher.id', '=', 'exam_result.teacher_id');
        })-> leftJoin('exam', function($join){
            $join -> on('exam.id', '=', 'student.exam_id');
        })
            ->leftJoin('station','station.id','=','exam_result.station_id')
            ->leftJoin('subject','subject.id','=','station.subject_id');
        $builder=$builder->where('exam_result.student_id',$studentId);
        $builder=$builder->select([
            'exam_result.station_id as id',
            'exam_result.score as score',
            'exam_result.time as time',
            'teacher.name as grade_teacher',
            'student.id as student_id',
            'student.name as student_name',
            'student.code as student_code',
            'exam.id as exam_id',
            'exam.name as exam_name',
            'exam.begin_dt as begin_dt',
            'exam.end_dt as end_dt',
            'subject.title as title',
            'station.id as station_id'
        ])
            ->paginate(config('osce.page_size'));

        return $builder;

    }



}