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

    public function examScore(){
        return $this->hasMany('\Modules\Osce\Entities\ExamScore','exam_result_id','id');
    }

    /**
     * 保存成绩
     * @param $data
     * @param $score
     * @param $specialScore
     * @return static
     * @throws \Exception
     */
    public function addTestResult($data,$score, $specialScore)
    {
        $connection = DB::connection($this->connection);
        $connection ->beginTransaction();
        try {
            //判断成绩是否已提交过
            $ExamResult= $this->getRemoveScore($data);
            //获取考试成绩打分详情（解析json为数组）
            $scoreData = $this->getExamResult($score);

            //获取考试成绩特殊评分项扣分详情（解析json为数组）
            $specialScoreData = $this->getSpecialScore($specialScore);
            //拿到特殊评分项总成绩
            $specialTotal  =   array_pluck($scoreData, 'score');
            $specialTotal  =   array_sum($specialTotal);

            //拿到总成绩
            $total  =   array_pluck($scoreData, 'score');
            $total  =   array_sum($total);
            $data['score']  =   $total-$specialTotal;       //总成绩=考核点总得分-特殊评分项总扣除分

            if ($testResult = $this->create($data))
            {
                //保存成绩评分
                $ExamResultId = $testResult->id;        //获取ID
                //保存考试，考核点分数详情
                $this->getSaveExamEvaluate($scoreData, $ExamResultId);

                //保存考试，特殊评分项 实际扣分详情 TODO: Zhoufuxiang
                $this->getSaveSpecialScore($specialScoreData, $ExamResultId);
                \Log::debug('特殊评分项',$specialScoreData);
                //保存语音 图片
                $this->getSaveExamAttach($data['student_id'], $ExamResultId, $score);

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
    private function getSaveExamAttach($studentId,$ExamResultId,$score)
    {
        try{
            $list = [];
            $arr = json_decode($score, true);
//            \Log::alert($arr);
            foreach($arr as $item){
                $list[]=[
                    'standard_item_id' => $item['id']
                ];
            }
            $standardItemId = array_column($list, 'standard_item_id');

            if(is_null(TestAttach::whereIn('standard_item_id', $standardItemId)->get()))
            {
                throw new \Exception('该考试没有上传图片和音频');
            }

            $AttachData = TestAttach::where('student_id','=',$studentId)->whereIn('standard_item_id',$standardItemId)->get();
            foreach($AttachData as $item)
            {
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
            $examScore=ExamScore::create($item);
            if(!$examScore)
            {
                throw new \Exception('保存分数详情失败',-1300);
            }
        }
    }

    /**
     * 保存考试，特殊评分项 实际扣分详情
     * @param $specialScoreDatas
     * @param $ExamResultId
     * @throws \Exception
     *
     * @author Zhoufuxiang <zhoufuxiang@misrobot.com>
     * @date   2016-05-07 16:44
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    private function  getSaveSpecialScore($specialScoreDatas, $ExamResultId)
    {
        if(count($specialScoreDatas)>0)
        {
            foreach ($specialScoreDatas as &$item) {
                $item['exam_result_id'] = $ExamResultId;
                $examSpecialScore       = ExamSpecialScore::create($item);
                if(!$examSpecialScore)
                {
                    throw new \Exception('保存特殊评分项 实际扣分详情失败',-1511);
                }
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
            if($examResult)
            {
                //拿到考试结果id去exam_score中删除数据
                if(!$examResult->examScore()->delete())
                {
                    throw new \Exception('舍弃考试评分详情失败',-1100);
                }
                if(!$examResult->delete())
                {
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
     * @param $studentExamScreeningIdArr 该考生在该场考试所对应所有场次id
     * @return
     * @throws \Exception
     * @author zhouqiang
     */
    public function AcquireExam($studentId, $studentExamScreeningIdArr)
    {
        if (empty($studentId)) {
            return NULL;
        } else {

            $studentExamScore = TestResult::where('student_id', '=', $studentId)
                                          ->whereIn('exam_screening_id',$studentExamScreeningIdArr)
                                          ->select('score')->get()->toArray();
            $StudentScores = 0;
            foreach ($studentExamScore as $val) {
                $StudentScores += $val['score'];
            }
            return $StudentScores;
        }

    }







    //获取考试成绩打分详情（解析json为数组）
    private function  getExamResult($score)
    {
        $list = [];
        $arr = json_decode($score, true);
        foreach ($arr as $item) {
            foreach ($item['test_term'] as $str)
            {
                $list [] = [
                    'subject_id'        => $str['subject_id'],
                    'standard_item_id'  => $str['id'],
                    'score'             => $str['real'],
                ];
            }
        }
        return $list;
    }

    /**
     * 获取考试成绩特殊评分项扣分详情（解析json为数组）
     * @param $specialScores
     * @return array
     *
     * @author Zhoufuxiang <zhoufuxiang@misrobot.com>
     * @date   2016-05-07 16:44
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    private function getSpecialScore($specialScores)
    {
        $list = [];
        $arr = json_decode($specialScores, true);
        \Log::debug('特殊评分项解析',[$arr]);
        if(!empty($arr))
        {
            foreach ($arr as $item)
            {
                $list [] = [
                    'subject_id'        => $item['subject_id'],
                    'special_score_id'  => $item['id'],
                    'score'             => $item['subtract'],
                ];
            }
        }
        return $list;
    }


}