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
            $builder=$builder->where('student.name',$name);
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
            $student  = Student::where('id', $student_id)->select(['user_id', 'name'])->first();
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
                        $url = 'http://www.baidu.com';
                    }
                    $msgData = [
                        [
                            'title' => '考试成绩查看',
                            'desc'  => $exam->name.'考试总成绩为：'.$examResult.'分',
                            'url'   => $url,
                        ],
                    ];
                    $message = Common::CreateWeiXinMessage($msgData);
                    Common::sendWeiXin($userInfo->openid, $message);    //单发

                }else{
                    throw new \Exception($userInfo->name.' 没有关联微信号');
                }
            }

        } catch (\Exception $ex) {
            throw $ex;
        }
    }


}