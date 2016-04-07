<?php
/**
 * Created by PhpStorm.
 * @author tangjun <tangjun@misrobot.com>
 * @date 2016-03-10 14:11
 * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
 */

namespace Modules\Osce\Http\Controllers\Admin\Branch;

use App\Entities\User;
use Illuminate\Support\Facades\Auth;

use Modules\Osce\Entities\Station;
use Modules\Osce\Entities\Student;

use Modules\Osce\Entities\ExamAbsent;
use Modules\Osce\Entities\ExamFlowRoom;
use Modules\Osce\Entities\ExamFlowStation;
use Modules\Osce\Entities\ExamPlan;
use Modules\Osce\Entities\ExamQueue;
use Modules\Osce\Entities\ExamScreening;

use Modules\Osce\Entities\QuestionBankEntities\ExamPaperExamStation;
use Modules\Osce\Entities\RoomStation;
use Modules\Osce\Http\Controllers\CommonController;
use Modules\Osce\Entities\QuestionBankEntities\ExamQuestionLabelType;
use Modules\Osce\Entities\QuestionBankEntities\ExamQuestionType;
use Modules\Osce\Entities\QuestionBankEntities\ExamQuestionLabel;
use Modules\Osce\Repositories\QuestionBankRepositories;
use Modules\Osce\Entities\QuestionBankEntities\ExamPaperFormal;
use Modules\Osce\Entities\QuestionBankEntities\ExamQuestion;
use Modules\Osce\Entities\QuestionBankEntities\ExamPaper;
use Modules\Osce\Entities\Exam;
use Illuminate\Http\Request;
use Modules\Osce\Entities\ExamScreeningStudent;
use Modules\Osce\Http\Controllers\Api\InvigilatePadController;
use Modules\Osce\Http\Controllers\Admin\Branch\AnswerController;
use Modules\Osce\Entities\StationTeacher;
class ApiController extends CommonController
{
    private $name;
    /**
     * @method
     * @url /osce/
     * @access public
     * @return \Illuminate\View\View
     * @author tangjun <tangjun@misrobot.com>
     * @date    2016年3月10日14:19:34
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function GetEditorExamPaperItem(QuestionBankRepositories $questionBankRepositories){
        $question_detail = \Input::get('question_detail','');
        $questionArr = [];
        $questionInfo = [];
        if($question_detail){
            $questionInfo = $questionBankRepositories->StrToArr($question_detail);
            $questionArr = $questionBankRepositories->HandlePaperPreviewArr(['0'=>$questionInfo]);
        }
        //获取题目类型列表
        $examQuestionTypeModel= new ExamQuestionType();
        $examQuestionTypeList = $examQuestionTypeModel->examQuestionTypeList();
        //获取考核范围列表（标签类型列表）
        $examQuestionLabelTypeModel = new ExamQuestionLabelType();
        $examQuestionLabelTypeList = $examQuestionLabelTypeModel->examQuestionLabelTypeList();
        foreach($examQuestionLabelTypeList as $k=>$v){
            $examQuestionLabelTypeList[$k]['examQuestionLabelList'] = $v->examQuestionLabel;

            if(count($questionArr)>0){
                foreach($questionArr as $val){
                    if(count($val['child'])>0){
                        foreach($val['child'] as $key => $value){
                            if($key == $v['id']){
                                $examQuestionLabelTypeList[$k]['examQuestionLabelSelectedList'] = $value;
                            }
                        }
                    }
                }
            }
        }
        // dd($questionInfo);
        return  view('osce::admin.resourcemanage.subject_papers_add_detail',[
            'examQuestionLabelTypeList'=>$examQuestionLabelTypeList,
            'examQuestionTypeList'=>$examQuestionTypeList,
            'questionInfo'=>$questionInfo,
            'ordinal'=>\Input::get('ordinal',''),
            'structureId'=>\Input::get('structureId',''),
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
                    1=>empty($request->get('questionNumber'))?0:$request->get('questionNumber'),
                    2=>empty($request->get('questionScore'))?0:$request->get('questionScore')
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
            //'question'        => 'required|array',
        ]);
        //（1.包含，2.等于）

        $str = [
            0=>'一',
            1=>'二',
            2=>'三',
            3=>'四',
            4=>'五',
            5=>'六',
            6=>'七',
            7=>'八',
            8=>'九',
            9=>'十',
        ];
        //组卷方式(1.自动组卷，2.手工组卷)
        $mode = $request->status;
        //试卷类型(1.随机试卷，2.统一试卷)
        $type = $request->status2;
        $PaperPreviewArr = [];
        $PaperNameMd5 = md5($request->name);
        $PaperPreviewArr['name'] = $request->name;
        $PaperPreviewArr['time'] = $request->time;
        $PaperPreviewArr['total_score'] = 0;


        $ExamQuestion = new ExamQuestion;
        $ExamQuestionType = new ExamQuestionType;
        $paperid = $request->paperid;
        //试卷类型(1.随机试卷，2.统一试卷)
//-_-------------------------------------------
        //`mode` 组卷方式(1.自动组卷，2.手工组卷),
        //   `type` 试卷类型(1.随机试卷，2.统一试卷),
        if($paperid) {//修改
            if($mode==1){
                if($type==1){
                    if(!empty($request->question)){
                        foreach($request->question as $k => $v){
                            $PaperPreviewArr['item'][$k] = $questionBankRepositories->StrToArr($v);
                        }
                    }
                    $PaperPreviewArr['item'] = $questionBankRepositories->StructureExamQuestionArr($PaperPreviewArr['item']);
                    foreach($PaperPreviewArr['item'] as $k => $v){
                        if(!empty($v['child'])){
                            $ExamQuestionList = $ExamQuestion->whereIn('id',$v['child'])->with('examQuestionItem')->get();
                            $ExamQuestionTypeInfo = $ExamQuestionType->where('id','=',$v['type'])->select('name')->first();
                            $PaperPreviewArr['item'][$k]['name'] = $str[$k].'、'.$ExamQuestionTypeInfo['name'].'（共'.$v['num'].'题，每题'.$v['score'].'分）';
                            $PaperPreviewArr['item'][$k]['child'] = $ExamQuestionList;
                            $PaperPreviewArr['total_score'] += intval($v['num']*$v['score']);
                        }
                    }
                }else{//type 2
                    //没有修改
                    $ExamPaperInfo = $questionBankRepositories->GenerateExamPaper($paperid,1);
                    $flag_tag=$questionBankRepositories-> updateMsg($request->question,$ExamPaperInfo);
                    //-----------------


                    //-----------------

                    if(!$flag_tag){//没有缓存 第一次预览

                        if(count($ExamPaperInfo->ExamPaperStructure)>0) {
                            foreach ($ExamPaperInfo->ExamPaperStructure as $k => $v) {
                                $name = ExamQuestionType::where('id', '=', $v['exam_question_type_id'])->pluck('name');
                                $PaperPreviewArr['item'][$k]['name'] = $str[$k] . '、' . $name . '（共' . $v['num'] . '题，每题' . $v['score'] . '分）';
                                $ExamQuestionId = [];
                                if (count($v->ExamPaperStructureQuestion) > 0) {
                                    $ExamQuestionId = $v->ExamPaperStructureQuestion->pluck('exam_question_id');
                                }
                                $ExamQuestionList = $ExamQuestion->whereIn('id', $ExamQuestionId)->with('examQuestionItem')->get();

                                $PaperPreviewArr['item'][$k]['child'] = $ExamQuestionList;
                                $PaperPreviewArr['total_score'] += intval($v['num'] * $v['score']);
                            }
                        }
                    }else{//修改过随机试卷
                        if(!empty($request->question)){
                            foreach($request->question as $k => $v){
                                $PaperPreviewArr['item'][$k] = $questionBankRepositories->StrToArr($v);
                            }
                        }
                        $PaperPreviewArr['item'] = $questionBankRepositories->StructureExamQuestionArr($PaperPreviewArr['item']);
                        \Cache::put($PaperNameMd5,$PaperPreviewArr['item'],config('osce.minutes',5));
                        foreach($PaperPreviewArr['item'] as $k => $v){
                            if(!empty($v['child'])){
                                $ExamQuestionList = $ExamQuestion->whereIn('id',$v['child'])->with('examQuestionItem')->get();
                                $ExamQuestionTypeInfo = $ExamQuestionType->where('id','=',$v['type'])->select('name')->first();
                                $PaperPreviewArr['item'][$k]['name'] = $str[$k].'、'.$ExamQuestionTypeInfo['name'].'（共'.$v['num'].'题，每题'.$v['score'].'分）';
                                $PaperPreviewArr['item'][$k]['child'] = $ExamQuestionList;
                                $PaperPreviewArr['total_score'] += intval($v['num']*$v['score']);
                            }
                        }
                    }
                }
            }else{ //mode 2 type 只能为2
                $questionData = $request->get('question-type');
                if(count($questionData)>0){
                    foreach($questionData as $k => $v){
                        $questionInfo = explode('@',$v);
                        $ExamQuestionId = isset($questionInfo[2])&&!empty($questionInfo[2])?explode(',',$questionInfo[2]):[];
                        $ExamQuestionList = $ExamQuestion->whereIn('id',$ExamQuestionId)->with('examQuestionItem')->get();
                        $ExamQuestionTypeInfo = $ExamQuestionType->where('id','=',$questionInfo[0])->select('name')->first();
                        $PaperPreviewArr['item'][$k]['name'] = $str[$k].'、'.$ExamQuestionTypeInfo['name'].'（共'.count($ExamQuestionId).'题，每题'.$questionInfo[1].'分）';
                        $PaperPreviewArr['item'][$k]['child'] = $ExamQuestionList;
                        $PaperPreviewArr['total_score'] += intval(count($ExamQuestionId)*$questionInfo[1]);
                    }
                }
            }

        }else{//新增
            if($mode==1){
                if($type==1){
                    if(!empty($request->question)){
                        foreach($request->question as $k => $v){
                            $PaperPreviewArr['item'][$k] = $questionBankRepositories->StrToArr($v);
                        }
                    }
                    $PaperPreviewArr['item'] = $questionBankRepositories->StructureExamQuestionArr($PaperPreviewArr['item']);
                    foreach($PaperPreviewArr['item'] as $k => $v){
                        if(!empty($v['child'])){
                            $ExamQuestionList = $ExamQuestion->whereIn('id',$v['child'])->with('examQuestionItem')->get();
                            $ExamQuestionTypeInfo = $ExamQuestionType->where('id','=',$v['type'])->select('name')->first();
                            $PaperPreviewArr['item'][$k]['name'] = $str[$k].'、'.$ExamQuestionTypeInfo['name'].'（共'.$v['num'].'题，每题'.$v['score'].'分）';
                            $PaperPreviewArr['item'][$k]['child'] = $ExamQuestionList;
                            $PaperPreviewArr['total_score'] += intval($v['num']*$v['score']);
                        }
                    }
                }else{//type 2
                    if(!empty($request->question)){
                        foreach($request->question as $k => $v){
                            $PaperPreviewArr['item'][$k] = $questionBankRepositories->StrToArr($v);
                        }
                    }

                    $PaperPreviewArr['item'] = $questionBankRepositories->StructureExamQuestionArr($PaperPreviewArr['item']);

                    \Cache::put($PaperNameMd5,$PaperPreviewArr['item'],config('osce.minutes',5));
                    foreach($PaperPreviewArr['item'] as $k => $v){
                        if(!empty($v['child'])){
                            $ExamQuestionList = $ExamQuestion->whereIn('id',$v['child'])->with('examQuestionItem')->get();
                            $ExamQuestionTypeInfo = $ExamQuestionType->where('id','=',$v['type'])->select('name')->first();
                            $PaperPreviewArr['item'][$k]['name'] = $str[$k].'、'.$ExamQuestionTypeInfo['name'].'（共'.$v['num'].'题，每题'.$v['score'].'分）';
                            $PaperPreviewArr['item'][$k]['child'] = $ExamQuestionList;
                            $PaperPreviewArr['total_score'] += intval($v['num']*$v['score']);
                        }
                    }


                }

            }else{ //mode 2 type 只能为2
                $questionData = $request->get('question-type');
                if(count($questionData)>0){
                    foreach($questionData as $k => $v){
                        $questionInfo = explode('@',$v);
                        $ExamQuestionId = isset($questionInfo[2])&&!empty($questionInfo[2])?explode(',',$questionInfo[2]):[];
                        $ExamQuestionList = $ExamQuestion->whereIn('id',$ExamQuestionId)->with('examQuestionItem')->get();
                        $ExamQuestionTypeInfo = $ExamQuestionType->where('id','=',$questionInfo[0])->select('name')->first();
                        $PaperPreviewArr['item'][$k]['name'] = $str[$k].'、'.$ExamQuestionTypeInfo['name'].'（共'.count($ExamQuestionId).'题，每题'.$questionInfo[1].'分）';
                        $PaperPreviewArr['item'][$k]['child'] = $ExamQuestionList;
                        $PaperPreviewArr['total_score'] += intval(count($ExamQuestionId)*$questionInfo[1]);
                    }
                }
            }
        }

        //-------------------------------------
        return  view('osce::admin.resourcemanage.subject_papers_add_preview',['PaperPreviewArr'=>$PaperPreviewArr]);
    }

    /**
     * @method
     * @url /osce/
     * @access public
     * @param QuestionBankRepositories $questionBankRepositories
     * @author tangjun <tangjun@misrobot.com>
     * @date    2016年3月15日09:22:47
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function GenerateExamPaper(QuestionBankRepositories $questionBankRepositories){
        //\DB::connection('osce_mis')->enableQueryLog();
        $ExamPaperInfo = $questionBankRepositories->GenerateExamPaper(20);
        //$queries = \DB::connection('osce_mis')->getQueryLog();
        $ExamPaperFormal = new ExamPaperFormal;
        if(count($ExamPaperInfo)>0){
            $ExamPaperFormal->CreateExamPaper($ExamPaperInfo);
        }else{
            dd('试卷没有内容');
        }

    }

    /**
     * @method
     * @url /osce/
     * @access public
     * @return \Illuminate\View\View
     * @author tangjun <tangjun@misrobot.com>
     * @date    2016年3月14日15:40:51
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function ExamineeInfo(QuestionBankRepositories $questionBankRepositories){
        $this->name = \Route::currentRouteAction();
        //$userId = $questionBankRepositories->LoginAuth();
        //dd($questionBankRepositories->GetExamInfo(347));
        return  view('osce::admin.theoryCheck.theory_check_volidate');
    }

    /**监考老师登录界面
     * @method
     * @url /osce/
     * @access public
     * @return \Illuminate\View\View
     * @author xumin <xumin@misrobot.com>
     * @date
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function LoginAuthView(){
        return  view('osce::admin.theoryTest.theory_login');
    }

    /**监考老师登录数据交互
     * @method
     * @url /osce/
     * @access public
     * @param Request $request
     * @return $this|\Illuminate\Http\RedirectResponse
     * @author tangjun <tangjun@misrobot.com>
     * @date    2016年3月16日09:49:31
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function LoginAuth(Request $request){
        $this->validate($request,[
            'username'  =>  'required',
            'password'  =>  'required',
        ]);

        $username = $request->get('username');
        $password = $request->get('password');

        if (Auth::attempt(['username' => $username, 'password' => $password]))
        {
            /*
            //获取当前登录账户的角色名称
            $user = new User();
            $userInfo = $user->getUserRoleName($username);

            if($userInfo->name == '监考老师'){
                return redirect()->route('osce.admin.ApiController.LoginAuthWait'); //必须是redirect
            }else if($userInfo->name == '考生'){
                return redirect()->route('osce.admin.ApiController.getStudentExamIndex'); //必须是redirect
            }else{
                return redirect()->back()->withErrors('你没有权限！');
            }
            */

            //获取当前登录账户的角色名称
            $questionBankRepositories = new QuestionBankRepositories();
            $roleType = $questionBankRepositories->getExamLoginUserRoleType();

            if($roleType == 1){
                return redirect()->route('osce.admin.ApiController.LoginAuthWait'); //必须是redirect
            }else if($roleType == 2){
                return redirect()->route('osce.admin.ApiController.getStudentExamIndex'); //必须是redirect
            }else{
                return redirect()->back()->withErrors('你没有权限！');
            }

        }
        else
        {
            return redirect()->back()->withErrors('账号密码错误');
        }
    }

    /**
     * 监考老师登录后等待界面
     * @method GET
     * @url /osce/admin/api/loginauth-wait
     * @access public
     *
     * @param Request $request get请求<br><br>
     * <b>get请求字段：</b>
     * * string        参数英文名        参数中文名(必须的)
     *
     * @return view
     *
     * @version 1.0
     * @author wangjiang <wangjiang@misrobot.com>
     * @date 2016-03-29 11:05
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function LoginAuthWait(QuestionBankRepositories $questionBankRepositories){
        try {
            $user = Auth::user();

            // 检查用户是否登录
            if (is_null($user)) {
                throw new \Exception('用户未登录', 1000);
            }

            //检验登录的老师是否是监考老师
            if (!$questionBankRepositories->LoginAuth()) {
                throw new \Exception('你不是监考老师', 1001);
            }

            //根据监考老师的id，获取对应的考站id
            $ExamInfo = $questionBankRepositories->GetExamInfo($user);
            if (is_array($ExamInfo)) {
                // 还要判断监考老师的类型是不是理论站的监考老师-station_teacher
                $stationModel = new Station();
                $station = $stationModel->where('id', '=', $ExamInfo['StationId'])->first();

                if($station->type != 3) {
                    throw new \Exception('你不是理论考试的监考老师', 1002);
                }

                $data = array(
                    'status'=>1,
                    'name'      => $ExamInfo['ExamName'],
                    'stationId' => $ExamInfo['StationId'],
                    'examId'    => $ExamInfo['ExamId'],
                    'userId'    => $user->id,
                );
            } else {
                $data = array(
                    'status'=>0,
                    'info'=>$ExamInfo
                );
            }
            return view('osce::admin.theoryCheck.theory_check_volidate', [
                'data' => $data,
            ]);
        }
        catch(\Exception $ex)
        {
            if ($ex->getCode() === 1000) {
                return redirect()->route('osce.admin.ApiController.LoginAuthView')->withErrors($ex->getMessage());
            }
            if($ex->getCode() === 1001 || $ex->getCode() === 1002)
            {
                //return redirect()->route('osce.admin.index')->withErrors($ex->getMessage());
                Auth::logout();
                return redirect()->route('osce.admin.ApiController.LoginAuthView')->withErrors($ex->getMessage());
            }
        }
    }

    /**刷完腕表后，获取该考生对应的试卷id
     * @method
     * @url /osce/
     * @access public
     * @param Request $request
     * @author xumin <xumin@misrobot.com>
     * @date
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function getExamPaperId(Request $request)
    {
        $this->validate($request, [
            'examId' => 'sometimes|integer',//试卷id
            'stationId' => 'sometimes|integer',//试卷id
        ]);
        $examId = $request->input('examId');//考试id
        $stationId = $request->input('stationId');//考站id
        //根据考试id和考站id查询对应的试卷id
        $examPaperExamStationModel = new ExamPaperExamStation();
        $data = $examPaperExamStationModel->where('exam_id','=',$examId)->where('station_id','=',$stationId)->first();
        if(!empty($data)){
            $examPaperId = $data['exam_paper_id'];
            return response()->json($examPaperId);
        }else{
            return response()->json(false);
        }
    }

    /**学生登录成功后跳转页
     * @method
     * @url api/student-exam-index
     * @access public
     * @param Request $request
     * @author xumin <weihuiguo@misrobot.com>
     * @date
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function getStudentExamIndex(){
        $user = Auth::user();
        //查找当前正在进行的考试--之后会改
        $examingDO = Exam::where('status','=',1)->first();

        if(count($examingDO) > 0){
            $studentModel = new Student();
            $userInfo = $studentModel->getStudentExamInfo($user->id,$examingDO->id);

            $ExamScreeningStudent = new ExamScreeningStudent();
            $examing = $ExamScreeningStudent->getExamings($userInfo->id);

            if(count($examing) > 0){
                $examing = $examing->toArray();
            }
            //dd($examing);
            //整理考试数据
            $examData = array();
            $StationTeacher = new StationTeacher();
            $ExamPaperExamStation = new ExamPaperExamStation();

            foreach($examing as $key=>$v){
                    $stationTeacher = $StationTeacher->where('station_id','=',$v['station_id'])->first();
                    $examPaper = $ExamPaperExamStation->where('exam_id','=',$v['id'])->first();
                //dd($v['id']);
                //echo $v['id'];
                    $examData[$key]['station_id'] = $v['station_id'];
                    $examData[$key]['teacher_id'] = $stationTeacher->user_id;
                    $examData[$key]['student_id'] = $userInfo->id;
                    $examData[$key]['paper_id'] = @$examPaper->exam_paper_id;
                    $examData[$key]['exam_id'] = $v['id'];
                    $examData[$key]['exam_name'] = $v['name'];
                    $examData[$key]['status'] = $v['status'];

            }
        }


        return view('osce::admin.theoryCheck.theory_check_student_volidate', [
            'userInfo'   => @$userInfo,
            'examData' => @$examData
        ]);
    }
/**
     *  获取当前考站所在流程考试是否已经结束
     * @url GET /osce/admin/api/exam-paper-status
     * @access public
     *
     * @param Request $request
     * <b>get请求字段：</b>
     * * string        examId        考试ID(必须的)
     * * string        stationId     考站ID(必须的)
     *
     * @return JSON
     *
     * @version 1.0
     * @author Luohaihua <Luohaihua@misrobot.com>
     * @date 2015-12-29 17:09
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     *
     */
    public function getExamPaperStatus(Request $request)
    {
        $this->validate($request, [
            'exam_id' => 'sometimes|integer',//考试ID
            'station_id' => 'sometimes|integer',//考站ID
        ]);

        $examId = $request->get('examId');
        $stationId = $request->get('stationId');

        try {
            $examScreeningModel = new ExamScreening();
            //获取正在进行的考试
            $examScreening = $examScreeningModel->getExamingScreening($examId);
            if (is_null($examScreening)) {
                //获取最近一场考试
                $examScreening = $examScreeningModel->getNearestScreening($examId);
            }

            $exam = $examScreening->ExamInfo;




            if ($exam->sequence_mode == 1) {
                //若果是考场模式
                //room_station
                $roomStation = RoomStation::where('station_id', '=', $stationId)->first();

                $roomId = $roomStation->room_id;

                $flowRoom = ExamFlowRoom::where('room_id', '=', $roomId)
                    ->where('exam_id', '=', $examId)
                    ->first();
                $serialnumber = $flowRoom->serialnumber;

            } else {
                //若果是考站
                $flowStation = ExamFlowStation::where('station_id', '=', $stationId)
                    ->where('exam_id', '=', $examId)
                    ->first();
                $serialnumber = $flowStation->serialnumber;
            }

            $count = ExamQueue::where('serialnumber', '=', $serialnumber)
                ->where('status', '=', 3)
                ->where('exam_id', '=', $examId)
                ->where('exam_screening_id', '=', $examScreening->id)
                ->count();
    
            $screeningTotal = ExamPlan::where('exam_id', '=', $examId)
                ->where('exam_screening_id', '=', $examScreening->id)
                ->groupBy('student_id')->count();
            $absentTotal = ExamAbsent::where('exam_id', '=', $examId)
                ->where('exam_screening_id', '=', $examScreening->id)
                ->count();

            //如果  场次人数 <= 当前流程已考人数+缺考人数 为 未考完；反之  已考完
            if ($screeningTotal <= $count + $absentTotal) {
                return response()->json(
                    $this->success_data('', 1, '未考完')
                );
            } else {
                return response()->json(
                    $this->success_data('', 2, '已考完')
                );
            }
        } catch (\Exception $ex) {
            return response()->json(
                $this->fail(new \Exception('查询是否考完失败', -2))
            );
        }
    }

    /**
     * 监考老师点击准备完成
     * @method GET
     * @url /osce/admin/api/ready-exam
     * @access public
     *
     * @param Request $request get请求<br><br>
     * <b>get请求字段：</b>
     * * string        参数英文名        参数中文名(必须的)
     *
     * @return json
     *
     * @version 1.0
     * @author wangjiang <wangjiang@misrobot.com>
     * @date 2016-04-06 15:43
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function getReadyExam (Request $request) {

    }

    /**
     * Android端替考警告接口
     * @method POST
     * @url /osce/admin/api/replace-exam-alert
     * @access public
     *
     * @param Request $request post请求<br><br>
     * <b>get请求字段：</b>
     * * string        参数英文名        参数中文名(必须的)
     *
     * @return json
     *
     * @version 3.4a
     * @author wangjiang <wangjiang@misrobot.com>
     * @date 2016-04-05 17:54
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function postAlertExamReplace (Request $request) {
        $this->validate($request, [
            'mode' => 'required|in:1,2',
            'exam_screening_id' => 'required|integer',
        ]);
    }
}