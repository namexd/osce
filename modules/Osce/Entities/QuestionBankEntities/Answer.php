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
use Mockery\CountValidator\Exception;
use Modules\Osce\Entities\ExamQueue;
use Modules\Osce\Entities\ExamResult;
use Modules\Osce\Entities\ExamScreening;
use Modules\Osce\Entities\ExamScreeningStudent;
use Modules\Osce\Entities\ExamStationStatus;

/**考生答题时，正式试卷模型
 * Class Answer
 * @package Modules\Osce\Entities\QuestionBankEntities
 */
class Answer extends Model
{
    protected $connection = 'osce_mis';
    protected $table = 'exam_paper_formal';//正式的试卷表
    public $timestamps = true;
    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $guarded = [];
    protected $hidden = [];
    protected $fillable = ['id', 'status', 'exam_paper_id','length','name','total_score','created_user_id','created_at','updated_at'];

    /**与正式试题分类表的关系
     * @method
     * @url /osce/
     * @access public
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     * @author xumin <xumin@misrobot.com>
     * @date
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function examCategoryFormal(){
        return $this->hasMany('Modules\Osce\Entities\QuestionBankEntities\ExamCategoryFormal','exam_paper_formal_id','id');
    }
    /**正式试卷信息列表
     * @method
     * @url /osce/
     * @access public
     * @param $id 正式的试卷表ID
     * @return mixed
     * @author xumin <xumin@misrobot.com>
     * @date
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function getFormalPaper($id)
    {
        $DB = \DB::connection('osce_mis');
        $builder = $this;
        $builder = $builder->leftJoin('exam_category_formal', function ($join) { //正式试题分类表
            $join->on('exam_paper_formal.id', '=', 'exam_category_formal.exam_paper_formal_id');

        })->leftJoin('exam_question_type',function($join){ //题目类型表
            $join->on('exam_category_formal.exam_question_type_id', '=', 'exam_question_type.id');

        })->leftJoin('exam_question_formal',function($join){ //正式的试题表
            $join->on('exam_category_formal.id', '=', 'exam_question_formal.exam_category_formal_id');

        })->where('exam_paper_formal.id','=',$id)->select([
            'exam_paper_formal.name',//试卷名称
            'exam_paper_formal.length',//考试时间
            'exam_paper_formal.total_score as totalScore',//试卷总分
            'exam_category_formal.id as examCategoryFormalId',//正式试题类型id
            'exam_category_formal.name as typeName',//试题类型名称
            'exam_category_formal.score',//单个试题分值
            'exam_category_formal.exam_question_type_id as examQuestionTypeId',//试题类型id
            'exam_question_formal.name as questionName',//试题名称
            'exam_question_formal.content',//试题内容
            'exam_question_formal.answer',//试题答案
            'exam_question_formal.student_answer as studentAnswer',//考生答案
        ]);
        return $builder->get();
    }
    /**保存考生答案
     * @method
     * @url /osce/
     * @access public
     * @param $data
     * @return bool
     * @author xumin <xumin@misrobot.com>
     * @date
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function saveAnswer($data,$resultData)
    {
        $DB = \DB::connection('osce_mis');
        $DB->beginTransaction();
        try{
            //保存考试用时
            $examPaperFormalModel = new ExamPaperFormal();
            $examPaperFormalData = array(
                'actual_length'=>$data['actualLength'],
                'updated_at'=>date('Y-m-d H:i:s',time())
            );

            if(!$examPaperFormalModel->where('id','=',$data['examPaperFormalId'])->update($examPaperFormalData)){
                throw new \Exception(' 保存考试用时失败！');
            }
            //保存考生答案
            if(count($data['examQuestionFormalInfo'])>0 && !empty($data['examQuestionFormalInfo'])){
                $examQuestionFormalModel = new ExamQuestionFormal();
                foreach($data['examQuestionFormalInfo'] as $v){
                    $examQuestionFormalData = array(
                        'student_answer'=>$v['answer']
                    );

                    $questionData = $examQuestionFormalModel->where('id','=',$v['exam_question_id'])->first();
                    if(!empty($questionData)){
                        $result = $examQuestionFormalModel->where('id','=',$v['exam_question_id'])->update($examQuestionFormalData);
                        if(!$result){
                            throw new \Exception(' 保存考生答案失败！',-101);
                        }
                    }

                }
            }


            $examQueueInfo = ExamQueue::where('exam_id','=',$resultData['examId'])
                ->where('student_id','=',$resultData['studentId'])
                ->where('station_id','=',$resultData['stationId'])->first();
            if(!empty($examQueueInfo)){
                //将向考试结果记录表增加一条数据
                $score = $this->selectGrade($resultData['examPaperFormalId'])['totalScore'];//获取该考生成绩
                $examResultData=array(
                    'student_id'=>$resultData['studentId'],
                    'exam_screening_id'=>$examQueueInfo['exam_screening_id'],
                    'station_id'=>$resultData['stationId'],
                    'time'=>$resultData['time'],
                    'score'=>$score,
                    'teacher_id'=>$resultData['teacherId'],
                    'begin_dt'=>$resultData['begin_dt'],//考试开始时间
                    'end_dt'=>$resultData['end_dt'],//考试结束时间
                );
                //查询是否已有该考生的成绩
                $examResultInfo = ExamResult::where('student_id','=',$resultData['studentId'])
                    ->where('exam_screening_id','=',$examQueueInfo['exam_screening_id'])
                    ->where('station_id','=',$resultData['stationId'])->first();
                if(empty($examResultInfo)){
                    //如果没有成绩则新增
                    if(!ExamResult::create($examResultData)){
                        throw new \Exception(' 向考试结果记录表中插入数据失败！',-102);
                    }
                }else{
                    //有成绩则更新
                    if(!ExamResult::where('id','=',$examResultInfo['id'])->update($examResultData)){
                        throw new \Exception(' 保存考生成绩失败！',-103);
                    }
                }
            }/*else{
                throw new \Exception(' 没有对应的考生数据！',-104);
            }*/
            $DB->commit();
            return true;
        }catch (\Exception $ex){
            $DB->rollback();
            throw $ex;
        }
    }

    /**更新状态
     * @method
     * @url /osce/
     * @access public
     * @param $examId 考试id
     * @param $studentId 学生id
     * @param $stationId 考站id
     * @author xumin <xumin@misrobot.com>
     * @date
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function saveStatus($examId,$studentId,$stationId){
        $DB = \DB::connection('osce_mis');
        $DB->beginTransaction();
         try{
            //获取该考生对应的队列信息
            $quene = ExamQueue::where('exam_id',$examId)->where('student_id',$studentId)->where('station_id',$stationId)->first();
            if(!empty($quene)){
                //获取当前的服务器时间
                $date = date('Y-m-d H:i:s');
                //修改状态
                $data = array(
                    'status' =>3,
                    'end_dt' =>$date,
                    'blocking' =>1
                );
                 if(!ExamQueue::where('id',$quene->id)->update($data)){
                     throw new \Exception('状态更新失败',-101);
                 }

            /*    $examScreeningModel = new ExamScreening();
                //获取正在考试的场次信息
                $examScreening = $examScreeningModel->getExamingScreening($examId);
                if (is_null($examScreening)) {
                    //获取最近一场考试
                    $examScreening = $examScreeningModel->getNearestScreening($examId);
                }
                //更改考试-场次-考站状态表
                $examStationStatus = ExamStationStatus::where('station_id',$stationId)->where('exam_id',$examId)->where('exam_screening_id',$examScreening->id)->first();
                if(!empty($examStationStatus)){
                    $examStationStatus->status = 4;
                    if (!$examStationStatus->save()) {
                        throw new \Exception('考站准备状态失败！', -102);
                    }
                }*/
             }else{
                 throw new \Exception('没有该考生的队列信息',-103);
             }
            $DB->commit();
            return $quene;
        }catch (\Exception $ex){
            $DB->rollback();
            throw $ex;
        }

    }




    /**查询该考生理论考试成绩及该场考试相关信息
     * @method
     * @url /osce/
     * @access public
     * @param $examPaperFormalId 正式的试卷表ID
     * @return array
     * @author xumin <xumin@misrobot.com>
     * @date
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function selectGrade($examPaperFormalId)
    {
        $answerModel = new Answer();
        //查询该正式的试卷表信息
        $examPaperFormalList=$answerModel->where('id','=',$examPaperFormalId)->first();
        //调用查询该考卷的所有试题信息方法
        $getFormalPaperList = $answerModel->getFormalPaper($examPaperFormalId);
        $totalScore=0;//考生总分
        if(count($getFormalPaperList)>0){
            foreach($getFormalPaperList as $k=>$v){
                if($v['examQuestionTypeId']==1){
                    //单选题
                    if($v['studentAnswer']==$v['answer']){
                        $totalScore+=$v['score'];
                    }
                }elseif($v['examQuestionTypeId']==2){
                    //多选题
                    //判断考生答案是否包含@符号，有证明考生选择的是多个选项，无证明考生只选择了一个选项
                    if(strstr($v['studentAnswer'],'@')){
                        if($v['studentAnswer']==$v['answer']){
                            $totalScore+=$v['score'];
                        }elseif(strstr($v['answer'],$v['studentAnswer'])){
                            $totalScore+=$v['score']/2;
                        }
                    }else{
                        if(strstr($v['answer'],$v['studentAnswer'])){
                            $totalScore+=$v['score']/2;
                        }
                    }
                }elseif($v['examQuestionTypeId']==3){
                    //不定性选择题
                    //判断考生答案是否包含@符号，有证明考生选择的是多个选项，无证明考生只选择了一个选项
                    if(strstr($v['studentAnswer'],'@')){
                        if($v['studentAnswer']==$v['answer']){
                            $totalScore+=$v['score'];
                        }elseif(strstr($v['answer'],$v['studentAnswer'])){
                            $totalScore+=$v['score']/2;
                        }
                    }else{
                        if(strstr($v['answer'],$v['studentAnswer'])){
                            $totalScore+=$v['score']/2;
                        }
                    }

                }elseif($v['examQuestionTypeId']==4){
                    //判断题
                    if($v['studentAnswer']==$v['answer']){
                        $totalScore+=$v['score'];
                    }
                }
            }
        }
        $examPaperFormalList['totalScore']=$totalScore;
        if($examPaperFormalList){
            $examPaperFormalData=array(
                'id'=>$examPaperFormalList->id,//编号
                'exam_paper_id'=>$examPaperFormalList->exam_paper_id,//试卷id
                'length'=>$examPaperFormalList->length,//考试时长
                'name'=>$examPaperFormalList->name,//试卷名称
                'total_score'=>$examPaperFormalList->total_score,//总分
                'actual_length'=>$examPaperFormalList->actual_length,//考试用时
                'totalScore'=>$examPaperFormalList->totalScore,//该考生成绩
            );
        }
        return $examPaperFormalData;

    }




























}