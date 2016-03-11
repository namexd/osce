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
use Modules\Osce\Entities\QuestionBankEntities\ExamPaper;
use Modules\Osce\Entities\QuestionBankEntities\ExamQuestionLabelType;
use Modules\Osce\Entities\QuestionBankEntities\ExamQuestionType;
use Modules\Osce\Entities\QuestionBankEntities\ExamQuestion;
use Modules\Osce\Http\Controllers\CommonController;
use Illuminate\Support\Facades\Auth;
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
        $examPaper= new ExamPaper();
        $examList = $examPaper->getExamPaperlist($keyword);
        //dd($examList->toArray());
        return view('osce::admin.resourcemanage.subject_papers', ['data' => $examList,'keyword' => $keyword]);
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

        $Paper = new ExamPaper();
        $delete = $Paper->where('id','=',$id)->delete();

        if($delete){
            return redirect()->route('osce.admin.ExamPaperController.getExamList');
        }else{
            return redirect()->route('osce.admin.ExamPaperController.getExamList');
        }
    }

    /**
     * 新增试卷页面
     * @url       GET /osce/admin/exampaper/add-exam-page
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
        //查找标签类型下的标签
        $label = $this->getExamLabelGet();

        //查找试题类型
        $question = ExamQuestionType::where('status','=',1)->select('id','name')->get()->toArray();
        return view('osce::admin.resourcemanage.subject_papers_add',['label'=>$label,'question'=>$question]);
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
    public function getExamLabelGet()
    {
        $LabelType= new ExamQuestionLabelType();
        //\DB::connection("osce_mis")->enableQueryLog();
        $label = $LabelType->getLabAndType()->toArray();
        //dd(\DB::connection("osce_mis")->getQueryLog());
        return $label;
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
            'subject_id'        => 'sometime|integer',
            'ability_id'        => 'sometime|integer',
            'difficult_id'        => 'sometime|integer',
        ]);

        //接收筛选参数
        $data = [
            intval($request -> subject_id),
            intval($request -> ability_id),
            intval($request -> difficult_id)
        ];

        //根据筛选参数查找试题数据
        $ExamQuestion = new ExamQuestion();

        $pageIndex = $request->page?$request->page:1;//获取页码

        $questions = $ExamQuestion -> getExamQuestion($data,$pageIndex);
        dd($questions);
        exit;
    }

    /**
     * 新增试卷操作
     * @url       GET /osce/admin/exampaper/add-exams
     * @access    public
     * @param Request $request get请求<br><br>
     * @param Exam $exam
     * @return view
     * @throws \Exception
     * @version   1.0
     * @author    weihuiguo <weihuiguo@misrobot.com>
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function getAddExams(Request $request){
        //验证试题类型ID
        $this->validate($request,[
            'name'        => 'required',
            'time'        => 'required',
            'status'        => 'required',
            'status2'        => 'required',
            'question'        => 'required',
        ]);
        DB::beginTransaction();

        $user = Auth::user();
        //接收参数
        $data = [
            'name' => $request -> name,
            'length' => $request -> time,
            'mode' => $request -> status,
            'type' => $request -> status2,
            'created_user_id' => $user->id
        ];

        //获取试卷类型
        $status = $request -> status;
        $status2 = $request -> status2;

        //向试卷表插入基础数据
        $examPaper = ExamPaper::create($data);
        if(!$examPaper){
            DB::rollback();
            return false;
        }

        $examPaperID = $examPaper->id;
        //dd($examPaperID);
        if($status == 1 && $status2 == 1){//自动-随机

        }elseif($status == 1 && $status2 == 2){//自动-统一

        }elseif($status == 2 && $status2 == 2){//手动-统一

        }


    }

    /**
     * TODO tangjun
     * @param Request $request
     */
    public function scopeCallback(Request $request){
        die(json_encode($request->all()));
    }

    /**
     * 选择试题页面
     * @url       GET /osce/admin/exampaper/examp-questions
     * @access    public
     * @param Request $request get请求<br><br>
     * @param Exam $exam
     * @return view
     * @throws \Exception
     * @version   1.0
     * @author    weihuiguo <weihuiguo@misrobot.com>
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function getExampQuestions(){
        return view('osce::admin.resourcemanage.subject_papers_add_detail');
    }
}