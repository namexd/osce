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

/**���Դ��������
 * Class Answer
 * @package Modules\Osce\Http\Controllers\Admin\Branch
 */

class AnswerController extends CommonController
{
    /**��ʽ�Ծ���Ϣ����
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
                    'name' => $v->name,//�Ծ�����
                    'length' => $v->length,//����ʱ��
                    'totalScore' => $v->totalScore,//�Ծ��ܷ�
                    'examQuestionTypeId' => $v->examQuestionTypeId,//��������id
                    'typeName' => $v->typeName,//������������
                    'score' => $v->score,//���������ֵ
                    'questionName' => $v->questionName,//��������
                    'content' => $v->content,//��������
                    'answer' => $v->answer,//�����
                );
            }
        }
        //��ȡ��ʽ�Ծ����Ϣ
        $examPaperFormalModel = new ExamPaperFormal();
        $examPaperFormalList = $examPaperFormalModel->first();
        //�����������Ͷ�����������з���
        $examCategoryFormalList='';
        $examCategoryFormalData='';//������Ϣ(�����������ͽ��з���)
        if($examPaperFormalList){
            $examCategoryFormalList = $examPaperFormalList->examCategoryFormal;//��ȡ��ʽ���������Ϣ
            if($examCategoryFormalList){
                foreach($examCategoryFormalList as $key=>$val){
                    if($val->ExamQuestionFormal){
                        $examCategoryFormalList[$key]['exam_question_formal'] = $val->ExamQuestionFormal;//��ȡ��ʽ������Ϣ
                       // $examCategoryFormalList[$key]['count'] = count($val->ExamQuestionFormal);//��ȡ��ʽ������Ϣ
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
                       // 'count'=>$v1->count //����������µ��������

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
                            $serialNumber[]=($k1+1).'.'.($k2+1);//���к�
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