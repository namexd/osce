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
use Modules\Osce\Entities\QuestionBankEntities\ExamQuestionLabelRelation;
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

    /**
     * @method
     * @url /osce/
     * @access public
     * @param $structureArr
     * @author tangjun <tangjun@misrobot.com>
     * @date    2016年3月11日18:33:54
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function StructureExamQuestionArr($structureArr)
    {
        $structureArr = $this->HandlePaperPreviewArr($structureArr);
        $ExamQuestionLabelRelation = new ExamQuestionLabelRelation;
        $arr = [];
        if (!empty($structureArr)) {
            foreach ($structureArr as $key => $val) {
                //\DB::connection('osce_mis')->enableQueryLog();
                $builder = $ExamQuestionLabelRelation->leftJoin('exam_question', function ($join) {
                    $join->on('exam_question.id', '=', 'exam_question_label_relation.exam_question_id');
                })
                    ->groupBy('exam_question.id')
                    ->where('exam_question.exam_question_type_id', '=', $val['question_type'])
                    ->select(
                        'exam_question.id as id'
                    );

                if (!empty($val['child'])) {
                    foreach ($val['child'] as $k => $v) {
                        $labelIdArr = collect($v)->pluck('exam_question_label_id');

                        //（1.包含，2.等于）
                        if ($v['0']['relation'] == 1) {
                            $builder->where(function ($query) use ($labelIdArr) {
                                foreach ($labelIdArr as $item) {
                                    $query->orWhere('exam_question_label_id', '=', $item);
                                }
                            });

                        } elseif ($v['0']['relation'] == 2) {
                            $builder->where(function ($query) use ($labelIdArr) {
                                foreach ($labelIdArr as $item) {
                                    $query->where('exam_question_label_id', '=', $item);
                                }
                            });
                        }
                    }
                    $questionList = $builder->get();
                    $questionIdArr = [];
                    if(count($questionList)>0){
                        $questionIdArr = $this->RandQuestionId($questionList->pluck('id'),$val['question_num']);
                    }
                    $arr[$val['question_type']]['type'] = $val['question_type'];
                    $arr[$val['question_type']]['num'] = $val['question_num'];
                    $arr[$val['question_type']]['score'] = $val['question_score'];
                    $arr[$val['question_type']]['total_score'] = $val['question_total_score'];
                    $arr[$val['question_type']]['child'] = $questionIdArr;
                }
            }
        }
        return  $arr;
    }

    /**
     * 处理构造试题的数组
     * @method
     * @url /osce/
     * @access public
     * @param $PaperPreviewArrItem
     * @return array
     * @author tangjun <tangjun@misrobot.com>
     * @date    2016年3月11日18:32:08
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function HandlePaperPreviewArr($PaperPreviewArrItem){
        $data = [];
        if(!empty($PaperPreviewArrItem)){
            foreach($PaperPreviewArrItem as $k => $v){
                if(!empty($v['structure_label'])){
                    foreach($v['structure_label'] as $key => $val){
                        $data[$k]['child'][$val['label_type_id']][] = $val;
                    }
                }
                $data[$k]['question_type'] = $v['type'];
                $data[$k]['question_num'] = $v['num'];
                $data[$k]['question_score'] = $v['score'];
                $data[$k]['question_total_score'] = $v['total_score'];
            }
        }
        return $data;
    }

    /**
     * 随机取出制定数量的试题id
     * @method
     * @url /osce/
     * @access public
     * @param $questionList
     * @param $questionNum
     * @return array
     * @author tangjun <tangjun@misrobot.com>
     * @date   2016年3月11日18:29:14
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function RandQuestionId($questionList,$questionNum){
        $length = count($questionList);
        $key = [];
        $questionIdArr = [];
        if($length<$questionNum){
            return  $questionList;
        }else{
            while (count($key)<$questionNum)
            {
                $k = rand(0,$length-1);
                if(in_array($k,$key)){
                    continue;
                }else{
                    $key[] = $k;
                }
            }
            foreach($key as $k => $v){
                $questionIdArr[] = $questionList[$v];
            }
            return  collect($questionIdArr);
        }
    }
}