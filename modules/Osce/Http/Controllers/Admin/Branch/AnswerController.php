<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/3/10 0010
 * Time: 14:05
 */

namespace Modules\Osce\Http\Controllers\Admin\Branch;


use Modules\Osce\Entities\QuestionBankEntities\Answer;
use Modules\Osce\Entities\QuestionBankEntities\ExamCategoryFormal;
use Modules\Osce\Entities\QuestionBankEntities\ExamPaperFormal;
use Modules\Osce\Http\Controllers\CommonController;
use Illuminate\Http\Request;

/**考试答题控制器
 * Class Answer
 * @package Modules\Osce\Http\Controllers\Admin\Branch
 */

class AnswerController extends CommonController
{
    /**正式试卷信息数据
     * @method
     * @url /osce/
     * @access public
     * @author xumin <xumin@misrobot.com>
     * @date
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function formalPaperList()
    {

        $answer = new Answer();
        $list = $answer->getFormalPaper();
        $data = [];
        $newData = [];
        if($list) {
            foreach ($list as $k => $v) {
                $data[] = array(
                    'name' => $v->name,//试卷名称
                    'length' => $v->length,//考试时间
                    'totalScore' => $v->totalScore,//试卷总分
                    'examQuestionTypeId' => $v->examQuestionTypeId,//试题类型id
                    'typeName' => $v->typeName,//试题类型名称
                    'score' => $v->score,//单个试题分值
                    'questionName' => $v->questionName,//试题名称
                    'content' => $v->content,//试题内容
                    'answer' => $v->answer,//试题答案
                );
            }
        }
        //获取正式试卷表信息
        $examPaperFormalModel = new ExamPaperFormal();
        $examPaperFormalList = $examPaperFormalModel->first();
        //根据试题类型对试题表来进行分类
        $examCategoryFormalList='';
        $examCategoryFormalData='';//试题信息(根据试题类型进行分类)
        if($examPaperFormalList){
            $examCategoryFormalList = $examPaperFormalList->examCategoryFormal;//获取正式试题分类信息
            if($examCategoryFormalList){
                foreach($examCategoryFormalList as $key=>$val){
                    if($val->ExamQuestionFormal){
                        $examCategoryFormalList[$key]['exam_question_formal'] = $val->ExamQuestionFormal;//获取正式试题信息
                        // $examCategoryFormalList[$key]['count'] = count($val->ExamQuestionFormal);//获取正式试题信息
                    }
                }
                foreach($examCategoryFormalList as $k1=>$v1){
                    $examCategoryFormalData[$k1]= array(
                        'id'=>$v1->id,
                        'name'=>$v1->name,
                        'exam_question_type_id'=>$v1->exam_question_type_id,
                        'number'=>$v1->number,
                        'score'=>$v1->score,
                        'exam_paper_formal_id'=>$v1->exam_paper_formal_id,
                        // 'count'=>$v1->count //该试题分类下的试题个数

                    );
                    if(count($v1['exam_question_formal'])>0){
                        foreach($v1['exam_question_formal'] as $k2=>$v2){
                            $examCategoryFormalData[$k1]['exam_question_formal'][$k2]=array(
                                'id' =>$v2->id,
                                'name' =>$v2->name,
                                'exam_question_id' =>$v2->exam_question_id,
                                'content' =>$v2->content,
                                'answer' =>$v2->answer,
                                'parsing' =>$v2->parsing,
                                'exam_category_formal_id' =>$v2->exam_category_formal_id,
                                'student_answer' =>$v2->student_answer
                            ,                            );
                            $serialNumber[]=($k1+1).'.'.($k2+1);//序列号
                        }
                    }else{
                        $examCategoryFormalData[$k1]['exam_question_formal']='';
                    }
                }
                $examCategoryFormalData['serialNumber'] = $serialNumber;
            }
        }
        dd($examCategoryFormalData);
    }
}