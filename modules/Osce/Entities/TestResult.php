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
    protected $fillable = ['student_id', 'exam_screening_id', 'station_id', 'begin_dt', 'end_dt', 'time', 'score', 'score_dt', 'teacher_id', 'create_user_id', 'evaluate', 'operation', 'skilled', 'patient', 'affinity'];

    //������ѧ����
    public function student()
    {
        return $this->hasOne('\Modules\Osce\Entities\Student', 'id', 'student_id');
    }

    //��������վ��
    public function  station()
    {

        return $this->hasOne('Modules\Osce\Entities\Station', 'id', 'station_id');

    }

    //���������Գ��α�
    public function  examScreening()
    {
        return $this->hasOne('Modules\Osce\Entities\ExamScreening', 'id', 'exam_screening_id');
    }


    public function addTestResult($data,$score)
    {
        $connection = DB::connection($this->connection);
        $connection->beginTransaction();
        try {
            //判断成绩是否已提交过
            $this->getRemoveScore($data['station_id'],$data['student_id'],$data['exam_screening_id']);
//            $examResult = $this->where('student_id', '=', $data['student_id'])
//                ->where('exam_screening_id', '=', $data['exam_screening_id'])
//                ->where('station_id', '=', $data['station_id'])
//                ->count();
//            if ($examResult > 0) {
//                throw new \Exception('该成绩已提交过', -7);
//            }
            $scoreData = $this->getExamResult($score);
            //拿到总成绩
            $total  =   array_pluck($scoreData,'score');
            $total  =   array_sum($total);
            $data['score']  =   $total;
            if ($testResult = $this->create($data)) {
                //保存成绩评分
                $ExamResultId = $testResult->id;
                $scoreConserve = $this->getSaveExamEvaluate($scoreData, $ExamResultId);
            } else {
                throw new \Exception('成绩提交失败',-6);
            }
            $connection->commit();
            return $testResult;
        } catch (\Exception $ex) {
            $connection->rollBack();
            throw $ex;
        }

    }

    private function  getSaveExamEvaluate($scoreData, $ExamResultId)
    {
        foreach ($scoreData as &$item) {
            $item['exam_result_id']=$ExamResultId;
            //$result=$connection->table('exam_score')->insert($data);;
            $examScore=ExamScore::create($item);
            if(!$examScore)
            {
                throw new \Exception('保存分数详情失败',-5);
            }
        }
    }

    //删除已提交过得成绩
    private function getRemoveScore($stationId,$studentId,$examscreeningId){
        //判断成绩是否已提交过
        try{
        $examResult = $this->where('student_id', '=',$stationId )
            ->where('exam_screening_id', '=', $studentId)
            ->where('station_id', '=',$examscreeningId)
            ->first();
            if($examResult){
          //拿到考试结果id去exam_score中删除数据
                if(!$examResult->examScore()->delete())
                {
                    throw new \Exception('舍弃考试评分详情失败',-1100);
                }
                    if(!$examResult->delect()) {
                        throw new \Exception('舍弃考试成绩失败',-1200);
                    }
            }
        }catch (\Exception $ex){
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
    public function AcquireExam($studentId)
    {
        if (empty($studentId)) {
            return NULL;
        } else {

            $studentExamScore = TestResult::where('student_id', '=', $studentId)->select('score')->get()->toArray();
            $StudentScores = 0;
            foreach ($studentExamScore as $val) {
                $StudentScores += $val['score'];
            }
            return $StudentScores;
        }

    }

    //获取考试成绩打分详情
    private function  getExamResult($score)
    {
        $list = [];
        $arr = json_decode($score, true);
        foreach ($arr as $item) {
            foreach ($item['test_term'] as $str) {
                $list [] = [
                    'subject_id' => $str['subject_id'],
                    'standard_id' => $str['id'],
                    'score' =>$str['real'],
                ];
            }
        }
        return $list;

    }


}