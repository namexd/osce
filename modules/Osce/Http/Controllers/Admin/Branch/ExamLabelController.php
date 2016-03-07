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

class ExamLabelController extends CommonController
{
    public function getExamLabel(){
        //dd('考核表签');
        $examquestion=new ExamQuestionLabel();
        $examlist= $examquestion->LabelType;
        dd($examlist);


        return view('osce::admin.resourcemanage.subject_check_tag');

    }


}