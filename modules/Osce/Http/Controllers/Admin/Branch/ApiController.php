<?php
/**
 * Created by PhpStorm.
 * @author tangjun <tangjun@misrobot.com>
 * @date 2016-03-10 14:11
 * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
 */

namespace Modules\Osce\Http\Controllers\Admin\Branch;
use Modules\Osce\Http\Controllers\CommonController;
use Modules\Osce\Entities\QuestionBankEntities\ExamQuestionLabelType;
use Modules\Osce\Entities\QuestionBankEntities\ExamQuestionType;
use Modules\Osce\Entities\QuestionBankEntities\ExamQuestionLabel;
use Modules\Osce\Repositories\QuestionBankRepositories;
use Modules\Osce\Entities\QuestionBankEntities\ExamQuestion;
use Illuminate\Http\Request;
class ApiController extends CommonController
{
    /**
     * @method
     * @url /osce/
     * @access public
     * @return \Illuminate\View\View
     * @author tangjun <tangjun@misrobot.com>
     * @date    2016年3月10日14:19:34
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function GetEditorExamPaperItem(){
        //获取题目类型列表
        $examQuestionTypeModel= new ExamQuestionType();
        $examQuestionTypeList = $examQuestionTypeModel->examQuestionTypeList();
        //获取考核范围列表（标签类型列表）
        $examQuestionLabelTypeModel = new ExamQuestionLabelType();
        $examQuestionLabelTypeList = $examQuestionLabelTypeModel->examQuestionLabelTypeList();
        foreach($examQuestionLabelTypeList as $k=>$v){
            $examQuestionLabelTypeList[$k]['examQuestionLabelList'] = $v->examQuestionLabel;
        }
        //dd($examQuestionLabelTypeList);
        return  view('osce::admin.resourcemanage.subject_papers_add_detail',[
            'examQuestionLabelTypeList'=>$examQuestionLabelTypeList,
            'examQuestionTypeList'=>$examQuestionTypeList
        ]);
    }

    /**
     * @method
     * @url /osce/
     * @access public
     * @param Request $request
     * @author tangjun <tangjun@misrobot.com>
     * @date    2016年3月11日11:20:48
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function PostEditorExamPaperItem(Request $request){
        $ExamQuestionLabel = new ExamQuestionLabel;
        //dd($request->all());
        $ExamQuestionLabelData = $ExamQuestionLabel->whereIn('id',$request->tag)->get();
        $idArr = [];
        $LabelNameStr = '';
        foreach($ExamQuestionLabelData as $k => $v){
            if($v->ExamQuestionLabelType){
                if(!in_array($v->ExamQuestionLabelType['id'],$idArr)){
                    $idArr[] = $v->ExamQuestionLabelType['id'];
                    if(!empty($LabelNameStr)){
                        $LabelNameStr .= ','.$v['name'];
                    }else{
                        $LabelNameStr .= $v['name'];
                    }
                }
            }
        }

        $LabelTypeStr = '';
        foreach($request->all() as $key => $val){
            if(preg_match('/^label-{1,3}/',$key)){
                $arr = explode('-',$key);
                if(!empty($LabelTypeStr)){
                    $LabelTypeStr .= ','.$arr[1].'-'.$val;
                }else{
                    $LabelTypeStr .= $arr[1].'-'.$val;
                }
            }
        }
        $data = [
            '0'=>$LabelNameStr,
            '1'=>implode(',',
                [
                    0=>empty($request->get('question-type'))?0:$request->get('question-type'),
                    1=>empty($request->get('question-number'))?0:$request->get('question-number'),
                    2=>empty($request->get('question-score'))?0:$request->get('question-score')
                ]
                ),
            '2'=>$LabelTypeStr,
            '3'=>$ExamQuestionLabelStr = implode(',',$request->tag)
        ];
        die(implode('@',$data));
    }

    /**
     * @method  GET
     * @url /osce/admin/api/exam-paper-preview
     * @access public
     * @param $data
     * @author tangjun <tangjun@misrobot.com>
     * @date    2016年3月11日11:21:47
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function ExamPaperPreview(Request $request,QuestionBankRepositories $questionBankRepositories){
        /*
         *   `mode` 组卷方式(1.自动组卷，2.手工组卷),
             `type` 试卷类型(1.随机试卷，2.统一试卷),
        */
        $this->validate($request,[
            'name'        => 'required',
            'time'        => 'required',
            'status'        => 'required|integer',
            'status2'        => 'required|integer',
            'question'        => 'required|array',
        ]);
        //（1.包含，2.等于）

        //组卷方式(1.自动组卷，2.手工组卷)
        $mode = $request->status;
        //试卷类型(1.随机试卷，2.统一试卷)
        $type = $request->status2;
        $PaperPreviewArr = [];
        $PaperPreviewArr['name'] = $request->name;
        $PaperPreviewArr['time'] = $request->time;

        if(!empty($request->question)){
            foreach($request->question as $k => $v){
                $PaperPreviewArr['item'][$k] = $questionBankRepositories->StrToArr($v);
            }
        }

        if($mode == 1 && !empty($PaperPreviewArr['item'])){
            $ExamQuestion = new ExamQuestion;
            $ExamQuestionType = new ExamQuestionType;
            $PaperPreviewArr['item'] = $questionBankRepositories->StructureExamQuestionArr($PaperPreviewArr['item']);
            foreach($PaperPreviewArr['item'] as $k => $v){
                if(!empty($v['child'])){
                    $ExamQuestionList = $ExamQuestion->whereIn('id',$v['child'])->get();
                    $ExamQuestionTypeInfo = $ExamQuestionType->where('id','=',$v['type'])->select('name')->first();
                    $PaperPreviewArr['item'][$k]['name'] = $ExamQuestionTypeInfo['name'].'（共'.$v['num'].'题，每题'.$v['score'].'分）';
                    $PaperPreviewArr['item'][$k]['child'] = $ExamQuestionList;
                }
            }
        }
        return  view('osce::admin.resourcemanage.subject_papers_add_preview',['PaperPreviewArr'=>$PaperPreviewArr]);
    }
}