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
//        $this->validate($request,[
//            'subject_id'        => 'required',
//            'ability_id'        => 'required',
//            'difficult_id'        => 'required',
//        ]);
        DB::beginTransaction();

        $data = [
            'name' => $request -> name,
            'code' => $request -> code,
            'status' => $request -> status,
            'status' => $request -> status,
        ];
        $mode = $request -> mode;
        $type = $request -> type;

        if($mode == 1 && $type == 1){//自动-随机

        }elseif($mode == 1 && $type == 2){//自动-统一

        }elseif($mode == 2 && $type == 2){//手动-统一

        }

        //向试卷表插入基础数据
        $examPaper = ExamPaper::create($data);
        if(!$examPaper){
            DB::rollback();
            return false;
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
     * 选择试题范围页面
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
        $label = $this->getExamLabelGet();

        //查找试题类型
        $question = ExamQuestionType::where('status','=',1)->select('id','name')->get()->toArray();
        return view('osce::admin.resourcemanage.subject_papers_add',['label'=>$label,'question'=>$question]);
    }
}