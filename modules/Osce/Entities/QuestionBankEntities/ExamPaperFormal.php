<?php
/**
 * Created by PhpStorm.
 * @author tangjun <tangjun@misrobot.com>
 * @date 2016年3月9日11:02:12
 * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
 */
namespace Modules\Osce\Entities\QuestionBankEntities;
use Illuminate\Database\Eloquent\Model;
use Modules\Osce\Entities\CommonModel;
class ExamPaperFormal extends CommonModel
{
    protected $connection = 'osce_mis';
    protected $table = 'exam_paper_formal';
    public $timestamps = true;
    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $guarded = [];
    protected $hidden = [];
    protected $fillable = ['id', 'exam_paper_id', 'length','name','total_score'];

    /**
     * 与正式试题分类表的关系
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     * @author tangjun <tangjun@misrobot.com>
     * @date    2016年3月9日10:38:36
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function ExamCategoryFormal(){
        return $this->hasMany('Modules\Osce\Entities\QuestionBankEntities\ExamCategoryFormal','exam_paper_formal_id','id');
    }

    /**
     * 创建真实的试卷
     * @method
     * @url /osce/
     * @access public
     * @param $ExamPaperInfo
     * @author tangjun <tangjun@misrobot.com>
     * @date    2016年3月15日09:28:01
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function CreateExamPaper($ExamPaperInfo){
        //throw new \Exception(' 插入试题和标签中间表失败！')
        $DB = \DB::connection('osce_mis');
        $DB->beginTransaction();
        try{
            $total_score = 0;
            //统计试卷总分
            if(count($ExamPaperInfo['item'])>0){
                foreach($ExamPaperInfo['item'] as $k => $v){
                    $total_score += $v['total_score'];
                }
            }
            $ExamPaperData = [
                'exam_paper_id'=>$ExamPaperInfo['id'],
                'length'=>$ExamPaperInfo['length'],
                'name'=>$ExamPaperInfo['name'],
                'total_score'=>$total_score
            ];
            //创建真实试卷
            $NewExamPaperInfo = $this->create($ExamPaperData);
            if(empty($NewExamPaperInfo->id)){
                throw new \Exception(' 创建试卷失败！');
            }
            //统计试卷总分
            if(count($ExamPaperInfo['item'])>0){
                foreach($ExamPaperInfo['item'] as $k => $v){
                    $ExamQuestionType = new ExamQuestionType;
                    //根据题目类型获取相应的题目类型id
                    $ExamQuestionTypeInfo = $ExamQuestionType->where('id','=',$v['type'])->select('name')->first();
                    $ExamCategoryFormalData = [
                        'name'=>!empty($ExamQuestionTypeInfo['name'])?$ExamQuestionTypeInfo['name']:'题目类型名称已经被删除',
                        'exam_question_type_id'=>$v['type'],
                        'number'=>$v['num'],
                        'score'=>$v['score'],
                        'exam_paper_formal_id'=>$NewExamPaperInfo->id
                    ];
                    //创建正式试题分类表数据
                    if($ExamCategoryFormalInfo = ExamCategoryFormal::create($ExamCategoryFormalData)){
                        if(count($v['child'])>0){
                            foreach($v['child'] as $val){
                                $ExamQuestionInfo = ExamQuestion::where('id','=',$val)->first();
                                //拼凑试题内容
                                $content = '';
                                if(!empty($ExamQuestionInfo->examQuestionItem)&&count($ExamQuestionInfo->examQuestionItem)>0){
                                    foreach($ExamQuestionInfo->examQuestionItem as $value){
                                        if($content){
                                            $content .= '|%|'.$value['name'].'.'.$value['content'];
                                        }else{
                                            $content .= $value['name'].'.'.$value['content'];
                                        }

                                    }
                                }
                                $ExamQuestionData = [
                                    'name'=>$ExamQuestionInfo['name'],
                                    'exam_question_id'=>$ExamQuestionInfo['id'],
                                    'content'=>$content,
                                    'answer'=>$ExamQuestionInfo['answer'],
                                    'parsing'=>$ExamQuestionInfo['parsing'],
                                    'exam_category_formal_id'=>$ExamCategoryFormalInfo['id'],
                                ];
                                if(!ExamQuestionFormal::create($ExamQuestionData)){
                                    throw new \Exception(' 创建试题表数据失败！');
                                }
                            }
                        }
                    }else{
                        throw new \Exception(' 创建试题分类表数据失败！');
                    }
                }
            }
            $DB->commit();
            return  $NewExamPaperInfo->id;

        }catch (\Exception $ex){
            $DB->rollBack();
            throw $ex;
        }

    }

}