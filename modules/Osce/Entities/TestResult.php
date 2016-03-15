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
             $ExamResult=$this->getRemoveScore($data);

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
//            function($data as $ExamResultKey=>$value ){
//                $ExamResult->$ExamResultKey = $value;
//            }
//            if($ExamResult->save()){
//
//            }
            if ($testResult = $this->create($data)) {
                //保存成绩评分
                $ExamResultId = $testResult->id;
                 $this->getSaveExamEvaluate($scoreData, $ExamResultId);
                 $this->getSaveExamAttach($data['student_id'],$ExamResultId,$score);
            } else {
                throw new \Exception('成绩提交失败',-1000);
            }
            $connection->commit();
            return $testResult;
        } catch (\Exception $ex) {
            $connection->rollBack();
            throw $ex;
        }

    }
    //upload_document_id 音频 图片id集合去修改
    private function getSaveExamAttach($studentId,$ExamResultId,$score){
        try{
            $list = [];
            $arr = json_decode($score, true);
//            \Log::alert($arr);
            foreach($arr as $item){
                $list[]=[
                    'standard_id' =>$item['id']
                ];
            }
            $standardId = array_column($list, 'standard_id');

            if(is_null(TestAttach::whereIn('standard_id',$standardId)->get())){
                throw new \Exception('该考试没有上传图片和音频');
            }
            $AttachData = TestAttach::where('student_id','=',$studentId)->whereIn('standard_id',$standardId)->get();
            foreach($AttachData as $item){
                $item->test_result_id = $ExamResultId;
                if(!$item->save()){
                    throw new \Exception('修改图片音频结果失败',-1400);
                }
            }
        }catch (\Exception $ex){
            \Log::alert($ex->getMessage());
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
                throw new \Exception('保存分数详情失败',-1300);
            }
        }
    }

    //删除已提交过得成绩
    private function getRemoveScore($data){
        //判断成绩是否已提交过
        try{
        $examResult = $this->where('student_id', '=',$data['student_id'] )
            ->where('exam_screening_id', '=', $data['exam_screening_id'])
            ->where('station_id', '=',$data['station_id'])
            ->first();
            if($examResult){
          //拿到考试结果id去exam_score中删除数据
                if(!$examResult->examScore()->delect())
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