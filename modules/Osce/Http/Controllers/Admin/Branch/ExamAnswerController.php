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
 * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
 */
class ExamAnswerController extends CommonController
{
    //查询答案所需数据
    public function  getStudentAnswer(Request $request)
    {

        $id = Input::get('id', '');
        $examPaperFormal = new ExamPaperFormal();
        $examItems = [];
        $child = [];
        $data = [];
        $stuScore = 0;
        $examPaperFormalInfo = $examPaperFormal->where('id', '=', 35)->first();
        // dd($examPaperFormalInfo);
       // dd($examPaperFormalInfo->ExamCategoryFormal);
        $examItems['exam_name'] = $examPaperFormalInfo['name']; //试题名称
        $examItems['length'] = $examPaperFormalInfo['length'];  //考试时长
        $examItems['total_score'] = $examPaperFormalInfo['total_score']; //试卷总分
        $examItems['actual_length'] = $examPaperFormalInfo['actual_length'];//考试使用时长*/
        // dd($examItems);
        //dd($this->arr_foreach($data));
        if (count($examPaperFormalInfo->ExamCategoryFormal) > 0) {
            foreach ($examPaperFormalInfo->ExamCategoryFormal as $k => $v) {
                if (count($v->ExamQuestionFormal) > 0) {
                    //dd($v->ExamQuestionFormal);
                    foreach ($v->ExamQuestionFormal as $key => $item) {

                        $child[$key]['exam_question_name'] = $key + 1 . '、' . '' . $item['name'] . '( ' . ' )'; // 拼接试题名称
                        $child[$key]['contentItem'] = explode('|%|', $item['content']); //试题内容（A.内容，B.内容，C.内容）用,拼接试题内容
                         //对多选题的正确答案进行拆分
                        if(strstr($item['answer'],'@')) {
                            $child[$key]['answer'] = explode('@',$item['answer']); //试题答案（a/abc/0,1）
                        }else{
                            $child[$key]['answer'] = $item['answer']; //试题答案（a/abc/0,1）
                        }
                        //对多选题的学生答案进行拆分
                        if(strstr($item['student_answer'],'@')){
                            $studentAnswer=explode('@',$item['student_answer']);
                        }else{
                            $studentAnswer=$item['student_answer'];
                        }

                        $child[$key]['parsing'] = '解析：' . $item['parsing']; //题目答案解析拼接
                        $answerArr = ['0' => '错误', '1' => '正确']; //1,0 与 正确 错误之间的显示转换

                        if (!empty($item['student_answer']) && !empty($item['answer'])) { // $v['exam_question_type_id'] = 4 这可以在第一个循环中进行判断，这里已经是第二个循环了，无法作为判断条件
                            if ($item['answer'] == '1' || $item['answer'] == '0') {  //填写的答案是个字符串，无法用is_int 或 $item['answer'] == 1 进行判断

                                $child[$key]['student_answer'] = '考生答案：' . $answerArr[$studentAnswer]. '(' . $answerArr[$item['answer']]  . ')'; // 学生答案拼接
                            } else {
                                $child[$key]['student_answer'] = '考生答案：' . $studentAnswer . '(' . $item['answer'] . ')'; // 学生答案
                            }
                        }else{

                            if ($item['answer'] == '1' || $item['answer'] == '0') {

                                $child[$key]['student_answer'] = '考生答案：' . '没有作答'. '(' . $answerArr[$item['answer']]  . ')'; // 学生答案
                            } else {
                                $child[$key]['student_answer'] = '考生答案：' .$studentAnswer . '(' . $item['answer'] . ')'; // 学生答案
                            }


                        }
                        if (!empty($item['answer']) && $item['answer'] == $item['student_answer']) {
                            $stuScore += $v['score'];
                        }
                    }
                }
                $arr = ['0' => '一', '1' => '二', '2' => '三', '3' => '四', '4' => '五', '5' => '六', '6' => '七', '7' => '八', '8' => '九', '9' => '十'];
                $data[$k]['Title'] = $arr[$k] . '、' . $v['name'] . ' (' . '共' . $v['number'] . '题，' . '每题' . $v['score'] . '分' . ')';
                /* $v['name']; //试题类型名称
                  $v['number']; //试题数量
                  $v['score'];  //单个试题的分值*/
                $examItems['stuScore'] = $stuScore;//考试最终成绩
                $data[$k]['child'] = $child;
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
