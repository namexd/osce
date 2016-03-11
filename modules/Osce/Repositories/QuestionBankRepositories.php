<?php
/**
 * Created by PhpStorm.
 * @author tangjun <tangjun@misrobot.com>
 * @date 2016-03-08 11:43
 * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
 */

namespace Modules\Osce\Repositories;
use Modules\Osce\Repositories\BaseRepository;
use Modules\Osce\Entities\QuestionBankEntities\ExamPaper;
use Modules\Osce\Entities\QuestionBankEntities\ExamQuestion;
use Modules\Osce\Entities\QuestionBankEntities\ExamQuestionLabel;
class QuestionBankRepositories  extends BaseRepository
{

    /**
     * @method
     * @url /osce/
     * @access public
     * @param ExamPaper $ExamPaper
     * @return array
     * @author tangjun <tangjun@misrobot.com>
     * @date    2016年3月9日12:11:59
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function ExamPaperPreview($data){
        $ExamQuestion  = new ExamQuestion;
        $data = [
            'ExamPaperName'=>'测试试卷',
            'ExamPaperLength' => 50,
            'mode'=>1,
            'type'=>2,
            'child'=>[
                [
                    'type'=>1,
                    'num'=>5,
                    'score'=>5,
                    'total_score'=>5*5,
                    'exam_question'=>[8,9,10,11]
                ],
                [
                    'type'=>1,
                    'num'=>5,
                    'score'=>5,
                    'total_score'=>5*5,
                    'exam_question'=>[8,9,10,11]
                ]
            ]
        ];
        if($data['mode'] == 1){

        }elseif($data['mode'] == 2){

        }
        return  $data;
    }

    /**
     * @method
     * @url /osce/
     * @access public
     * @param $str
     * @return array|bool
     * @author tangjun <tangjun@misrobot.com>
     * @date    2016年3月11日11:05:43
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function StrToArr($str){
        $NewArr = [];
        $ExamQuestionLabel = new ExamQuestionLabel;
        if(!empty($str)){
            $arr = explode('@',$str);
            if(!empty($arr[1])&&!empty($arr[2])&&!empty($arr[3])){
                $AdditionalInfo = explode(',',$arr[1]);
                $NewArr['type'] = $AdditionalInfo['0'];
                $NewArr['num'] = $AdditionalInfo['1'];
                $NewArr['score'] = $AdditionalInfo['2'];
                $NewArr['total_score'] = $AdditionalInfo['1']*$AdditionalInfo['2'];
                $lableType = explode(',',$arr[2]);
                $lableArr = explode(',',$arr[3]);
                $ExamQuestionLabelData = $ExamQuestionLabel->whereIn('id',$lableArr)->get();
                $data = [];
                foreach($ExamQuestionLabelData as $k => $v){
                    if(!empty($v->ExamQuestionLabelType['id'])){
                        foreach($lableType as $key => $val){
                            $arr = explode('-',$val);
                            if($arr[0] == $v->ExamQuestionLabelType['id']){
                                $data[$k]['label_type_id'] = $v->ExamQuestionLabelType['id'];
                                $data[$k]['exam_question_label_id'] = $v['id'];
                                $data[$k]['relation'] = $arr[1];
                            }
                        }
                    }
                }
                $NewArr['structure_label'] = $data;
            }else{
                return  false;
            }
        }
        return  $NewArr;
    }
}