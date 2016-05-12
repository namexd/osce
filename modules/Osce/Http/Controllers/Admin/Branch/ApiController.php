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
use Modules\Osce\Entities\Drawlots\DrawlotsRepository;
use Modules\Osce\Entities\ExamDraft;
use Modules\Osce\Entities\ExamMidway\ExamMidwayRepository;
use Modules\Osce\Entities\ExamOrder;
use Modules\Osce\Entities\ExamResult;
use Modules\Osce\Entities\ExamStation;
use Modules\Osce\Entities\ExamStationStatus;
use Modules\Osce\Entities\QuestionBankEntities\ExamMonitor;
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
use Modules\Osce\Entities\Watch;
use Modules\Osce\Entities\WatchLog;
use Modules\Osce\Http\Controllers\Api\Pad\DrawlotsController;
use Modules\Osce\Http\Controllers\CommonController;
use Modules\Osce\Entities\QuestionBankEntities\ExamQuestionLabelType;
use Modules\Osce\Entities\QuestionBankEntities\ExamQuestionType;
use Modules\Osce\Entities\QuestionBankEntities\ExamQuestionLabel;
use Modules\Osce\Repositories\Common;
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
use Illuminate\Support\Facades\Redis;
use Modules\Osce\Http\Controllers\Api\StudentWatchController;
use Modules\Osce\Repositories\WatchReminderRepositories;

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
        return  view('osce::admin.resourceManage.subject_papers_add_detail',[
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
       // dd($PaperPreviewArr);

        //-------------------------------------
        return  view('osce::admin.resourceManage.subject_papers_add_preview',['PaperPreviewArr'=>$PaperPreviewArr]);
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
    public function LoginAuth(Request $request, ExamMidwayRepository $examMidway){
        $this->validate($request,[
            'username'  =>  'required',
            'password'  =>  'required',
        ]);

        $username = $request->get('username');
        $password = $request->get('password');
        if (\Auth::attempt(['username' => $username, 'password' => $password]))
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
            \Log::debug('获考试信息',[]);
            //获取当前登录账户的角色名称
            $questionBankRepositories = new QuestionBankRepositories();
            $roleType = $questionBankRepositories->getExamLoginUserRoleType();
            //dd($roleType);
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
    public function LoginAuthWait(QuestionBankRepositories $questionBankRepositories, ExamMidwayRepository $examMidway, DrawlotsRepository $drawlots){

        try {
            $user = Auth::user();
            // 检查用户是否登录
            if (is_null($user)) {
                throw new \Exception('您还没有登录，请先登录', 1000);
            }
            //检验登录的老师是否是监考老师
            if (!$questionBankRepositories->LoginAuth()) {
                throw new \Exception('您不是监考老师', 1001);
            }

            //根据监考老师的id，获取对应的考站id
            $ExamInfo = $questionBankRepositories->GetExamInfo($user);
            // 还要判断监考老师的类型是不是理论站的监考老师-station_teacher
            $stationModel = new Station();
            $station = $stationModel->where('id', '=', $ExamInfo['StationId'])->first();

            if($station->type != 3) {
                throw new \Exception('您不是理论考试的监考老师', 1002);
            }
            $data = array(
                'status'=>1,
                'name'      => $ExamInfo['ExamName'],
                'stationId' => $ExamInfo['StationId'],
                'examId'    => $ExamInfo['ExamId'],
                'userId'    => $user->id,
            );
            try{
                //拿到场次id
                $ExamScreeningModel =  new ExamScreening();
                $examscreeningId = $ExamScreeningModel->getScreenID($ExamInfo['ExamId']);
                //拿到考场id
                $roomId = ExamDraft::getExamRoom($ExamInfo['ExamId'],$examscreeningId ,$ExamInfo['StationId']);

                $examStationStatusModel  =  new ExamStationStatus();

                $stationIds = $drawlots->getStationNum($ExamInfo['ExamId'],$roomId->room_id ,$examscreeningId);
                //拿到场次下房间里该老师支持的考站
                $station_id = array_intersect($ExamInfo['StationId'], $stationIds);
                //改变老师支持该考站的准备状态

                $StationStatus = $examStationStatusModel->getStationStatus($data['examId'],$station_id,$examscreeningId);

                if ($examMidway->isChangeToTwo($data['examId'],$stationIds)) {
                    //就把所有考站改为2
                    $StationStatus = $examStationStatusModel->getStationStatus($data['examId'],$stationIds,$examscreeningId,$type =2);
                }
            }catch (\Exception $ex){
                \Log::debug('理论考试老师登陆改变准备状态出错',[$data,]);
            }
          


            return view('osce::admin.theoryCheck.theory_check_volidate', [
                'data' => $data,
            ]);
        }
        catch(\Exception $ex)
        {
            if ($ex->getCode() === 1000) {
                return redirect()->route('osce.admin.ApiController.LoginAuthView')->withErrors($ex->getMessage());
            }else{
                $data = array(
                    'status'=>0,
                    'info'=>$ex->getMessage()
                );
                return view('osce::admin.theoryCheck.theory_check_volidate', [
                    'data' => $data,
                ]);
            }
        }
    }

    /**理论考试退出
     * @method
     * @url /osce/
     * @access public
     * @return \Illuminate\Http\RedirectResponse
     * @author xumin <xumin@misrobot.com>
     * @date
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function logout(){
        Auth::logout();
        return redirect()->route('osce.admin.ApiController.LoginAuthView');
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
            'stationId' => 'required|int',
        ]);
        $stationId = $request->input('stationId');//考站id
        $stationInfo = Station::where('id',$stationId)->where('type',3)->first();
        if(!empty($stationInfo)&&!empty($stationInfo->paper_id)){
            return response()->json($stationInfo->paper_id);
        }else{
            return response()->json(false);
        }
    }

    /**学生登录成功后跳转页
     * @method
     * @url api/student-exam-index
     * @access public
     * @param Request $request
     * @author weihuiguo <weihuiguo@misrobot.com>
     * @date
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function getStudentExamIndex(ExamMidwayRepository $examMidway, DrawlotsRepository $drawlots){
        $user = Auth::user();
        //查找当前正在进行的考试--之后会改
        $examingDO = Exam::where('status','=',1)->first();
        //dd($examingDO);
        if(count($examingDO) > 0){
            $studentModel = new Student();

            $userInfo = $studentModel->getStudentExamInfo($user->id,$examingDO->id);
            //在队列表中查找与考试相关的数据
            $examquen = new ExamQueue();
            $examing = $examquen->getExamingData($examingDO->id,@$userInfo->id);

            if(count($examing) > 0){
                $examing = $examing->toArray();
            }


            //dd($examing);
            //整理考试数据
            $examData = array();
            $StationTeacher = new StationTeacher();
            $ExamPaperExamStation = new ExamPaperExamStation();


            foreach($examing as $key=>$v){
//                    if(!$v['station_id']){
//                        $station_id = ExamStation::where('exam_id','=',$v['id'])->first()->station_id;
//                    }
                    $station = $v['station_id'];
                    $stationTeacher = $StationTeacher->where('station_id','=',$station)->first();
                    $examPaper = $ExamPaperExamStation->where('exam_id','=',$v['id'])->first();
                    $examData[$key]['station_id'] = $station;
                    $examData[$key]['teacher_id'] = @$stationTeacher->user_id;
                    $examData[$key]['student_id'] = @$userInfo->id;
                    $examData[$key]['paper_id'] = $examPaper->exam_paper_id;
                    $examData[$key]['exam_id'] = $v['id'];
                    $examData[$key]['exam_name'] = $v['name'];
                    $examData[$key]['status'] = $v['status'];

            }
        }
        //获取考生当前是在哪个考站考试
        $queue = $examMidway->getQueueByStudent($userInfo->id, $examingDO->id);
        //检查此考站是否是理论考站
        if (Station::find($queue->station_id)->type != 3) {
            throw new \Exception('学生当前不应该考这个考站', -111);
        }
        //修改exam_station_status表状态
        $examMidway->beginTheoryStatus($examingDO->id, [$queue->station_id], 1);
        /*
         * 判断是否要把1改成2
         */
        $screen = $drawlots->getScreening($examingDO->id);
        $stations = $drawlots->getStationNum($examingDO->id, $queue->room_id, $screen->id);
        //判断当前已经有多少个考站已经是1了
        $bool = $examMidway->isChangeToTwo($examingDO->id, $stations->pluck('station_id')->toArray());
        if ($bool) {
            $examMidway->beginTheoryStatus($examingDO->id, $stations->pluck('station_id')->toArray(), 2);
        }

        //dd($examData);
        return view('osce::admin.theoryCheck.theory_check_student_volidate', [
            'userInfo'   => @$userInfo,
            'examData' => @$examData
        ]);
    }

    /**
     * 获取当前考站所在流程考试是否已经结束
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
     */
    public function getExamPaperStatus(Request $request)
    {
        $this->validate($request, [
            'exam_id'    => 'required|integer',// 考试ID
            'station_id' => 'required|integer',// 考站ID
        ]);

        $examId = $request->get('exam_id');
        $stationId = $request->get('station_id');

        try {
            $examScreeningModel = new ExamScreening();

            //获取正在进行的考试
            $examScreening = $examScreeningModel->getExamingScreening($examId);
            if (is_null($examScreening)) {
                //获取最近一场考试
                $examScreening = $examScreeningModel->getNearestScreening($examId);
            }

            $unExamQueues = ExamQueue::where('status', '<>', 3)
                ->where('exam_id', '=', $examId)
                ->where('exam_screening_id', '=', $examScreening->id)
                ->where('station_id', '=', $stationId)
                ->get();

            if (count($unExamQueues) > 0) {
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
     * 监考老师pad端点击准备完成并给腕表推送消息
     * @method GET
     * @url /osce/admin/api/ready-exam
     * @access public
     *
     * @param Request $request get请求<br><br>
     * <b>get请求字段：</b>
     * * int        $exam_id               考试id
     * * int        $station_id            考站id
     * * int        $room_id               考场id
     * * int        $exam_screening_id     考试场次id
     * * int        $teacher_id            老师id
     *
     * @return json
     *
     * @version 3.4a
     * @author wangjiang <wangjiang@misrobot.com>
     * @date 2016-04-06 15:43
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function getReadyExam (Request $request, WatchReminderRepositories $watchReminder, DrawlotsRepository $draw)
    {
        \Log::alert('老师准备传入所有的参数', $request->all());
        $this->validate($request, [
            'exam_id'           => 'required|integer',
            'station_id'        => 'required|integer',
            'exam_screening_id' => 'required|integer',
            'teacher_id'        => 'required|integer',
            'room_id'           => 'required|integer',
        ]);

        $examId          = $request->input('exam_id');
        $stationId       = $request->input('station_id');
        $examScreeningId = $request->input('exam_screening_id');
        $teacherId       = $request->input('teacher_id');
        $roomId          = $request->input('room_id');
        // 查询当前老师对应考站准备完成信息
        $examStationStatusModel = new ExamStationStatus();
        $examStationStatus = $examStationStatusModel->where('exam_id', '=', $examId)
                                                    ->where('exam_screening_id', '=', $examScreeningId)
                                                    ->where('station_id', '=', $stationId)
                                                    ->first();

        if (is_null($examStationStatus)) {
            return response()->json(
                $this->success_data([], -1, '未查询到当前考站是否准备完成信息')
            );
        }

        // 判断考试排考方式(分考站或者考场)
        $examModel = new Exam();
        $exam = $examModel->where('id', '=', $examId)->first();
        if (is_null($exam)) {
            return response()->json(
                $this->success_data([], -4, '未查询到当前考试信息')
            );
        }
        //获取考试模式（1、考场、2、考站）
        $examSequenceMode = $exam->sequence_mode;
        $examQenenModel = new ExamQueue();
        $watchLogModel = new WatchLog();


        if ($examSequenceMode == 1) {
            // 考场排 多个学生
            $studentIds = $examQenenModel->where('exam_id', '=', $examId)
                ->where('exam_screening_id', '=', $examScreeningId)
                ->where('room_id', '=', $roomId)
                ->where('status', '<', 3)           // 确保可以多次点击（0:绑定腕表,1:抽签,2:正在考试）
                ->get()
                ->pluck('student_id')->toArray();   // 获取学生ID数组

            \Log::alert('老师准备时拿到的学生信息',[$studentIds]);
            if (empty($studentIds))
            {
                \Log::alert('未查到相应考试队列信息',[$studentIds, $request->all(),'screeningId' => Common::getExamScreening($examId)->id]);

                return response()->json(
                    $this->success_data([], -2, '未查到相应考试队列信息')
                );
            }

            $watches = $watchLogModel->leftJoin('watch', function($join){
                    $join->on('watch_log.watch_id', '=', 'watch.id');
                })
                ->whereIn('watch_log.student_id', $studentIds)
                ->where('watch.status', '=', 1)->get();

            $watchNfcCodes = [];
            if (!$watches->isEmpty()) {
                foreach ($watches as $item) {
                    $watchNfcCodes[] = $item['code'];
                }
            }

            if (empty($watchNfcCodes)) {
                return response()->json(
                    $this->success_data([], -3, '未查到相应腕表信息')
                );
            }

        } else
        {
            // 考站排 一个学生
            $examQenens = $examQenenModel->where('exam_id', '=', $examId)
                ->where('exam_screening_id', '=', $examScreeningId)
                ->where('station_id', '=', $stationId)
                ->where('status', '<', 3)       // 确保可以多次点击
                ->orderBy('begin_dt', 'asc')
                ->first();

            if (is_null($examQenens))
            {
                \Log::alert('未查到相应考试队列信息',[$examQenens, $request->all()]);

                return response()->json(
                    $this->success_data([], -2, '未查到相应考试队列信息')
                );
            }

            $watch = $watchLogModel->leftJoin('watch', function($join){
                $join->on('watch_log.watch_id', '=', 'watch.id');
            })->where('watch_log.student_id', '=', $examQenens->student_id)
                ->where('watch.status', '=', 1)
                ->select([
                    'watch.code as nfc_code',
                ])->first();

            if (is_null($watch)) {
                return response()->json(
                    $this->success_data([], -3, '未查到相应腕表信息')
                );
            }



            try {
//                $studentWatchController = new StudentWatchController();
//                $request['nfc_code'] = $watch['nfc_code'];
//                $studentWatchController->getStudentExamReminder($request, $stationId);
                $watchReminder->getWatchPublish($examId,$examQenens->student_id, $stationId, $roomId);
            } catch (\Exception $ex) {
                \Log::debug('准备考试按钮2', [$examQenens->student_id, $stationId, $roomId]);
            }
        }
        //查询该考试该考场下的所有考站信息
//        $stationArr = ExamDraft::leftJoin('exam_draft_flow', function($join){
//            $join->on('exam_draft.exam_draft_flow_id', '=', 'exam_draft_flow.id');
//        })->where('exam_draft_flow.exam_id',$examId)
//            ->where('exam_draft.room_id',$roomId)->select('exam_draft.station_id')->get()->pluck('station_id')->toArray();
        $examScreeningId = $draw->getScreening($examId)->id;
        $stationArr = $draw->getStationNum($examId, $roomId, $examScreeningId);
        if(!$stationArr->isEmpty()){
            //查询exam_station_status表（考试-场次-考站状态表）中该考试该考场下status是否全为1，如果是，修改其状态值为2

            //如果已经有状态为2了，那么就让他为2
            if ($examStationStatusModel->where('exam_id', $examId)
            ->where('status', 2)
            ->whereIn('station_id', $stationArr)
            ->first()) {
                $examStationStatus->status = 2;
            } else {
                $examStationStatus->status = 1;
            }

            if($examStationStatus->save()){
                // todo  准备好后调用腕表接口

                try {

                    \Log::alert('老师准备的学生id',$studentIds);

                    foreach($studentIds as $studentId){
                        $watchReminder->getWatchPublish($examId,$studentId, $stationId);
                    }
                } catch (\Exception $ex) {

                    \Log::debug('准备考试按钮', [$stationId, $roomId, $ex]);
                }
            } else {
                //TODO 与安卓商量如果报错，就不刷新页面
                throw new \Exception('网络故障', -112);
            }
            $examStationStatusData = $examStationStatusModel
                ->where('exam_id',$examId)
                ->where('status','=',1)
                ->whereIn('station_id',$stationArr)
                ->count();
            if($examStationStatusData == $stationArr->count()){
                $examStationStatusModel->where('exam_id',$examId)->whereIn('station_id',$stationArr)->update(['status'=>2]);
            }
        }
        $request['station_id']=$stationId;
        $request['teacher_id']=$teacherId;
        $request['exam_id']=$examId;
        $draw = \App::make('Modules\Osce\Http\Controllers\Api\Pad\DrawlotsController');
        $request['id']=$teacherId;
        $draw->getExaminee_arr($request);//当前组推送(可以获得)
        $draw->getNextExaminee_arr($request);
        $inv=new InvigilatePadController();
        $msg=$inv->getAuthentication_arr($request);//当前考生推送(如果有)
        if($msg) {
            //调用向腕表推送消息的方法
//            $examQueue = ExamQueue::where('student_id', '=', $msg->student_id)
//                ->where('station_id', '=', $stationId)
//                ->whereIn('status', [0, 2])
//                ->first();
//            if ($examQueue) {
//                $examScreeningStudentData = ExamScreeningStudent::where('exam_screening_id', '=', $examQueue->exam_screening_id)
//                    ->where('student_id', '=', $examQueue->student_id)->first();
//                $watchData = Watch::where('id', '=', $examScreeningStudentData->watch_id)->first();

        }
        return response()->json(
            $this->success_data([], 1, '当前考站准备完成成功')
        );
    }

    /**
     * Android端替考警告接口
     * @method POST
     * @url /osce/admin/api/replace-exam-alert
     * @access public
     *
     * @param Request $request post请求<br><br>
     * <b>get请求字段：</b>
     * * int        $mode               处理模式（1-否 2-是）
     * * int        $exam_id            考试id
     * * int        $student_id         学生id
     * * int        $exam_screening_id  考试场次id
     *
     * @return json
     *
     * @version 3.4a
     * @author xumin <xumin@misrobot.com>
     * @date 2016-04-05 17:54
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function postAlertExamReplace (Request $request,WatchReminderRepositories $watchReminder) {
        $this->validate($request, [
            'mode'              => 'required|in:1,2',
            'exam_id'           => 'required|integer',
            'student_id'        => 'required|integer',
            'exam_screening_id' => 'required|integer',
        ]);
        $mode            = $request->input('mode');
        $examId          = $request->input('exam_id');
        $studentId       = $request->input('student_id');
        $examScreeningId = $request->input('exam_screening_id');
        $stationId       = $request->input('station_id');
//        \Log::alert('ReplaceData', [$mode,$stationId,$examScreeningId,$stationId]);
        try {

            $examQueueModel = new ExamQueue();
            $examQueue = $examQueueModel->where('student_id', $studentId)
                ->where('exam_id',$examId)
                ->where('exam_screening_id','=',$examScreeningId)
                ->whereNotIn('status',[3,4])->get();
            if (!empty($examQueue)) {
                if ($mode == 1) {
                    //如果选择否，只是做标记
                    //向监控标记学生替考记录表插入数据
                    $examMonitorModel = new ExamMonitor();
                    foreach ($examQueue as $val) {
                        $examMonitorData = array(
                            'station_id'  =>$val->station_id,
                            'exam_id'      =>$val->exam_id,
                            'student_id'  =>$val->student_id,
                            'type'         =>1,
                            'exam_screening_id'=>$val->exam_screening_id,
                        );
                        if(!$examMonitorModel->create($examMonitorData)){
                            throw new \Exception(' 向监控标记学生替考记录表插入数据失败！',-101);
                        }
                    }
                    $retval['title'] = '标记替考成功';
                    return response()->json(
                        $this->success_data($retval,1,'success')
                    );
                } else {

                    //如果选择是，终止这场考试
                    foreach ($examQueue as $val) {
                        //更新exam_queue表（考试队列）
                        $val->status = 3;
                        $val->blocking = 1;
                        if(!$val->save()){
                            throw new \Exception(' 更新考试队列表失败！',-102);
                        }

                        //向exam_result（考试结果记录表）插入数据
                        $examResultData = [
                            'student_id'        => $studentId,
                            'exam_screening_id' => $examScreeningId,
                            'station_id'        => $val->station_id,
                            'begin_dt'          => date('Y-m-d H:i:s', time()),
                            'end_dt'            => date('Y-m-d H:i:s', time()),
                            'score'             => 0,
                            'score_dt'          => date('Y-m-d H:i:s', time()),
//                            'create_user_id'    => Auth::user()->id,
                        ];
                        if(!ExamResult::create($examResultData)){
                            throw new \Exception(' 向考试结果记录表插入数据失败！',-106);
                        }

                    }

                    //更新exam_screening_student表（考试场次-学生关系表）
                    $result = ExamScreeningStudent::where('exam_screening_id',$examScreeningId)->where('student_id',$studentId)->first();

                    if(!empty($result)&&$result->is_end!=1){
//                        $result->is_end =1;
                        $result->status =2;
                        if(!$result->save()){
                            throw new \Exception(' 更新考试场次-学生关系表失败！',-103);
                        }
                    }

                    //更新exam_order表（考试学生排序）
                    $examOrder = ExamOrder::where('exam_id',$examId)->where('exam_screening_id',$examScreeningId)->where('student_id',$studentId)->first();
                    if(!empty($examOrder)&&$examOrder->status!=2){
                        $examOrder->status = 5; //为替考结束考试
                        if(!$examOrder->save()){
                            throw new \Exception(' 更新考试学生排序表失败！',-104);
                        }
                    }

                    //向监控标记学生替考记录表插入数据
                    $examMonitorData = array(
                        'station_id'  =>$stationId,
                        'exam_id'      =>$examId,
                        'student_id'  =>$studentId,
                        'description' =>3,
                        'exam_screening_id'=>$examScreeningId,
                    );
                    if(!ExamMonitor::create($examMonitorData)){
                        throw new \Exception(' 向监控标记学生替考记录表插入数据失败！',-105);
                    }
                    try{
                        $watchReminder ->getWatchPublish($examId,$studentId, $stationId);

                    }catch (\Exception $ex){
                        \Log::debug('监控调用腕表出错',[$examId,$studentId, $stationId]);
                    }

                    $retval['title'] = '确定替考成功';
                    return response()->json(
                        $this->success_data($retval,1,'success')
                    );
                }
            }
        }catch (\Exception $ex) {
            return response()->json($this->fail($ex));

        }

    }
}