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
use Illuminate\Support\Facades\Input;
use Modules\Osce\Entities\QuestionBankEntities\ExamPaper;
use Modules\Osce\Entities\QuestionBankEntities\ExamQuestionLabelType;
use Modules\Osce\Entities\QuestionBankEntities\ExamQuestionType;
use Modules\Osce\Entities\QuestionBankEntities\ExamQuestion;
use Modules\Osce\Entities\QuestionBankEntities\ExamPaperStructure;
use Modules\Osce\Entities\QuestionBankEntities\ExamPaperStructureLabel;
use Modules\Osce\Entities\QuestionBankEntities\ExamPaperStructureQuestion;
use Modules\Osce\Repositories\QuestionBankRepositories;
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
        $DB = \DB::connection('osce_mis');
        $DB->beginTransaction();
        $Paper = new ExamPaper();
        //删除试卷
        $delete = $Paper->where('id','=',$id)->delete();
        if(!$delete){
            $DB->rollback();
            return redirect()->back()->withInput()->withErrors('系统异常');
        }

        //查找试卷构造表
        $exam_paper_structure = ExamPaperStructure::where('exam_paper_id','=',$id)->first();
        //dd($exam_paper_structure);
        if($exam_paper_structure){
            $paper_structure_id = $exam_paper_structure->id;
            if(!$exam_paper_structure->delete()){
                $DB->rollback();
                return redirect()->back()->withInput()->withErrors('系统异常');
            }
        }
        $paper_structure_id = @$paper_structure_id?@$paper_structure_id:0;
        //删除试卷构造表和标签关联表数据
        if(ExamPaperStructureLabel::where('exam_paper_structure_id','=',$paper_structure_id)->delete()){
            $DB->commit();
            return redirect()->back()->withInput()->withErrors('操作成功');
        }else{
            $DB->rollBack();
            return redirect()->back()->withInput()->withErrors('系统异常');
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
    public function getAddExamPage(Request $request,QuestionBankRepositories $QuestionBankRepositories)
    {
        //查找标签类型下的标签
        $label = $this->getExamLabelGet();

        //查找试题类型
        $question = ExamQuestionType::where('status','=',1)->select('id','name')->get()->toArray();
        if($request->id){
            //验证试卷ID
            $this->validate($request,[
                'id'        => 'sometimes|integer',
            ]);
            $paperID = $request->id;
            //查找当前试卷信息
            $papers = ExamPaper::where('id','=',$paperID)->first();
            if($papers){
                //根据试卷ID查找试卷基础信息与评分标准
                $paperDetail = $QuestionBankRepositories->GenerateExamPaper($paperID,1);
                if($papers->type == 2 && $papers->mode == 2){
                    //统一试卷
                    if($paperDetail){
                        $paperDetail = $paperDetail->toArray();

                        //判断题目类型
                        $questionType = new ExamQuestionType();
                        foreach($paperDetail['item'] as $k=>$detail){
                            $paperDetail['item'][$k]['typename'] = $questionType->where('id','=',$detail['type'])->pluck('name');
                            $paperDetail['item'][$k]['child'] = implode(',',$detail['child']->toArray());
                        }

                    }
                    return view('osce::admin.resourcemanage.subject_papers_add',[
                        'label'=>$label,
                        'ExamQuestionLabelTypeList'=>$question,
                        'paperDetails' => $paperDetail,
                    ]);

                }else{

                    //随机试卷
                    if(count($paperDetail)>0){
                        if(count($paperDetail->ExamPaperStructure)>0){
                            $arr = [];
                            foreach($paperDetail->ExamPaperStructure as $key => $val){
                                if(count($val->structure_label)>0){
                                    $arr[] = $val;
                                }
                            }


                            $paperDetail['item'] = $QuestionBankRepositories->HandlePaperPreviewArr($arr);
                            if(count($paperDetail['item'])>0){

                                $item = [];
                                $questionType = new ExamQuestionType();
                                foreach($paperDetail['item'] as $k =>$v){
                                    $data = [];
                                    $data['question-type'] = $v['question_type'];
                                    $data['questionNumber'] = $v['question_num'];
                                    $data['questionScore'] = $v['question_score'];
                                    $data['tag'] = $this->GetExamQuestionLabelId($v['child']);
                                    if(count($v['child'])){
                                        foreach($v['child'] as $key => $val){
                                            $data['label-'.$key] = $val[0]['relation'];
                                        }
                                    }
                                    $v['str'] = $QuestionBankRepositories->ArrToStr($data).'@'.$paperDetail['ExamPaperStructure'][$k]['id'];
                                    $v['id'] = $paperDetail['ExamPaperStructure'][$k]['id'];
                                    $array = explode('@',$QuestionBankRepositories->ArrToStr($data));
                                    $v['strName'] = $array[0];
                                    $v['question_type_name'] = $questionType->where('id','=',$v['question_type'])->pluck('name');

                                    $item[] = $v;
                                }
                                $paperDetail['item'] = $item;
                            }



                        }

                    }
                    
                    return view('osce::admin.resourcemanage.subject_papers_add',[
                        'label'=>$label,
                        'ExamQuestionLabelTypeList'=>$question,
                        'paperDetail' => $paperDetail,
                    ]);
                }
            }




        }else{
            return view('osce::admin.resourcemanage.subject_papers_add',[
                'label'=>$label,
                'ExamQuestionLabelTypeList'=>$question,
            ]);
        }


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
        //dd($request->all());
        //验证试题类型ID
        $this->validate($request,[
            'subject_id'        => 'sometimes|integer',
            'ability_id'        => 'sometimes|integer',
            'difficult_id'        => 'sometimes|integer',
            'question_type'        => 'required|integer',
        ]);

        //接收筛选参数
        $data = [];
        if(intval($request -> subject_id) !== 0){
            array_push($data,intval($request -> subject_id));
        }
        //dd($data);
        if(intval($request -> ability_id) !== 0){
            array_push($data,intval($request -> ability_id));
        }

        if(intval($request -> difficult_id) !== 0){
            array_push($data,intval($request -> difficult_id));
        }

        $question_type = $request->question_type;
        //根据筛选参数查找试题数据
        $ExamQuestion = new ExamQuestion();

        $pageIndex = $request->page?$request->page:1;//获取页码

        $questions = $ExamQuestion -> getExamQuestion($data,$pageIndex,$question_type)->toArray();
        //dd($questions);
        foreach($questions['data'] as $k=>$v){
            $label = '';
            $questions['data'][$k]['question_name'] = $v['name'];
            $questions['data'][$k]['question_id'] = $v['id'];
            $questions['data'][$k]['questtion_type'] = $v['tname'];
            if($v['exam_question_label_relation']){
                foreach(@$v['exam_question_label_relation'] as $kk=>$vv){

                    if($kk <= 3){
                        $label .= $vv['exam_question_label']['name'].',';
                    }

                }
                $questions['data'][$k]['label'] = trim($label,',');
            }

            //continue;
        }
        //重新定义数组，方便排序
        $newQuestions = array();
        $newQuestions = $questions;
        $newQuestions['data'] = array();
       // dd($request->questionArr);
        if($request->questionArr){
            foreach($questions['data'] as $key=>$qq){

                if(in_array($qq['id'],$request->questionArr)){

                    array_unshift($newQuestions['data'],$questions['data'][$key]);
                    //dd($newQuestions['data']);
                }else{
                    array_push($newQuestions['data'],$questions['data'][$key]);
                }
               // var_dump($request->questionArr);
                //dd($newQuestions);
            }
            $questions = null;
            $questions = $newQuestions;
        }

        if($questions){

            return $this->success_data($questions);
        }else{
            return $this->success_data('',0,'error');
        }
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
    public function getAddExams(Request $request,QuestionBankRepositories $QuestionBankRepositories){
       //dd($request->all());
        //验证试题类型ID
        $this->validate($request,[
            'name'        => 'required',
            'time'        => 'required',
            'status'        => 'required|integer',
            'status2'        => 'required',
            'question'        => 'sometimes',
        ]);
        $DB = \DB::connection('osce_mis');
        $DB->beginTransaction();

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

        //判断当前数据是否存在
        $check = ExamPaper::where($data)->first();
        if($check){
            $DB->rollBack();
            return redirect()->back()->withInput()->withErrors('试卷已存在');
        }
        //向试卷表插入基础数据
        $examPaper = ExamPaper::create($data);
        if(!$examPaper){
            $DB->rollBack();
            return false;
        }

        $examPaperID = $examPaper->id;

        $questions = $request->question;//获取标签参数
        if($questions){
            $examPapers = [];
            foreach($questions as $v){
                $examPapers[] = $QuestionBankRepositories->StrToArr($v);//字符串转换为数组
            }
        }

        if($status == 1 && $status2 == 1){//自动-随机
            //新增试卷-试卷构造表和标签类型关联数据添加
            $result = $this->addData($examPapers,$examPaperID,$QuestionBankRepositories);
            if(!$result){
                $DB->rollBack();
                return redirect()->back()->withInput()->withErrors('系统异常');
            }

        }elseif($status == 1 && $status2 == 2){//自动-统一

            //新增试卷-试卷构造表和标签类型关联数据添加
            $ExamPaperStructure = new ExamPaperStructure();
            $ExamPaperStructureLabel = new ExamPaperStructureLabel();

            foreach($examPapers as $exam){
                //拼合试卷构造表数据
                $papers = [
                    'exam_paper_id' => $examPaperID,
                    'exam_question_type_id' => $exam['type'],
                    'num' => $exam['num'],
                    'score' => $exam['score'],
                    'total_score' => $exam['total_score'],
                    'created_user_id' => $user->id,
                ];

                $addExamPaperStructure = $ExamPaperStructure->create($papers);
                if(!$addExamPaperStructure){
                    DB::rollback();
                    return false;exit;
                }

                foreach($exam['structure_label'] as $structure_label){
                    //拼合试卷构造表和试题标签表关联的数据
                    $structure_label['exam_paper_structure_id'] = $addExamPaperStructure->id;
                    $structure_label['created_user_id'] = $user->id;

                    $addExamPaperStructureLabel = $ExamPaperStructureLabel->create($structure_label);
                    if(!$addExamPaperStructureLabel){
                        DB::rollback();
                        return false;exit;
                    }
                }

            }


            //查找筛选条件下的试题
            $examQuestion = $QuestionBankRepositories->StructureExamQuestionArr($examPapers);

            //整理数据
            foreach($examQuestion as $k=>$v){
                $examQuestion[$k]['created_user_id'] = $user->id;
                $examQuestion[$k]['exam_paper_id'] = $examPaperID;
                $questionType = $this->checkQuestions($v['type']);

                if(!count($v['child'])){
                    $DB->rollBack();
                    return redirect()->back()->withInput()->withErrors('没有'.$questionType.'类型的试题！');
                }
            }

            //保存数据
            foreach($examQuestion as $kk=>$vv){
                    foreach($vv['child'] as $key=>$val){
                        $arrs = [
                            'exam_paper_structure_id' => $addExamPaperStructure->id,
                            'exam_question_id' => $val,
                        ];
                        $addPaperStructureQuestion = ExamPaperStructureQuestion::create($arrs);

                        if(!$addPaperStructureQuestion){
                            $DB->rollBack();
                            return redirect()->back()->withInput()->withErrors('系统异常');
                        }
                    }

            }
            //exit;
        }elseif($status == 2 && $status2 == 2){//手动-统一
            //分割字符串-拼合数组
            $questions = Input::get('question-type');
            foreach($questions as $k=>$v){
                $type[] = explode('@',$v);
            }
            foreach($type as $kk=>$vv){
                $questionsID = explode(',',$vv[2]);
                $structure['exam_paper_id'] = $examPaperID;
                $structure['exam_question_type_id'] = $vv[0];
                $structure['score'] = $vv[1];
                $structure['num'] = count($questionsID);
                $structure['total_score'] = count(explode(',',$vv[2])) * $vv[1];
                $structure['created_user_id'] = $user->id;
                $addPaperStructure = ExamPaperStructure::create($structure);
                if(!$addPaperStructure){
                    $DB->rollBack();
                    return redirect()->back()->withInput()->withErrors('系统异常');
                }else{
                    foreach($questionsID as $val){
                        $structure_question['exam_paper_structure_id'] = $addPaperStructure->id;
                        $structure_question['exam_question_id'] = $val;
                        $addStructureQuestion = ExamPaperStructureQuestion::create($structure_question);
                        if(!$addStructureQuestion){
                            $DB->rollBack();
                            return redirect()->back()->withInput()->withErrors('系统异常');
                        }
                    }
                }
            }
        }

        $DB->commit();
        return redirect()->back()->withInput()->withErrors('操作成功');

    }
    /**
     * 试卷修改
     * @access    public
     * @param Request $request get请求<br><br>
     * @param Exam $exam
     * @return view
     * @throws \Exception
     * @version   1.0
     * @author    weihuiguo <weihuiguo@misrobot.com>
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function getEditExamPaper(Request $request,QuestionBankRepositories $QuestionBankRepositories){
        //dd($request->all());
        //验证试题类型ID
        $this->validate($request,[
            'name'        => 'required',
            'time'        => 'required',
            'status'      => 'required|integer',
            'status2'     => 'required',
            'question'    => 'sometimes',
            'paperid'     => 'required|integer',
        ]);
        $DB = \DB::connection('osce_mis');
        $DB->beginTransaction();
        //接收试卷ID
        $examPaperID = $request->paperid;

        $user = Auth::user();
        //接收参数
        $data = [
            'name'            => $request -> name,
            'length'          => $request -> time,
            'mode'            => $request -> status,
            'type'            => $request -> status2,
            'created_user_id' => $user->id,
            'updated_at'      => date('Y-m-d H:i:s'),
        ];

        //获取试卷类型
        $status = $request -> status;
        $status2 = $request -> status2;

        //修改时判断当前数据是否存在
        $check = ExamPaper::where('id','!=',$examPaperID)->where($data)->first();
        if($check){
            $DB->rollBack();
            return redirect()->back()->withInput()->withErrors('试卷已存在');
        }
        //修改基础数据
        $examPaper = ExamPaper::where('id','=',$examPaperID)->update($data);
        if(!$examPaper){
            $DB->rollBack();
            return false;
        }

        $questions = $request->question;//获取标签参数
        if($questions){
            $examPapers = [];
            foreach($questions as $v){
                $examPapers[] = $QuestionBankRepositories->StrToArr($v);//字符串转换为数组
            }
        }
        //dd($examPapers);
        if($status == 1 && $status2 == 1){//自动-随机
            //新增试卷-试卷构造表和标签类型关联数据添加
            $result = $this->editData($examPapers,$examPaperID,$QuestionBankRepositories);
            if(!$result){
                $DB->rollBack();
                return redirect()->back()->withInput()->withErrors('系统异常');
            }

        }elseif($status == 1 && $status2 == 2){//自动-统一

            //新增试卷-试卷构造表和标签类型关联数据添加
            $result = $this->editData($examPapers,$examPaperID,$QuestionBankRepositories);
            if(!$result){
                $DB->rollBack();
                return redirect()->back()->withInput()->withErrors('系统异常');
            }
            //查找筛选条件下的试题
            $examQuestion = $QuestionBankRepositories->StructureExamQuestionArr($examPapers);

            //整理数据
            foreach($examQuestion as $k=>$v){
                $examQuestion[$k]['created_user_id'] = $user->id;
                $examQuestion[$k]['exam_paper_id'] = $examPaperID;
                $questionType = $this->checkQuestions($v['type']);

                if(!count($v['child'])){
                    $DB->rollBack();
                    return redirect()->back()->withInput()->withErrors('没有'.$questionType.'类型的试题！');
                }
            }

            //保存数据
            foreach($examQuestion as $kk=>$vv){
                $addPaperStructure = ExamPaperStructure::create($examQuestion);
                if(!$addPaperStructure){
                    $DB->rollBack();
                    return redirect()->back()->withInput()->withErrors('系统异常');
                }else{
                    foreach($vv['child'] as $key=>$val){
                        $arrs = [
                            'exam_paper_structure_id' => $addPaperStructure->id,
                            'exam_question_id' => $val,
                        ];
                        $addPaperStructureQuestion = ExamPaperStructureQuestion::create($arrs);
                        if(!$addPaperStructureQuestion){
                            $DB->rollBack();
                            return redirect()->back()->withInput()->withErrors('系统异常');
                        }
                    }
                }
            }

        }elseif($status == 2 && $status2 == 2){//手动-统一
            //分割字符串-拼合数组
            $questions = Input::get('question-type');
            foreach($questions as $k=>$v){
                $type[] = explode('@',$v);
            }
            foreach($type as $kk=>$vv){
                $questionsID = explode(',',$vv[2]);
                $structure['exam_question_type_id'] = $vv[0];
                $structure['score']                 = $vv[1];
                $structure['num']                   = count($questionsID);
                $structure['total_score']           = count(explode(',',$vv[2])) * $vv[1];
                $structure['created_user_id']       = $user->id;
                $structure['updated_at']            = date('Y-m-d H:i:s');
                //修改数据
                $addPaperStructure = ExamPaperStructure::where('id','=',$vv[3])->update($structure);
                if(!$addPaperStructure){
                    $DB->rollBack();
                    return redirect()->back()->withInput()->withErrors('系统异常');
                }else{
                    if(!ExamPaperStructureQuestion::where('exam_paper_structure_id','=',$vv[3])->delete()){
                        $DB->rollBack();
                        return redirect()->back()->withInput()->withErrors('系统异常');
                    }
                    foreach($questionsID as $val){
                        $structure_question['exam_paper_structure_id'] = $vv[3];
                        $structure_question['exam_question_id'] = $val;
                        $addStructureQuestion = ExamPaperStructureQuestion::create($structure_question);
                        if(!$addStructureQuestion){
                            $DB->rollBack();
                            return redirect()->back()->withInput()->withErrors('系统异常');
                        }
                    }

                }
            }
        }

        $DB->commit();
        return redirect()->back()->withInput()->withErrors('操作成功');
    }

    //判断试题类型
    public function checkQuestions($type){
        $question = ExamQuestionType::where('id','=',$type)->pluck('name');
        return $question;
    }
    /**
     * 新增试卷-试卷构造表和标签类型关联数据添加
     * @access    public
     * @param Request $request get请求<br><br>
     * @param Exam $exam
     * @return view
     * @throws \Exception
     * @version   1.0
     * @author    weihuiguo <weihuiguo@misrobot.com>
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function addData($examPapers,$examPaperID,$QuestionBankRepositories){
        $user = Auth::user();
        $ExamPaperStructure = new ExamPaperStructure();
        $ExamPaperStructureLabel = new ExamPaperStructureLabel();

        foreach($examPapers as $exam){
            //拼合试卷构造表数据
            $papers = [
                'exam_paper_id' => $examPaperID,
                'exam_question_type_id' => $exam['type'],
                'num' => $exam['num'],
                'score' => $exam['score'],
                'total_score' => $exam['total_score'],
                'created_user_id' => $user->id,
            ];

            $addExamPaperStructure = $ExamPaperStructure->create($papers);
            if(!$addExamPaperStructure){
                DB::rollback();
                return false;exit;
            }

            foreach($exam['structure_label'] as $structure_label){
                //拼合试卷构造表和试题标签表关联的数据
                $structure_label['exam_paper_structure_id'] = $addExamPaperStructure->id;
                $structure_label['created_user_id'] = $user->id;

                $addExamPaperStructureLabel = $ExamPaperStructureLabel->create($structure_label);
                if(!$addExamPaperStructureLabel){
                    DB::rollback();
                    return false;exit;
                }
            }

        }
        return true;
    }


    /**
     * 修改试卷-试卷构造表和标签类型关联数据添加
     * @access    public
     * @param Request $request get请求<br><br>
     * @param Exam $exam
     * @return view
     * @throws \Exception
     * @version   1.0
     * @author    weihuiguo <weihuiguo@misrobot.com>
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function editData($examPapers,$examPaperID,$QuestionBankRepositories){
        $user = Auth::user();
        $ExamPaperStructure = new ExamPaperStructure();
        $ExamPaperStructureLabel = new ExamPaperStructureLabel();

        DB::beginTransaction();

        foreach($examPapers as $exam){
            //拼合试卷构造表数据
            $papers = [
                'exam_paper_id' => $examPaperID,
                'exam_question_type_id' => $exam['type'],
                'num' => $exam['num'],
                'score' => $exam['score'],
                'total_score' => $exam['total_score'],
                'created_user_id' => $user->id,
            ];


            $addExamPaperStructure = $ExamPaperStructure->where('id','=',$exam['structureid'])->update($papers);
            if(!$addExamPaperStructure){
                DB::rollback();
                return false;exit;
            }

            if(!$ExamPaperStructureLabel->where('exam_paper_structure_id','=',$exam['structureid'])->delete()){
                DB::rollback();
                return false;exit;
            }
            foreach($exam['structure_label'] as $structure_label){
                //拼合试卷构造表和试题标签表关联的数据
                $structure_label['created_user_id'] = $user->id;
                $structure_label['exam_paper_structure_id'] = $exam['structureid'];
                $structure_label['updated_at'] = date('Y-m-d H:i:s');
                $addExamPaperStructureLabel = $ExamPaperStructureLabel->create($structure_label);
                if(!$addExamPaperStructureLabel){
                    DB::rollback();
                    return false;exit;
                }
            }

        }
        DB::commit();
        return true;
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
    public function getExampQuestions(Request $request){
        if($request->question_detail){
            $questionIDstr = explode('@',$request->question_detail);
            if(count($questionIDstr) > 2){
                $questionIDs = $questionIDstr[2];
            }else{
                $questionIDs = '';
            }

        }
        $label = $this->getExamLabelGet();//标签
        if($request->question_detail){
            $type = explode('@',$request->question_detail);
        }
       // dd($request->all());
        return view('osce::admin.resourcemanage.subject_papers_add_detail2',[
            'labelList'=>$label,
            'question_type'=>$type[0],
            'sequence'=>$request->sequence,
            'question_detail' => $request->question_detail,
            'questionIDs' => $questionIDs,
            'labelList'=>$label,
        ]);
    }

    /**
     * 获取试题标签id
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
    public function GetExamQuestionLabelId($obj){
        $IdArr = [];
        if(count($obj)>0){

            foreach($obj as $k => $v){
                $IdArr = array_merge($IdArr,collect($v)->pluck('exam_question_label_id')->toArray());
            }

            return  $IdArr;
        }else{
            return  $IdArr;
        }

    }

     /**
     * 获取试题标签id
     * @url       POST /osce/admin/exampaper/check-name-only
     * @access    public
     * @param Request $request get请求<br><br>
     * @param Exam $exam
     * @return view
     * @throws \Exception
     * @version   1.0
     * @author    weihuiguo <weihuiguo@misrobot.com>
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function postCheckNameOnly(Request $request){
        //dd('验证');
        $this->validate($request,[
            'name'   => 'required',
        ]);

        $ExamQuestionLabelType = new ExamPaper();
        if(!empty($request['id'])){
            $ExamQuestionLabelTypeList = $ExamQuestionLabelType->where('name','=',$request['name'])->where('id','<>',$request['id'])->first();
        }else{
            $ExamQuestionLabelTypeList = $ExamQuestionLabelType->where('name','=',$request['name'])->first();
        }
        if(!empty($ExamQuestionLabelTypeList->id)){
            die(json_encode(['valid'=>false]));
        }else{
            die(json_encode(['valid'=>true]));
        }
    }
}
