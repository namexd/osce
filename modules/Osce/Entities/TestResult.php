<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/1/14 0014
 * Time: 14:44
 */

namespace Modules\Osce\Entities;

use DB;
use Illuminate\Support\Facades\Log;

class TestResult extends CommModel
{
    protected $connection = 'osce_mis';
    protected $table = 'exam_result';
    public $timestamps = true;
    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $guarded = [];
    protected $hidden = [];
    protected $fillable = [
        'student_id', 'exam_screening_id', 'station_id', 'end_dt',   'begin_dt',  'time',    'score',
        'score_dt',   'create_user_id',    'teacher_id', 'evaluate', 'operation', 'skilled', 'patient',
        'affinity',   'original_score',    'flag', 'subject_id'
    ];

    //������ѧ����
    public function student()
    {
        return $this->hasOne('\Modules\Osce\Entities\Student', 'id', 'student_id');
    }

    //��������վ��
    public function station()
    {

        return $this->hasOne('Modules\Osce\Entities\Station', 'id', 'station_id');

    }

    //���������Գ��α�
    public function examScreening()
    {
        return $this->hasOne('Modules\Osce\Entities\ExamScreening', 'id', 'exam_screening_id');
    }

    public function examScore()
    {
        return $this->hasMany('\Modules\Osce\Entities\ExamScore', 'exam_result_id', 'id');
    }

    public function examSpecialScore()
    {
        return $this->hasMany('\Modules\Osce\Entities\ExamSpecialScore', 'exam_result_id', 'id');
    }

    /**
     * 保存成绩
     * @param $data
     * @param $score
     * @param $specialScore
     * @return static
     * @throws \Exception
     */
    private function groupResultScore($scoreJsonOb)
    {
        $score = [];
        $special = [];
        foreach ($scoreJsonOb as $option) {
            \Log::info('PAD提交过来的分数json对象', [$option]);
            if ($option['tag'] == 'normal') {
                $score[] = $option;
            } else {
                $special[] = $option;
            }
        }
        return [
            'score' => $score,
            'special' => $special,
        ];
    }

    public function addTestResult($data, $score, $exam_id)
    {
        $connection = DB::connection($this->connection);
        $connection->beginTransaction();
        $score = json_decode($score, true);
        if ($score == false) {
            throw new \Exception('提交的数据格式不合法');
        }
        $groupData = $this->groupResultScore($score);

        $examScreening = ExamScreening::find($data['exam_screening_id']);
        if (is_null($examScreening)) {
            throw new \Exception('找不到提交的场次');
        }

        $score = $groupData['score'];
        $specialScore = $groupData['special'];
        try {
            //判断成绩是否已提交过
            $ExamResult = $this->getRemoveScore($data);
            //获取考试成绩打分详情（解析json为数组）
            $scoreData = $this->getExamResult($score);

            //获取考试成绩特殊评分项扣分详情（解析json为数组）
            $specialScoreData = $this->getSpecialScore($specialScore);

            //todo:增加提交数据校验  20160512 01:02 Zouyuchao
            //获取当前考试当前场次当前考站下的考试项目
            $subject = $this->getSuject($examScreening->exam_id, $examScreening->id, $data['station_id']);

            //校验普通分数
            $this->checkScore($subject, $scoreData);
            //校验特殊分数
            \Log::debug('开始校验特殊评分项', [$specialScoreData]);
            $this->checkSpecialScore($subject, $specialScoreData);

            //拿到特殊评分项总成绩
            $specialTotal = array_pluck($specialScoreData, 'score');
            $specialTotal = array_sum($specialTotal);

            //拿到总成绩
            $total = array_pluck($scoreData, 'original_score');
            $total = array_sum($total);
            $data['original_score'] = $total - $specialTotal;       //总成绩=考核点总得分-特殊评分项总扣除分
            $data['subject_id'] = $subject->id;
            \Log::info('考试存入数据库成绩',[$data]);
            if ($testResult = $this->create($data)) {
                //保存成绩评分
                $ExamResultId = $testResult->id;        //获取ID
                \Log::debug('保存考试数据', [$scoreData, $testResult]);
                //保存考试，考核点分数详情
                $this->getSaveExamEvaluate($scoreData, $ExamResultId);

                //保存考试，特殊评分项 实际扣分详情 TODO: fandian
                \Log::info('特殊评分项保存前记录', [$specialScoreData, $ExamResultId]);
                $this->getSaveSpecialScore($specialScoreData, $ExamResultId,$data['station_id']);
                \Log::debug('特殊评分项', [$specialScoreData]);
                //保存语音 图片
                $this-> getSaveExamAttach($data['student_id'], $ExamResultId, $score);

            } else {
                throw new \Exception('成绩提交失败', -1000);
            }

            //标记父考试成绩 TODO：fandian 2016-06-17
            $result = $this->flagOldExamResult($exam_id, $data['student_id']);

            $connection->commit();
            return $testResult;

        } catch (\Exception $ex) {
            $connection->rollBack();
            throw $ex;
        }
    }

    private function getSuject($examId, $examScreeningId, $stationId)
    {
        //获取提交场次
        $ExamScreening = ExamScreening::find($examScreeningId);
        \Log::debug('根据条件获取场次', [$ExamScreening]);
        if (is_null($ExamScreening)) {
            throw new \Exception('场次不存在');
        }
        //获取提交阶段
        $gradation = ExamGradation::where('order', '=', $ExamScreening->gradation_order)->where('exam_id', '=',
            $examId)->first();
        \Log::debug('根据条件获取阶段', [$gradation]);
        if (is_null($gradation)) {
            \Log::alert('找不到当前阶段');
            throw new \Exception('找不到当前阶段');
        }
        //获取当前考试当前阶段考站安排
        $ExamDraftInfo = ExamDraft::leftJoin('exam_draft_flow', 'exam_draft_flow.id', '=',
            'exam_draft.exam_draft_flow_id')
            ->where('exam_draft_flow.exam_id', '=', $examId)
            ->where('exam_draft_flow.exam_gradation_id', '=', $gradation->id)
            ->where('exam_draft.station_id', '=', $stationId)
            ->select(['exam_draft.id', 'exam_draft_flow.name', 'exam_draft.station_id', 'exam_draft.subject_id'])
            ->with('subject')
            ->first();
        \Log::info('根据条件获取到的考站安排情况', [$ExamDraftInfo]);
        if (is_null($ExamDraftInfo)) {
            throw new \Exception('找不到考站安排');
        }
        \Log::info('根据考站安排获取到的考试项目', [$ExamDraftInfo->subject]);
        return $ExamDraftInfo->subject;
    }

    //根据考试项目检查普通评分数据
    private function checkScore($subject, $score)
    {
        if (is_null($subject)) {
            throw new \Exception('找不到当前考站下的考试项目');
        }
        $standard = $subject->standards->first();
        \Log::info('检查普通数据时查找到的当前评分表名', [$standard]);
        if (is_null($standard)) {
            throw new \Exception('找不到当前项目下的评分表');
        }
        $standardItems = $standard->standardItem;
        \Log::info('提交普通成绩校验-评分表详情清单', [$standardItems, $subject]);
        $scoreList = [];
        \Log::info('提交普通成绩校验-提交的数据', [$score]);
        foreach ($score as $priont) {
            \Log::info('提交普通成绩校验-提交的考核点或考核项数据', [$priont]);
            $scoreList[$priont['standard_item_id']] = $priont;
        }

        \Log::info('提交普通成绩校验-评分表详情提交数据', [$score, $scoreList]);
        foreach ($standardItems as $item) {
            if ($item->pid == 0) {
                continue;
            }
            \Log::info('标准评分点数据', [$item]);
            if (!array_key_exists($item->id, $scoreList)) {
                \Log::info('提交的考核点不对', []);
                if ($item->id == 0) {
                    throw new \Exception('没有找到考核点' . $item->sort . '的相关成绩,提交失败');
                } else {
                    throw new \Exception('没有找到考核点' . $item->parent->sort . '-' . $item->sort . '的相关成绩,提交失败');
                }
            } else {
                $thisScore = $scoreList[$item->id];
                //当前分数小于0检查
                if (intval($thisScore['original_score']) < 0) {
                    if ($item->id == 0) {
                        throw new \Exception('考核点' . $item->sort . '分数不合法,提交失败');
                    } else {
                        \Log::info('标准评分项数据(父级)', [$item->parent]);
                        throw new \Exception('考核点' . $item->parent->sort . '-' . $item->sort . '分数不合法,提交失败');
                    }
                }
                //当前分数大于上限检查
                if (intval($thisScore['original_score']) > intval($item->score)) {
                    if ($item->id == 0) {
                        throw new \Exception('考核点' . $item->sort . '分数不合法,提交失败');
                    } else {
                        \Log::info('上限，标准评分项数据(父级)', [$item->parent]);
                        throw new \Exception('考核点' . $item->parent->sort . '-' . $item->sort . '分数不合法,提交失败');
                    }
                }
            }
        }
        \Log::info('普通数据校验完成', []);
    }

    //根据考试项目检查普通特殊评分数据
    private function checkSpecialScore($subject, $specialScoreData)
    {
        if (is_null($subject)) {
            throw new \Exception('找不到当前考站下的考试项目');
        }
        $specialScores = [];
        //获取标准特殊评分项清单
        $specials = $subject->specials;
        //当有提交有特殊评分项时
        if (count($specialScoreData)) {
            foreach ($specialScoreData as $item) {
                \Log::info('特殊评分项详情', [$item]);
                $specialScores[$item['special_score_id']] = $item;
            }
            \Log::info('提交特殊评分项查询数据', [$specials, $subject]);
            \Log::info('提交特殊评分项校验提交数据', [$specialScoreData]);
            foreach ($specials as $special) {
                if (!array_key_exists($special->id, $specialScores)) {
                    throw new \Exception('传入了非法的特殊评分项');
                }

                $score = $specialScores[$special->id]['score'];
                if (intval($score) < 0) {
                    throw new \Exception('特殊评分项分数不合法,提交失败');
                }
                if (intval($score) > intval($special->score)) {
                    throw new \Exception('特殊评分项分数不合法,提交失败');
                }
            }
        } else {
            if (count($specials)) {
                throw new \Exception('提交失败,没有找到特殊评分项成绩');
            }
        }
    }

    //upload_document_id 音频 图片id集合去修改
    private function getSaveExamAttach($studentId, $ExamResultId, $score)
    {
        try {
            $list = [];
//            $arr = json_decode($score, true);
//            \Log::alert($arr);
            foreach ($score as $item) {
                $list[] = [
                    'standard_item_id' => $item['id']
                ];
            }
            $standardItemId = array_column($list, 'standard_item_id');

            if (is_null(TestAttach::whereIn('standard_item_id', $standardItemId)->get())) {
                throw new \Exception('该考试没有上传图片和音频');
            }

            $AttachData = TestAttach::where('student_id', '=', $studentId)->whereIn('standard_item_id',
                $standardItemId)->get();
            foreach ($AttachData as $item) {
                $item->test_result_id = $ExamResultId;
                if (!$item->save()) {
                    throw new \Exception('修改图片音频结果失败', -1400);
                }
            }

        } catch (\Exception $ex) {
            \Log::alert($ex->getMessage());
        }
    }

    private function getSaveExamEvaluate($scoreData, $ExamResultId)
    {
        foreach ($scoreData as &$item) {

            $item['exam_result_id'] = $ExamResultId;
             //把科目下所有成绩详情取出
            $standard = StandardItem::find($item['standard_item_id']);
            //拿到折算率
            $convert =StandardItem::find($standard->pid);
            //折算分数
            $item['score'] = $item['original_score']*$convert->coefficient;
            $examScore = ExamScore::create($item);
            \Log::info('存入考核项到数据库',[$examScore]);
            if (!$examScore) {
                throw new \Exception('保存分数详情失败', -1300);
            }
        }
        //添加折算总成绩
    //        $this->getScoreCorrected($ExamResultId,$stationId);
    }

    /**
     * 添加折算总成绩
     * @param $specialScoreDatas
     * @param $ExamResultId
     * @throws \Exception
     *
     * @author zhouqiang <zhouqiang@sulida.com>
     * @date   2016-06-15 16:44
     * @copyright  2013-2017 sulida.com  Inc. All Rights Reserved
     */

    
    private function getScoreCorrected($ExamResultId,$stationId){
        $ExamResult= ExamResult::find($ExamResultId);
        $ExamScore =  ExamScore::where('exam_result_id','=' ,$ExamResultId)->get()->pluck('score')->toArray();
        $station = Station::find($stationId);
        $CorrectedScore = 0;
        //判断考站是否是理论考站
        if($station ->type ==3){
            $CorrectedScore =$ExamResult->original_score;  //是理论考站分数不变
        }else{
            foreach ($ExamScore as $value){
                $CorrectedScore += $value;
            }
        }
        //拿到特殊评分项
        $special = ExamSpecialScore::where('exam_result_id','=',$ExamResultId)->get();
        if(!$special->isEmpty()){
            //拿到特殊评分项的折算成绩
            $specialScore = 0;
            foreach ($special as $item){
               //拿到特殊项的折算率
               $specialConvert = SubjectSpecialScore::find($item->special_score_id);
                //得到所有特殊项折算成绩
                $specialScore += $item->score*$specialConvert->rate;
            }
            $CorrectedScore = $CorrectedScore-$specialScore;
        }
        if($CorrectedScore < 0){
            $CorrectedScore = 0;
        }
        \Log::info('折算总成绩',[$CorrectedScore]);
        $ExamResult ->score = round($CorrectedScore,2);
        if(!$ExamResult ->save()){
            throw  new \Exception('折算总分出错');
        }
        \Log::info('成绩和折算成绩保存完成',[$ExamResult]);
        return true;
    }


    /**
     * 保存考试，考核项折算分数
     * @param $specialScoreDatas
     * @param $ExamResultId
     * @throws \Exception
     *
     * @author zhouqiang <zhouqiang@sulida.com>
     * @date   2016-06-15 16:44
     * @copyright  2013-2017 sulida.com  Inc. All Rights Reserved
     */


    private function  getGradeCorrected($subjectId){

        if(empty($subjectId)){
            //把科目下所有成绩详情取出
            $examScoreModel = new ExamScore();
            $examScoreData = $examScoreModel->getExamScoreData($subjectId);
            \Log::info('拿到原始成绩',[$examScoreData]);
            foreach ($examScoreData as $item){
                $standard = StandardItem::find($item->standard_item_id);
                //拿到折算率
                $convert =StandardItem::find($standard->pid);

                //折算分数
                $item ->score = $item->original_score*$convert->coefficient;
                if(!$item->save()){
                    throw  new \Exception('折算评分详情出错');
                }

            }

        }

    }








    /**
     * 保存考试，特殊评分项 实际扣分详情
     * @param $specialScoreDatas
     * @param $ExamResultId
     * @throws \Exception
     *
     * @author fandian <fandian@sulida.com>
     * @date   2016-05-07 16:44
     * @copyright  2013-2017 sulida.com  Inc. All Rights Reserved
     */
    private function getSaveSpecialScore($specialScoreDatas, $ExamResultId,$stationId)
    {
        if (count($specialScoreDatas) > 0) {
            foreach ($specialScoreDatas as &$item) {
                $item['exam_result_id'] = $ExamResultId;
                $examSpecialScore = ExamSpecialScore::create($item);
                if (!$examSpecialScore) {
                    throw new \Exception('保存特殊评分项 实际扣分详情失败', -1511);
                }
            }
        }
        //添加折算总成绩
        $this->getScoreCorrected($ExamResultId,$stationId);
    }

    //删除已提交过得成绩
    private function getRemoveScore($data)
    {
        //判断成绩是否已提交过
        try {
            $examResult = $this->where('student_id', '=', $data['student_id'])
                ->where('exam_screening_id', '=', $data['exam_screening_id'])
                ->where('station_id', '=', $data['station_id'])
                ->first();
            if ($examResult)
            {
                //拿到考试结果id去exam_score中删除数据
                if (!$examResult->examScore->isEmpty()) {
                    if (false === $examResult->examScore()->delete()) {
                        throw new \Exception('舍弃考试评分详情失败', -1100);
                    }
                }
                
                //删除特殊评分项的评分
                if (!$examResult->examSpecialScore->isEmpty())
                {
                    if (false === $examResult->examSpecialScore()->delete()) {
                        throw new \Exception('舍弃考试特殊评分项成绩失败', -11010);
                    }
                }

                if (false === $examResult->delete()) {
                    throw new \Exception('舍弃考试成绩失败', -1200);
                }
            }
            return true;
        } catch (\Exception $ex) {
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
            return null;
        } else {

            $studentExamScore = TestResult::where('student_id', '=', $studentId)
                ->whereIn('exam_screening_id', $studentExamScreeningIdArr)
                ->select('score')->get()->toArray();
            $StudentScores = 0;
            foreach ($studentExamScore as $val) {
                $StudentScores += $val['score'];
            }
            return $StudentScores;
        }

    }


    //获取考试成绩打分详情（解析json为数组）
    private function getExamResult($score)
    {
        $list = [];
        //$arr = json_decode($score, true);//todo:罗海华 2016-05-10 调试 提交成绩变更
        $arr = $score;
        \Log::debug('成绩打分项', $arr);
        foreach ($arr as $item) {
            foreach ($item['test_term'] as $str) {
                $list [] = [
                    'subject_id'        => $str['subject_id'],
                    'standard_item_id'  => $str['id'],
                    'original_score'    => $str['real'],
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
     * @author fandian <fandian@sulida.com>
     * @date   2016-05-07 16:44
     * @copyright  2013-2017 sulida.com  Inc. All Rights Reserved
     */
    private function getSpecialScore($specialScores)
    {
        $list = [];
        //$arr = json_decode($specialScores, true);//todo:罗海华 2016-05-10 调试 提交成绩变更
        $arr = $specialScores;
        \Log::debug('特殊评分项解析', [$arr]);
        if (!empty($arr)) {
            foreach ($arr as $item) {
                \Log::info('特殊评分项解析元素', [$item]);
                $list [] = [
                    'subject_id'        => $item['subject_id'],
                    'special_score_id'  => $item['id'],
                    'score'             => $item['subtract'],
                ];
            }
        }

        \Log::debug('特殊评分项结果', [$list]);
        return $list;
    }

    /**
     * 给长辈考试做标记（包括 父考试、前面所有的兄弟考试）
     * @param $exam_id
     * @param $student_id
     * @return bool
     * @throws \Exception
     *
     * @author fandian <fandian@sulida.com>
     * @date   2016-06-17 13:44
     * @copyright  2013-2017 sulida.com  Inc. All Rights Reserved
     */
    public function flagOldExamResult($exam_id, $student_id, $flag = 1)
    {
        try{
            $exam = Exam::find($exam_id);
            //pid不为0，表示有父考试
            if($exam->pid != 0)
            {
                //1、根据考试ID，查询其所有的考试场次，去掉当前的考试ID
                list($screening_ids, $elderExam_ids) = ExamScreening::getAllScreeningByExam($exam->pid, [$exam_id]);
                //获取对应学生信息
                $student = Student::find($student_id);
                if(is_null($student)){
                    throw new \Exception('没有对应学生信息');
                }
                //2、获取学生对应的用户ID
                $user_id = $student->user_id;
                //3、获取 长辈考试该用户对应的所有学生ID
                $student_ids= Student::where('user_id', '=', $user_id)
                            ->whereIn('exam_id', $elderExam_ids)
                            ->where('id', '<>', $student_id)                //去掉自己
                            ->select('id')->get()->pluck('id')->toArray();
                //4、标记对应的考试成绩
                $result = ExamResult::whereIn('student_id', $student_ids)
                          ->whereIn('exam_screening_id', $screening_ids)
                          ->where('flag', '=', 0);                      //只标记使用中的（其他 作废、弃考、作弊、替考的，无需标记）
                //存在需要标记的，再进行标记
                if($result->count() >0)
                {
                    $flagNum = $result->update(['flag'=>$flag]);    //$flagNum 为标记成功的个数（标记为1，成绩作废）
                    if(!$flagNum){
                        throw new \Exception('标记父考试失败！');
                    }
                    return $flagNum;        //返回标记成功的个数
                }
            }

            return true;

        }catch (\Exception $ex){
            throw $ex;
        }
    }

    public function flagExamSelf($exam_id, $flag = 1)
    {
        $exam = Exam::find($exam_id);
        if(is_null($exam)){
            throw new \Exception('没有找到对应考试信息！');
        }
        //获取考试对应的所有场次
        $examScreenings = $exam->examScreening;
    }

}