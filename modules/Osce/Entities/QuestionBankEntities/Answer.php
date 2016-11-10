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
    public function saveAnswer($data)
    {
        $DB = \DB::connection('osce_mis');
        $DB->beginTransaction();
        try{
            if($data){
                //保存考试用时
                $examPaperFormalModel = new ExamPaperFormal();
                $examPaperFormalData = array(
                    'actual_length'=>$data['actualLength']
                );
                $result = $examPaperFormalModel->where('id','=',$data['examPaperFormalId'])->update($examPaperFormalData);
                if(!$result){
                    throw new \Exception(' 保存考试用时失败！');
                }
                //保存考生答案
                if(count($data['examQuestionFormalInfo'])>0 && !empty($data['examQuestionFormalInfo'])){
                    $examQuestionFormalModel = new ExamQuestionFormal();
                    foreach($data['examQuestionFormalInfo'] as $v){
                        $examQuestionFormalData = array(
                            'student_answer'=>$v['answer']
                        );
                        $result = $examQuestionFormalModel->where('id','=',$v['exam_question_id'])->update($examQuestionFormalData);
                        if(!$result){
                            throw new \Exception(' 保存考生答案失败！');
                        }
                    }
                }
            }
            $DB->commit();
            return true;
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