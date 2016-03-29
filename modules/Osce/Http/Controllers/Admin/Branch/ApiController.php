<?php
/**
 * Created by PhpStorm.
 * @author tangjun <tangjun@misrobot.com>
 * @date 2016-03-10 14:11
 * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
 */

namespace Modules\Osce\Http\Controllers\Admin\Branch;

use App\Entities\User;
use Auth;
use Modules\Osce\Entities\QuestionBankEntities\ExamPaperExamStation;
use Modules\Osce\Http\Controllers\CommonController;
use Modules\Osce\Entities\QuestionBankEntities\ExamQuestionLabelType;
use Modules\Osce\Entities\QuestionBankEntities\ExamQuestionType;
use Modules\Osce\Entities\QuestionBankEntities\ExamQuestionLabel;
use Modules\Osce\Repositories\QuestionBankRepositories;
use Modules\Osce\Entities\QuestionBankEntities\ExamPaperFormal;
use Modules\Osce\Entities\QuestionBankEntities\ExamQuestion;
use Modules\Osce\Entities\QuestionBankEntities\ExamPaper;
use Illuminate\Http\Request;

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
                    //-----------------
                    $flag_tag=false;
                    //随机试卷
                    if(count($ExamPaperInfo)>0){
                        $strArr=[];
                        if(count($ExamPaperInfo->ExamPaperStructure)>0){
                            $arr = [];
                            foreach($ExamPaperInfo->ExamPaperStructure as $key => $val){
                                if(count($val->structure_label)>0){
                                    $arr[] = $val;
                                }
                            }
                            $paperDetail['item'] = $questionBankRepositories->HandlePaperPreviewArr($arr);
                            if(count($paperDetail['item'])>0){
                                foreach($paperDetail['item'] as $k =>$v){
                                    $data = [];
                                    $data['question-type'] = $v['question_type'];
                                    $data['questionNumber'] = $v['question_num'];
                                    $data['questionScore'] = $v['question_score'];
                                    $data['tag'] = $questionBankRepositories->GetExamQuestionLabelId($v['child']);
                                    if(count($v['child'])){
                                        foreach($v['child'] as $key => $val){
                                            $data['label-'.$key] = $val[0]['relation'];
                                        }
                                    }
                                    $strArr[] = $questionBankRepositories->ArrToStr($data).'@'.$v['id'];
                                }
                            }
                        }
                        if(count($request->question)&&count($strArr)) {//判段有没有修改操作
                            $flag = false;
                            foreach ($strArr as $key=>$val) {
                                    if ($strArr[$key]!= $request->question[$key]) {
                                        $flag = true;
                                        break;
                                }
                            }
                            if($flag||count($request->question)!=count($strArr)){//不相等以修改
                                $flag_tag=true;
                            }
                        }
                    }
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
        $this   ->validate($request,[
            'username'  =>  'required',
            'password'  =>  'required',
        ]);

        $username   =   $request    ->  get('username');
        $password   =   $request    ->  get('password');
        if (Auth::attempt(['username' => $username, 'password' => $password]))
        {
            return redirect()->route('osce.admin.ApiController.LoginAuthWait');
        }
        else
        {
            return redirect()->back()->withErrors('账号密码错误');
        }
    }

    /**监考老师等待学生考试界面
     * @method
     * @url /osce/
     * @access public
     * @param QuestionBankRepositories $questionBankRepositories
     * @return $this|\Illuminate\View\View
     * @author xumin <xumin@misrobot.com>
     * @date
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */

    public function LoginAuthWait(QuestionBankRepositories $questionBankRepositories){
        try{
            $user=Auth::user();
            if(is_null($user))
            {
                throw new \Exception('用户未登录');
            }
            //检验登录的老师是否是监考老师
            if(!$questionBankRepositories->LoginAuth())
            {
                throw new \Exception('你不是监考老师',1000);
            }

            //根据监考老师的id，获取对应的考站id
            $ExamInfo = $questionBankRepositories->GetExamInfo($user);
            if(is_array($ExamInfo)){
                $data = array(
                    'status'=>1,
                    'name'=>$ExamInfo['ExamName'],//考试名称
                    'stationId'=>$ExamInfo['StationId'],
                    'examId'=>$ExamInfo['ExamId'],
                    'userId'=>$user->id,
                );
            }else{
                $data=array(
                    'status'=>0,
                    'info'=>$ExamInfo
                );
            }
            return view('osce::admin.theoryCheck.theory_check_volidate', [
                'data'=>$data,
            ]);
        }
        catch(\Exception $ex)
        {
            if($ex->getCode()===1000)
            {
                return redirect()->route('osce.admin.getIndex')->withErrors($ex->getMessage());
            }
            return redirect()->route('osce.admin.ApiController.LoginAuthView')->withErrors($ex->getMessage());
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















}