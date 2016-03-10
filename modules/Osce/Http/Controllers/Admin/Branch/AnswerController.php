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
        if($list){
            foreach($list as $k=>$v){
                $data[] = array(
                    'name'=>$v->name,//试卷名称
                    'length'=>$v->length,//考试时间
                    'totalScore'=>$v->totalScore,//试卷总分
                    'examQuestionTypeId'=>$v->examQuestionTypeId,//试题类型id
                    'typeName'=>$v->typeName,//试题类型名称
                    'score'=>$v->score,//单个试题分值
                    'questionName'=>$v->questionName,//试题名称
                    'content'=>$v->content,//试题内容
                    'answer'=>$v->answer,//试题答案
                );
            }
        }
        dd($data);
    }




























}