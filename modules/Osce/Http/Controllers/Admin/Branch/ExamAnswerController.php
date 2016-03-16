<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/3/10 0010
 * Time: 15:48
 */
namespace Modules\Osce\Http\Controllers\Admin\Branch;
use Modules\Osce\Http\Controllers\CommonController;
use Modules\Osce\Entities\QuestionBankEntities\ExamPaperFormal;
use Modules\Osce\Entities\QuestionBankEntities\ExamCategoryFormal;
use Illuminate\Support\Facades\Input;
use Illuminate\Http\Request;

/**
 * 新增试卷标签
 * @method  GET
 * @url   /osce/admin/answer/student-answer
 * @access public
 * @param
 * @author yangshaolin <yangshaolin@misrobot.com>
 * @date    2016年3月16日09:48:25
 * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved*/
class ExamAnswerController extends CommonController
{
    //查询答案所需数据
    public function  getStudentAnswer(Request $request)
    {

        $id = Input::get('id', '');
        $examPaperFormal = new ExamPaperFormal();
        $examItems=[];
        $child=[];
        $data=[];
        $stuScore=0;
        $examPaperFormalInfo = $examPaperFormal->where('id', '=', 35)->first();
        // dd($examPaperFormalInfo);
        //dd($examPaperFormalInfo->ExamCategoryFormal);
        $examItems['exam_name']=$examPaperFormalInfo['name']; //试题名称
        $examItems['length']=$examPaperFormalInfo['length'];  //考试时长
        $examItems['total_score']=$examPaperFormalInfo['total_score']; //试卷总分
        $examItems['actual_length']=$examPaperFormalInfo['actual_length'];//考试使用时长*/
       // dd($examItems);
        //dd($this->arr_foreach($data));
        if (count($examPaperFormalInfo->ExamCategoryFormal) > 0) {
            foreach ($examPaperFormalInfo->ExamCategoryFormal as $k=>$v) {
               // dd($v);
               if (count($v->ExamQuestionFormal) > 0) {
                  // dd($v->ExamQuestionFormal);
                    foreach ($v->ExamQuestionFormal as $key=>$item) {

                        $child[$key]['exam_question_name']=$key+1 .'、'.''.$item['name'].'( '.' )'; // 拼接试题名称
                        $child[$key]['contentItem']=explode('|%|',$item['content']); //试题内容（A.内容，B.内容，C.内容）用,拼接试题内容
                        $child[$key]['answer']=$item['answer']; //试题答案（a/abc/0,1）
                        $child[$key]['parsing']=$item['parsing']; //题目内容解析
                        $child[$key]['student_answer']=$item['student_answer']; // 学生答案*/

                        if(!empty($item['answer'])&& $item['answer']==$item['student_answer']){
                            $stuScore+=$v['score'];
                        }
                    }
                }
                $arr=['0'=>'一','1'=>'二','2'=>'三','3'=>'四','4'=>'五','5'=>'六','6'=>'七','7'=>'八','8'=>'九','9'=>'十'];
                $data[$k]['Title']=$arr[$k].'、'.$v['name'].' ('.'共'.$v['number'].'题，'.'每题'.$v['score'].'分'.')';
              /* $v['name']; //试题类型名称
                $v['number']; //试题数量
                $v['score'];  //单个试题的分值*/
                $examItems['stuScore']=$stuScore;//考试最终成绩
                $data[$k]['child']=$child;
            }
        }
        //dd($examItems);
        //dd($stuScore);
       //dd($data);
       /* return view('osce::admin.resourcemanage.subject_check_tag_add',
            [
                'examItems'=>$examItems,
                'data'=>$data
            ]);*/
        }

}
