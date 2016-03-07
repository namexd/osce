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
use Illuminate\Http\Request;

class ExamLabelController extends CommonController
{
    public function getExamLabel(Request $Request){
/*        $this->validate($Request,[
            'keyword'=>'required|integer',
            'label_type_id'=>'required|integer',
        ]);*/
        $ExamQuestionLabelType=new ExamQuestionLabelType();
        $ExamQuestionLabelTypeList= $ExamQuestionLabelType->examQuestionLabelTypeList();


        return view('osce::admin.resourcemanage.subject_check_tag',['ExamQuestionLabelTypeList'=>$ExamQuestionLabelTypeList]);

    }

    public function AddExamQuestionLabel(Request $Request){
        $this->validate($Request,[

        ]);


    }


}