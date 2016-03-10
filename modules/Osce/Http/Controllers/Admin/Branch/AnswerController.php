<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/3/10 0010
 * Time: 14:05
 */

namespace Modules\Osce\Http\Controllers\Admin\Branch;


use Modules\Osce\Entities\QuestionBankEntities\Answer;
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
        if($list){
            foreach($list as $k=>$v){
                $data[] = array(
                    'name'=>$v->name,//�Ծ�����
                    'length'=>$v->length,//����ʱ��
                    'totalScore'=>$v->totalScore,//�Ծ��ܷ�
                    'examQuestionTypeId'=>$v->examQuestionTypeId,//��������id
                    'typeName'=>$v->typeName,//������������
                    'score'=>$v->score,//���������ֵ
                    'questionName'=>$v->questionName,//��������
                    'content'=>$v->content,//��������
                    'answer'=>$v->answer,//�����
                );
            }
        }
        dd($data);
    }




























}