<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/3/10 0010
 * Time: 14:05
 */

namespace Modules\Osce\Http\Controllers\Admin\Branch;


use Modules\Osce\Entities\QuestionBankEntities\Answer;
use Modules\Osce\Entities\QuestionBankEntities\ExamPaperFormal;
use Modules\Osce\Http\Controllers\CommonController;
use Modules\Osce\Repositories\QuestionBankRepositories;
use Illuminate\Http\Request;



/**考试答题控制器
 * Class Answer
 * @package Modules\Osce\Http\Controllers\Admin\Branch
 */

class AnswerController extends CommonController
{
    /**正式试卷信息数据
     * @method
     * @url /osce/
     * @access public
     * @author xumin <xumin@misrobot.com>
     * @date
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function formalPaperList(Request $request,QuestionBankRepositories $questionBankRepositories)
    {
        $ExamPaperId = $request->input('id');//试卷id
        $stationId = $request->input('stationId');//考站id
        $userId = $request->input('userId');//老师id
        $studentId = $request->input('studentId');//学生id
        //获取试卷信息
        $ExamPaperInfo = $questionBankRepositories->GenerateExamPaper($ExamPaperId);

        $ExamPaperFormal = new ExamPaperFormal;
        //生成正式的试卷并且 返回id
        $ExamPaperFormalId = $ExamPaperFormal->CreateExamPaper($ExamPaperInfo);
        //将开始时间存入session中
        if(\Session::get('systemTimeStart')){
            $systemTimeStart =\Session::get('systemTimeStart');
        }else{
            $systemTimeStart=time();
            \Session::put('systemTimeStart',$systemTimeStart);
        }

        //获取正式试卷表信息
        $examPaperFormalModel = new ExamPaperFormal();
        $examPaperFormalList = $examPaperFormalModel->where('id','=',$ExamPaperFormalId)->first();
        $examPaperFormalData ='';
        $systemTimeEnd =0;
        if($examPaperFormalList) {
            $examPaperFormalData = array(
                'id' => $examPaperFormalList->id,//正式试卷id
                'name' => $examPaperFormalList->name,//正式试卷名称
                'length' => $examPaperFormalList->length,//正式试卷考试时间
                'totalScore' => $examPaperFormalList->total_score,//正式试卷总分
            );
          $systemTimeEnd =$systemTimeStart+$examPaperFormalData['length']*60; //结束时间
        }
        $examCategoryFormalData='';//正式试题信息(根据试题类型进行分类)
        if($examPaperFormalList){
            $examCategoryFormalList = $examPaperFormalList->examCategoryFormal;//获取正式试题分类信息
            if($examCategoryFormalList){
                foreach($examCategoryFormalList as $key=>$val){
                    if($val->ExamQuestionFormal){
                        $examCategoryFormalList[$key]['exam_question_formal'] = $val->ExamQuestionFormal;//获取正式试题信息
                    }
                }
                //转换为数组格式
                foreach($examCategoryFormalList as $k1=>$v1){
                    if(count($v1['exam_question_formal'])>0){
                        foreach($v1['exam_question_formal'] as $k2=>$v2){
                            $examCategoryFormalData[]=array(
                                'id' =>$v2->id,//正式试题信息
                                'name' =>($k2+1).'、'.$v2->name,
                                'exam_question_id' =>$v2->exam_question_id,
                                'content' =>explode('|%|',$v2->content),
                                'answer' =>$v2->answer,
                                'parsing' =>$v2->parsing,
                                'exam_category_formal_id' =>$v2->exam_category_formal_id,
                                'student_answer' =>$v2->student_answer,
                                'serialNumber' =>($k1+1).'.'.($k2+1),

                                'examCategoryFormalId'=>$v1->id,//正式试题分类信息
                                'examCategoryFormalName'=>$v1->name,
                                'examCategoryFormalNumber'=>$v1->number,
                                'examCategoryFormalScore'=>$v1->score,
                                'examQuestionTypeId'=>$v1->exam_question_type_id,
                                'exam_paper_formal_id'=>$v1->exam_paper_formal_id,
                                );
                        }
                    }
                }
            }
        }


        if(count($examCategoryFormalData)>0&&!empty($examCategoryFormalData)){
            foreach($examCategoryFormalData as $key=>$val){
                if($val['examQuestionTypeId']==1){//单选
                    $examCategoryFormalData[$key]['examCategoryFormalName']='一、'.$val['examCategoryFormalName'];
                }elseif($val['examQuestionTypeId']==2){//多选
                    $examCategoryFormalData[$key]['examCategoryFormalName']='二、'.$val['examCategoryFormalName'];
                }elseif($val['examQuestionTypeId']==3){//不定向
                    $examCategoryFormalData[$key]['examCategoryFormalName']='三、'.$val['examCategoryFormalName'];
                }elseif($val['examQuestionTypeId']==4){//判断
                    $examCategoryFormalData[$key]['examCategoryFormalName']='四、'.$val['examCategoryFormalName'];
                    $examCategoryFormalData[$key]['content']=array('0'=>0,'1'=>1);
                }

            }
        }
        //dd($examCategoryFormalData);
        return view('osce::admin.theoryCheck.theory_check', [
            'examCategoryFormalData'      =>$examCategoryFormalData,//正式试题信息
            'examPaperFormalData'         =>$examPaperFormalData,//正式试卷信息
            'systemTimeStart'              =>date('Y/m/d H:i:s',$systemTimeStart),//开始时间
            'systemTimeEnd'                =>date('Y/m/d H:i:s',$systemTimeEnd),//结束时间
            'stationId'                    => $stationId,//考站id
            'userId'                       => $userId,//老师id
            'studentId'                       =>$studentId,//学生id
        ]);
    }
    /**保存考生答案
     * @method
     * @url /osce/
     * @access public
     * @param Request $request
     * @author xumin <xumin@misrobot.com>
     * @date
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function postSaveAnswer(Request $request)
    {
        $systemTimeStart = \Session::get('systemTimeStart');//取出存入的系统开始时间
        $actualLength = (time()-$systemTimeStart)/60;//考试用时
        $data =array(
            'examPaperFormalId' =>$request->input('examPaperFormalId'), //正式试卷id
            'actualLength' =>sprintf("%.2f",$actualLength), //考试用时
            'examQuestionFormalInfo'=>$request->input('examQuestionFormalInfo'),//正式试题信息
        );

 /*       //提交过来的数据格式
        $case = array(
            'examPaperFormalId'=>'1',//试卷id
            'examQuestionFormalInfo'=>array(
                '0'=>array('exam_question_id'=>1,'examCategoryFormalId'=>1,'answer'=>'0'),//试题id，试题类型，考生答案
                '1'=>array('exam_question_id'=>2,'examCategoryFormalId'=>2,'answer'=>'0@1@3'),
                '2'=>array('exam_question_id'=>3,'examCategoryFormalId'=>3,'answer'=>'1@2'),
                '3'=>array('exam_question_id'=>4,'examCategoryFormalId'=>4,'answer'=>'1'),
            )
        );*/
        if(!empty($data['examQuestionFormalInfo']) && count($data['examQuestionFormalInfo'])>0 ){
            foreach($data['examQuestionFormalInfo'] as $k=>$v){
                $newStudentAnswer='';
                $studentAnswer = explode('@',$v['answer']);
                foreach($studentAnswer as $val){
                    if($v['examCategoryFormalId']=='4'){//判断题
                        $newStudentAnswer = $val;
                    }else{
                        if($val=='0'){
                            if($newStudentAnswer){
                                $newStudentAnswer.='@A';
                            }else{
                                $newStudentAnswer ='A';
                            }
                        }elseif($val=='1'){
                            if($newStudentAnswer){
                                $newStudentAnswer.='@B';
                            }else{
                                $newStudentAnswer ='B';
                            }
                        }elseif($val=='2'){
                            if($newStudentAnswer){
                                $newStudentAnswer.='@C';
                            }else{
                                $newStudentAnswer ='C';
                            }
                        }elseif($val=='3'){
                            if($newStudentAnswer){
                                $newStudentAnswer.='@D';
                            }else{
                                $newStudentAnswer ='D';
                            }
                        }elseif($val=='4'){
                            if($newStudentAnswer){
                                $newStudentAnswer.='@E';
                            }else{
                                $newStudentAnswer ='E';
                            }
                        }elseif($val=='5'){
                            if($newStudentAnswer){
                                $newStudentAnswer.='@F';
                            }else{
                                $newStudentAnswer ='F';
                            }
                        }
                    }
                }

                $data['examQuestionFormalInfo'][$k]['answer']=$newStudentAnswer;
            }
        }


        //保存考生答案
        $answerModel = new Answer();
        $result = $answerModel->saveAnswer($data);
        if($result){
            //删除session
            \Session::forget('systemTimeStart');
            return response()->json(['status'=>'2','info'=>'保存成功']);
        }else{
            return response()->json(['status'=>'3','info'=>'保存失败']);
        }
    }
    /**查询该考生理论考试成绩及该场考试相关信息
     * @method
     * @url /osce/
     * @access public
     * @param $examPaperFormalId
     * @return \Illuminate\View\View
     * @author xumin <xumin@misrobot.com>
     * @date
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function selectGrade(Request $request)
    {
        $this->validate($request, [
             'examPaperFormalId'=>'required|integer',//正式的试卷id

        ]);
        $examPaperFormalId =$request->input('examPaperFormalId'); //正式的试卷表id
        $answerModel = new Answer();
        //保存成功，调用查询该考生成绩的方法
        $examPaperFormalData = $answerModel->selectGrade($examPaperFormalId);
        //dd($examPaperFormalData);
        return view('osce::admin.theoryCheck.theory_check_complete', [
            'data'  =>$examPaperFormalData,//考试成绩及该考试相关信息
        ]);
    }





}