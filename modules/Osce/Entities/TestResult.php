<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/1/14 0014
 * Time: 14:44
 */

namespace Modules\Osce\Entities;
use DB;

class TestResult extends CommonModel
{
    protected $connection = 'osce_mis';
    protected $table = 'exam_result';
    public $timestamps = true;
    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $guarded = [];
    protected $hidden = [];
    protected $fillable = ['student_id', 'exam_screening_id', 'station_id', 'begin_dt', 'end_dt','time','score','score_dt','teacher_id','create_user_id'];

   //������ѧ����
    public function student(){
        return $this->hasOne('\Modules\Osce\Entities\Student','id','student_id');
    }

    //��������վ��
    public  function  station(){
        return $this->hasOne('Modules\Osce\Entities\Station','id','station_id');

    }
    //���������Գ��α�
    public  function  examScreening(){
         return $this->hasOne('Modules\Osce\Entities\ExamScreening','id','exam_screening_id');
    }


    public function addTestResult($data)
    {
        $connection = DB::connection($this->connection);
        $connection->beginTransaction();
        try {
            $TestResultData = [];
            if ($testResult = $this->create($data)) {
                $TestResultData = [
                    'item_id' => $testResult->id,
                    'type' => 1,
                ];
            } else {
                throw new \Exception('��������ʧ��');
            }
            if (empty($TestResultData)) {
                throw new \Exception('û���ҵ�������������');
            }

            $connection->commit();
            return $testResult;
        } catch (\Exception $ex) {
            $connection->rollBack();
            throw $ex;
        }

    }


    /**
     *获取学生考试最终成绩
     * @param $studentId
     * @return
     * @throws \Exception
     * @author zhouqiang
     */
     public function AcquireExam($studentId){
        if(empty($studentId)){
              return false;
        }else{

          $studentExamScore = TestResult::where('student_id','=',$studentId)->select('score')->first();

            return   $studentExamScore;
        }

     }




}