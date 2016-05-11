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
    private function groupResultScore($scoreJsonOb){
        $score      =   [];
        $special    =   [];
        foreach($scoreJsonOb as $option)
        {
            \Log::info('PAD提交过来的分数json对象',[$option]);
            if($option['tag']=='normal')
            {
                $score[]    =   $option;
            }
            else
            {
                $special[]  =   $option;
            }
        }
        return [
            'score'     =>  $score,
            'special'   =>  $special,
        ];
    }
    public function addTestResult($data,$score)
    {
        $connection = DB::connection($this->connection);
        $connection ->beginTransaction();
        $score  =   json_decode($score,true);
        if($score==false)
        {
            throw new \Exception('提交的数据格式不合法');
        }
        $groupData      =   $this->groupResultScore($score);

        $examScreening  =   ExamScreening::find($data['exam_screening_id']);
        if(is_null($examScreening))
        {
            throw new \Exception('找不到提交的场次');
        }



        $score          =   $groupData['score'];
        $specialScore   =   $groupData['special'];
        try {
            //判断成绩是否已提交过
            $ExamResult= $this->getRemoveScore($data);
            //获取考试成绩打分详情（解析json为数组）
            $scoreData = $this->getExamResult($score);

            //获取考试成绩特殊评分项扣分详情（解析json为数组）
            $specialScoreData = $this->getSpecialScore($specialScore);

            //todo:增加提交数据校验  20160512 01:02 luohaihua
            //获取当前考试当前场次当前考站下的考试项目
            $subject    =   $this->getSuject($examScreening->exam_id,$examScreening->id,$data['station_id']);

            //校验普通分数
            $this->checkScore($subject,$scoreData);
            //校验特殊分数
            \Log::debug('开始校验特殊评分项',[$specialScoreData]);
            $this->checkSpecialScore($subject,$specialScoreData);

            //拿到特殊评分项总成绩
            $specialTotal  =   array_pluck($specialScoreData, 'score');
            $specialTotal  =   array_sum($specialTotal);

            //拿到总成绩
            $total  =   array_pluck($scoreData, 'score');
            $total  =   array_sum($total);
            $data['score']  =   $total-$specialTotal;       //总成绩=考核点总得分-特殊评分项总扣除分

            if ($testResult = $this->create($data))
            {
                //保存成绩评分
                $ExamResultId = $testResult->id;        //获取ID
                \Log::debug('保存考试数据',[$scoreData,$ExamResultId]);
                //保存考试，考核点分数详情
                $this->getSaveExamEvaluate($scoreData, $ExamResultId);

                //保存考试，特殊评分项 实际扣分详情 TODO: Zhoufuxiang
                \Log::info('特殊评分项保存前记录',[$specialScoreData,$ExamResultId]);
                $this->getSaveSpecialScore($specialScoreData, $ExamResultId);
                \Log::debug('特殊评分项',[$specialScoreData]);
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

    private function getSuject($examId,$examScreeningId,$stationId){
        //获取提交场次
        $ExamScreening  =   ExamScreening::find($examScreeningId);
        \Log::debug('根据条件获取场次',[$ExamScreening]);
        if(is_null($ExamScreening))
        {
            throw new \Exception('场次不存在');
        }
        //获取提交阶段
        $gradation  =   ExamGradation::where('order','=',$ExamScreening->gradation_order)->where('exam_id','=',$examId)->first();
        \Log::debug('根据条件获取阶段',[$gradation]);
        if(is_null($gradation))
        {
            \Log::alert('找不到当前阶段');
            throw new \Exception('找不到当前阶段');
        }
        //获取当前考试当前阶段考站安排
        $ExamDraftInfo   =   ExamDraft::leftJoin('exam_draft_flow', 'exam_draft_flow.id', '=', 'exam_draft.exam_draft_flow_id')
            ->where('exam_draft_flow.exam_id', '=', $examId)
            ->where('exam_draft_flow.exam_gradation_id', '=', $gradation->id)
            ->where('exam_draft.station_id', '=', $stationId)
            ->select(['exam_draft.id','exam_draft_flow.name','exam_draft.station_id','exam_draft.subject_id'])
            ->with('subject')
            ->first();
        \Log::info('根据条件获取到的考站安排情况',[$ExamDraftInfo]);
        if(is_null($ExamDraftInfo))
        {
            throw new \Exception('找不到考站安排');
        }
        \Log::info('根据考站安排获取到的考试项目',[$ExamDraftInfo->subject]);
        return $ExamDraftInfo->subject;
    }
    //根据考试项目检查普通评分数据
    private function checkScore($subject,$score){
        if(is_null($subject))
        {
            throw new \Exception('找不到当前考站下的考试项目');
        }
        $standard       =   $subject     ->  standards   ->  first();
        \Log::info('检查普通数据时查找到的当前评分表名',[$standard]);
        if(is_null($standard))
        {
            throw new \Exception('找不到当前项目下的评分表');
        }
        $standardItems  =   $standard   ->  standardItem;
        \Log::info('提交普通成绩校验-评分表详情清单',[$standardItems,$subject]);
        $scoreList  =   [];
        \Log::info('提交普通成绩校验-提交的数据',[$score]);
        foreach($score as $priont)
        {
            \Log::info('提交普通成绩校验-提交的考核点或考核项数据',[$priont]);
            $scoreList[$priont['standard_item_id']] =   $priont;
        }

        \Log::info('提交普通成绩校验-评分表详情提交数据',[$score,$scoreList]);
        foreach($standardItems as $item)
        {
            \Log::info('标准评分点数据',[$item]);
            if(!array_key_exists($item->id,$scoreList))
            {
                if($item->id==0)
                {
                    throw new \Exception('没有找到考核点'.$item->sort.'的相关成绩,提交失败');
                }
                else
                {
                    throw new \Exception('没有找到考核点'.$item->parent->sort.'-'.$item->sort.'的相关成绩,提交失败');
                }
            }
            else
            {
                $thisScore=$scoreList[$item->id];
                //当前分数小于0检查
                if(intval($thisScore['score'])<0)
                {
                    if($item->id==0)
                    {
                        throw new \Exception('考核点'.$item->sort.'分数不合法,提交失败');
                    }
                    else
                    {
                        \Log::info('标准评分项数据(父级)',[$item->parent]);
                        throw new \Exception('考核点'.$item->parent->sort.'-'.$item->sort.'分数不合法,提交失败');
                    }
                }
                //当前分数大于上限检查
                if(intval($thisScore['score'])>intval($item->score))
                {
                    if($item->id==0)
                    {
                        throw new \Exception('考核点'.$item->sort.'分数不合法,提交失败');
                    }
                    else
                    {
                        \Log::info('上限，标准评分项数据(父级)',[$item->parent]);
                        throw new \Exception('考核点'.$item->parent->sort.'-'.$item->sort.'分数不合法,提交失败');
                    }
                }
            }
        }
        \Log::info('普通数据校验完成',[]);
    }

    //根据考试项目检查普通特殊评分数据
    private function checkSpecialScore($subject,$specialScoreData){
        if(is_null($subject))
        {
            throw new \Exception('找不到当前考站下的考试项目');
        }
        $specialScores  =   [];
        //获取标准特殊评分项清单
        $specials   =   $subject->specials;
        //当有提交有特殊评分项时
        if(count($specialScoreData))
        {
            foreach($specialScoreData as $item)
            {
                $specialScores[$item['id']]=$item;
            }
            \Log::info('提交特殊评分项查询数据',[$specials,$subject]);
            \Log::info('提交特殊评分项校验提交数据',[$specialScoreData]);
            foreach($specials as $special)
            {
                if(!array_key_exists($special->id,$specialScores))
                {
                    throw new \Exception('传入了非法的特殊评分项');
                }

                $score  =   $specialScores[$special->id]['score'];
                if(intval($score)<0)
                {
                    throw new \Exception('特殊评分项分数不合法,提交失败');
                }
                if(intval($score)>intval($special->score))
                {
                    throw new \Exception('特殊评分项分数不合法,提交失败');
                }
            }
        }
        else
        {
            if(count($specials))
            {
                throw new \Exception('提交失败,没有找到特殊评分项成绩');
            }
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
            return true;
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
        //$arr = json_decode($score, true);//todo:罗海华 2016-05-10 调试 提交成绩变更
        $arr    =   $score;
        \Log::debug('成绩打分项',$arr);
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
        //$arr = json_decode($specialScores, true);//todo:罗海华 2016-05-10 调试 提交成绩变更
        $arr    =   $specialScores;
        \Log::debug('特殊评分项解析',[$arr]);
        if(!empty($arr))
        {
            foreach ($arr as $item)
            {
                \Log::info('特殊评分项解析元素',[$item]);
                //$item   =   json_decode($item,true);
                \Log::info('特殊评分项解析json元素',$item);
                $list [] = [
                    'subject_id'        => $item['subject_id'],
                    'special_score_id'  => $item['id'],
                    'score'             => $item['score'],
//                    'subject_id'        =>  $item   -> subject_id,
//                    'special_score_id'  =>  $item   -> id,
//                    'score'             =>  $item   -> subtract
                ];

            }
        }

        \Log::debug('特殊评分项结果',[$list]);
        return $list;
    }


}