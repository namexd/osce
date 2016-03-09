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
use Modules\Osce\Entities\QuestionBankEntities\ExamQuestion;
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
        //dd($examList);
        return view('osce::admin.resourcemanage.subject_papers', ['data' => $examList]);
    }

    /**
     * 获取考核范围
     * @url       GET /osce/admin/exampaper/question-round
     * @access    public
     * @param Request $request get请求<br><br>
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
        return $this->success_data($LabelTypeList);
    }

    /**
     * 删除试卷
     * @url       GET /osce/admin/exampaper/delete-exam
     * @access    public
     * @param Request $request get请求<br><br>
     * @param Exam $exam
     * @return view
     * @throws \Exception
     * @version   1.0
     * @author    weihuiguo <weihuiguo@misrobot.com>
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function getDeleteExam(Request $request)
    {
        //验证试卷ID
        $this->validate($request,[
            'id'        => 'required|integer',
        ]);
        $id = $request->id;

        $Paper = new ExamQuestionPaper();
        $delete = $Paper->where('id','=',$id)->delete();

        if($delete){
            return redirect()->route('osce.admin.ExamPaperController.getExamList');
        }else{
            return redirect()->route('osce.admin.ExamPaperController.getExamList');
        }
    }

    /**
     * 新增试卷页面
     * @url       GET /osce/admin/exampaper/delete-exam
     * @access    public
     * @param Request $request get请求<br><br>
     * @param Exam $exam
     * @return view
     * @throws \Exception
     * @version   1.0
     * @author    weihuiguo <weihuiguo@misrobot.com>
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function getAddExamPage(Request $request)
    {
        return view('osce::admin.resourcemanage.subject_papers_add');
    }


    /**
     * 新增试卷时ajax请求标签类型
     * @url       GET /osce/admin/exampaper/exam-label-get
     * @access    public
     * @param Request $request get请求<br><br>
     * @param Exam $exam
     * @return view
     * @throws \Exception
     * @version   1.0
     * @author    weihuiguo <weihuiguo@misrobot.com>
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved


     */
    public function getExamLabelGet(Request $request)
    {
        $LabelType= new ExamQuestionLabelType();
        //\DB::connection("osce_mis")->enableQueryLog();
        $label = $LabelType->getLabAndType()->toArray();
        //dd(\DB::connection("osce_mis")->getQueryLog());
        return $this->success_data($label);
    }

    /**
     * ajax请求考试题目
     * @url       GET /osce/admin/exampaper/exam-questions
     * @access    public
     * @param Request $request get请求<br><br>
     * @param Exam $exam
     * @return view
     * @throws \Exception
     * @version   1.0
     * @author    weihuiguo <weihuiguo@misrobot.com>
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved


     */
    public function getExamQuestions(Request $request)
    {
        //验证试题类型ID
        $this->validate($request,[
            'subject_id'        => 'required',
            'ability_id'        => 'required',
            'difficult_id'        => 'required',
        ]);

        //接收筛选参数
        $data = [
            intval($request -> subject_id),
            intval($request -> ability_id),
            intval($request -> difficult_id)
        ];

        //根据筛选参数查找试题数据
        $ExamQuestion = new ExamQuestion();

        $questions = $ExamQuestion -> getExamQuestion($data);
//        foreach($questions as $k => $v){
//            foreach($v->exam_question_label_relation as $kk => $vv){
//                dd($vv);
//            }
//        }
        dd($questions);
        exit;
    }
}