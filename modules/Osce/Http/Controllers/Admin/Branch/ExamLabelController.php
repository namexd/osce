<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/3/7 0007
 * Time: 11:06
 */

namespace Modules\Osce\Http\Controllers\Admin\Branch;
use Modules\Osce\Http\Controllers\CommonController;
use Modules\Osce\Entities\QuestionBankEntities\ExamQuestionLabel;
use Modules\Osce\Entities\QuestionBankEntities\ExamQuestionLabelType;

class ExamLabelController extends CommonController
{
    public function getExamLabel(){
        //dd('考核表签');
        $ExamQuestionLabelType=new ExamQuestionLabelType();
        $ExamQuestionLabelTypeList= $ExamQuestionLabelType->labelTypeList();


        return view('osce::admin.resourcemanage.subject_check_tag',['ExamQuestionLabelTypeList'=>$ExamQuestionLabelTypeList]);

    }


}