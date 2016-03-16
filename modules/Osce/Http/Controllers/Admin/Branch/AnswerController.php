<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/3/10 0010
 * Time: 14:05
 */

namespace Modules\Osce\Http\Controllers\Admin\Branch;


use Modules\Osce\Entities\QuestionBankEntities\Answer;
use Modules\Osce\Entities\QuestionBankEntities\ExamCategoryFormal;
use Modules\Osce\Entities\QuestionBankEntities\ExamPaperFormal;
use Modules\Osce\Entities\QuestionBankEntities\ExamQuestionFormal;
use Modules\Osce\Http\Controllers\CommonController;
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
    public function formalPaperList()
    {

        \Session::put('systemTimeStart',time());//存入当前系统时间
        //获取该理论考试相关信息
        $id = 1;//正式的试卷表ID
        //获取正式试卷表信息
        $examPaperFormalModel = new ExamPaperFormal();
        $examPaperFormalList = $examPaperFormalModel->where('id','=',$id)->first();
        $examPaperFormalData ='';
        if($examPaperFormalList) {
            $examPaperFormalData = array(
                'id' => $examPaperFormalList->id,//正式试卷id
                'name' => $examPaperFormalList->name,//正式试卷名称
                'length' => $examPaperFormalList->length,//正式试卷考试时间
                'totalScore' => $examPaperFormalList->total_score,//正式试卷总分
            );
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
                    $examCategoryFormalData[$k1]= array(
                        'id'=>$v1->id,
                        'number'=>$v1->number,
                        'score'=>$v1->score,
                        'exam_question_type_id'=>$v1->exam_question_type_id,
                        'exam_paper_formal_id'=>$v1->exam_paper_formal_id,
                    );
                    if($k1+1==1){
                        $examCategoryFormalData[$k1]['name']='一、'.$v1->name.'（共'.$v1->number.'题，每题'.$v1->score.'分）';
                    }elseif($k1+1==2){
                        $examCategoryFormalData[$k1]['name']='二、'.$v1->name.'（共'.$v1->number.'题，每题'.$v1->score.'分）';
                    }elseif($k1+1==3){
                        $examCategoryFormalData[$k1]['name']='三、'.$v1->name.'（共'.$v1->number.'题，每题'.$v1->score.'分）';
                    }elseif($k1+1==4){
                        $examCategoryFormalData[$k1]['name']='四、'.$v1->name.'（共'.$v1->number.'题，每题'.$v1->score.'分）';
                    }elseif($k1+1==5){
                        $examCategoryFormalData[$k1]['name']='五、'.$v1->name.'（共'.$v1->number.'题，每题'.$v1->score.'分）';
                    }elseif($k1+1==6){
                        $examCategoryFormalData[$k1]['name']='六、'.$v1->name.'（共'.$v1->number.'题，每题'.$v1->score.'分）';
                    }
                    if(count($v1['exam_question_formal'])>0){
                        foreach($v1['exam_question_formal'] as $k2=>$v2){
                            $examCategoryFormalData[$k1]['exam_question_formal'][$k2]=array(
                                'id' =>$v2->id,
                                'name' =>($k2+1).'、'.$v2->name,
                                'exam_question_id' =>$v2->exam_question_id,
                                'content' =>explode(',',$v2->content),
                                'answer' =>$v2->answer,
                                'parsing' =>$v2->parsing,
                                'exam_category_formal_id' =>$v2->exam_category_formal_id,
                                'student_answer' =>$v2->student_answer
                            ,                            );
                            $serialNumber[]=($k1+1).'.'.($k2+1);//序列号
                        }
                    }else{
                        $examCategoryFormalData[$k1]['exam_question_formal']='';
                    }
                }
                $examCategoryFormalData['serialNumber'] = $serialNumber;

            }
        }
        //dd($examCategoryFormalData);
        //dd($examPaperFormalData);
        return view('osce::admin.theoryCheck.theory_check', [
            'examCategoryFormalData'      =>$examCategoryFormalData,//正式试题信息
            'examPaperFormalData'          =>$examPaperFormalData,//正式试卷信息
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
        //设置时间周期为不超过两分钟
        if(time()-$systemTimeStart>120){
            return response()->json(['status'=>'1','info'=>'超时']);
        }

        $this->validate($request, [
           // 'examPaperFormalId'=>'required|integer',//正式的试卷表id
           // 'examQuestionFormalId'=>'required|integer',//正式的试题表id
            'studentAnswer'        => 'sometimes|array',
        ]);
        $data =array(
            'id' =>$request->input('examQuestionFormalId'), //正式试题id
            'student_answer' =>$request->input('studentAnswer'), //考生答案
        );
        //$examPaperFormalId =$request->input('examPaperFormalId'); //正式的试卷表id
        $examPaperFormalId =1; //正式的试卷表id
        $answerModel = new Answer();
        //提交过来的数据格式
        $case = array(
            '0'=>array('examQuestionFormalId'=>1,'studentAnswer'=>'B'),
            '1'=>array('examQuestionFormalId'=>2,'studentAnswer'=>'B@C@D'),
            '2'=>array('examQuestionFormalId'=>3,'studentAnswer'=>'B@C'),
            '3'=>array('examQuestionFormalId'=>4,'studentAnswer'=>'1'),
        );
        //保存考生答案
        //$result = $answerModel->saveAnswer($data);
        $result=true;
        if($result){
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
    public function selectGrade($examPaperFormalId=1)
    {
        $answerModel = new Answer();
        //保存成功，调用查询该考生成绩的方法
        $examPaperFormalData = $answerModel->selectGrade($examPaperFormalId);
        //dd($examPaperFormalData);
        return view('osce::admin.theoryCheck.theory_check_complete', [
            'data'                         =>$examPaperFormalData,//考试成绩及该考试相关信息
        ]);
    }



}