<?php
/**
 * Created by PhpStorm.
 * User: j5110
 * Date: 2016/1/6
 * Time: 10:30
 */

namespace Modules\Osce\Http\Controllers\Admin\Branch;


use App\Entities\User;
use Cache;
use Illuminate\Http\Request;
use Modules\Osce\Entities\QuestionBankEntities\ExamQuestionPaper;
use Modules\Osce\Entities\QuestionBankEntities\ExamQuestionLabelType;
use Modules\Osce\Http\Controllers\CommonController;
use DB;
class ExamPaperController extends CommonController
{
    /**
     * 获取试卷列表
     * @url       GET /osce/admin/exampaper/exam-list
     * @access    public
     * @param Request $request get请求<br><br>
     *                         <b>get请求字段：</b>
     *                         string        keyword         关键字
     * @param Exam $exam
     * @return view
     * @throws \Exception
     * @version   1.0
     * @author    weihuiguo <weihuiguo@misrobot.com>
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function getExamList(Request $request)
    {
        $keyword = $request->keyword;

        //获取试卷与试题构造表数据
        $examPaper= new ExamQuestionPaper();
        $examList = $examPaper->getExamPaperlist($keyword);

        return view('osce::admin.resourcemanage.subject_papers', ['data' => $examList]);
    }

    /**
     * 获取考核范围
     * @url       GET /osce/admin/exampaper/question-round
     * @access    public
     * @param Request $request get请求<br><br>
     *                         <b>get请求字段：</b>
     *                         string        keyword         关键字
     *                         string        order_name      排序字段名 枚举 e.g 1:设备名称 2:预约人 3:是否复位状态自检 4:是否复位设备
     *                         string        order_by        排序方式 枚举 e.g:desc,asc
     * @param Exam $exam
     * @return view
     * @throws \Exception
     * @version   1.0
     * @author    weihuiguo <weihuiguo@misrobot.com>
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function getQuestionRound(Request $request)
    {
        $LabelType= new ExamQuestionLabelType();
        $LabelTypeList = $LabelType->getLabAndType()->toArray();
        $this->success_data($LabelTypeList);
    }
}