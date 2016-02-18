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
            $examResult = $this->where('student_id', '=', $data['student_id'])
                ->where('exam_screening_id', '=', $data['exam_screening_id'])
                ->where('station_id', '=', $data['station_id'])
                ->count();
            if ($examResult > 0) {
                throw new \Exception('该成绩已提交过', -7);
            }
            $scoreData = $this->getExamResult($score);
            //拿到总成绩

            if ($testResult = $this->create($data)) {
                //保存成绩评分
                $ExamResultId = $testResult->id;
                dump($ExamResultId);
                $scoreConserve = $this->getSaveExamEvaluate($scoreData, $ExamResultId);
                dd($scoreConserve);
                if(!$scoreConserve){
                    throw new \Exception('成绩提交失败');
                }
            } else {
                throw new \Exception('成绩提交失败');
            }
//            $connection->commit();
            return $testResult;
        } catch (\Exception $ex) {
            $connection->rollBack();
            throw $ex;
        }

    }

    private function  getSaveExamEvaluate($scoreData, $ExamResultId)
    {
        dump($scoreData);
        $data=[];
        $connection=\DB::connection('osce_mis');
        foreach ($scoreData as $item) {
            $data[]=[
              'exam_result_id'=>$ExamResultId
            ];
            $data=$item;

            $result=$connection->table('exam_score')->insert($data);;

            return $result;
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

    private function  getExamResult($score)
    {
        $list = [];
        $scores = 0;
        $arr = json_decode($score, true);
        foreach ($arr as $item) {
            foreach ($item['test_term'] as $str) {
//                $scores += $str['score'];
//                $list['scores'] = $scores;
                $list [] = [
                    'subject_id' => $str['subject_id'],
                    'standard_id' => $str['id'],
                    'score' => $str['score'],
                ];
            }
        }
        return $list;

    }


}