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
use Modules\Osce\Entities\Student;
use Modules\Osce\Entities\ExamResult;




class ExamAnswerController extends CommonController
{

     //将为秒的时间转化为  XX分XX秒
     public function timeTransformation($time){

         date_default_timezone_set("UTC");
         $exam_result_time = date('H:i:s', $time);
         date_default_timezone_set("PRC");
         return $exam_result_time;

     }

    /**
     * 查询答案所需数据
     * @method  GET
     * @url   /osce/admin/answer/student-answer
     * @access public
     * @param
     * @author yangshaolin <yangshaolin@misrobot.com>
     * @date    2016年3月29日15:09:43
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function  getStudentAnswer($student_id)
    {
        
        $id = intval($student_id);//学生id

        $studentMsg=Student::where('id',$id)->first();
        if(is_null($studentMsg)){
            abort(404,'学生不存在');
        }

        $examItems = [];
        $child = [];
        $data = [];
        $stuScore = 0;
        \DB::connection('osce_mis')->enableQueryLog();
        $examPaperFormalInfo = ExamPaperFormal::where('student_id',$studentMsg->id)->first();
        $q = \DB::connection('osce_mis')->getQueryLog();
        if(is_null($examPaperFormalInfo)){
            abort(404,'试卷不存在');
        }
        $examItems['student_name'] = $studentMsg->name; //试题名称
        $examItems['exam_name'] = $examPaperFormalInfo['name']; //试题名称
        $examItems['length'] = $examPaperFormalInfo['length'];  //考试时长
        $examItems['total_score'] = $examPaperFormalInfo['total_score']; //试卷总分
        $examItems['actual_length'] = $this->timeTransformation(sprintf('%.2f',$examPaperFormalInfo['actual_length']));//考试使用时长*/

        if (count($examPaperFormalInfo->ExamCategoryFormal) > 0) {
            foreach ($examPaperFormalInfo->ExamCategoryFormal as $k => $v) {
                if (count($v->ExamQuestionFormal) > 0) {
                    //dd($v->ExamQuestionFormal);
                    foreach ($v->ExamQuestionFormal as $key => $item) {
//dump($item);
                        $child[$key]['exam_question_name'] = $key + 1 . '.' . '' . $item['name'] .'?'; // 拼接试题名称
                        $child[$key]['exam_question_image'] = unserialize($item['image']); //试题图片
                        $child[$key]['contentItem'] = explode('|%|', $item['content']); //试题内容（A.内容，B.内容，C.内容）用,拼接试题内容

                        foreach($child[$key]['contentItem'] as $kkk => $vvv){ //将$child[$key]['contentItem']中的 . 替换成冒号：

                            $child[$key]['contentItem'][$kkk] = str_replace('.', ':', $vvv);

                        }

                        $answerArr = ['0' => '错误', '1' => '正确'];    //1,0 与 正确 错误之间的显示转换

                        if ($item['answer'] == '1' || $item['answer'] == '0'){
                            $child[$key]['answer'] = $answerArr[$item['answer']];
                        } else {
                            $child[$key]['answer'] = str_replace('@', '、', $item['answer']); //将$item['answer']中的 @替换成顿号、 达到展示效果
                        }

                        //对多选题的学生答案进行拆分
                        if(strstr($item['student_answer'],'@')){

                            $studentAnswer=empty($item['student_answer'])?'未作答':str_replace('@', '、', $item['student_answer']);//将$item['student_answer']中的 @替换成顿号、 达到展示效果
                            $child[$key]['student_answer'] = $studentAnswer;
                            $studentAnswerAarry=explode('@',$item['student_answer']);//将$item['student_answer']利用@符号拆成数组传到前端
                            $child[$key]['studentAnswerAarry'] = $studentAnswerAarry;

                        }elseif (intval($item['answer']) === 1 || $item['answer'] === 0){  //当为判断题时，进行答案中，0,1与错误、正确之间的转换
                            //dd($item);
                            $studentAnswer =empty($item['student_answer'])?'未作答':$answerArr[$item['student_answer']];
                                $child[$key]['student_answer'] = $studentAnswer;
                                $child[$key]['studentAnswerAarry'] = null;
                            }else {
                                  $studentAnswer=empty($item['student_answer'])?'未作答':$item['student_answer'];//试题答案（a/abc/0,1）
                                  $child[$key]['student_answer'] = $studentAnswer;
                                  $child[$key]['studentAnswerAarry'] =[$studentAnswer];  // 把单选题的答案也变成数组，方便前端回显选择过的答案
                            }

                          $child[$key]['parsing'] = '解析：' . $item['parsing']; //题目答案解析拼接
                           //无论答案是多选 还是 单选 或 判断题（0/1） 使用explode 拆分后都可以得到一个一维数组，然后判断两个数组是否相同
                            // 当答案为 0 时 if(!empty(0)) 是不成立的，所以不能加上 if(!empty($item['answer'])) 这个条件进行筛选
                            $studentAnswerExplode = explode('@', $item['student_answer']);

                            $answerExplode = explode('@', $item['answer']);

                            $c=array_diff($answerExplode,$studentAnswerExplode); //注意array_diff的使用方式：这里必须是正确答案 $answerExplode 的数组在前
                           /*if (empty($c)) {
                               $stuScore += $v['score'];
                            }*/
                    }
                }
                $arr = ['0' => '一', '1' => '二', '2' => '三', '3' => '四', '4' => '五', '5' => '六', '6' => '七', '7' => '八', '8' => '九', '9' => '十'];
                $data[$k]['Title'] = $arr[$k] . '、' . $v['name'] . ' ' . '共' . $v['number'] . '题，' . '每题' . $v['score'] . '分' . ' ';
                $data[$k]['questionType']=$v['exam_question_type_id'];
                //$examItems['stuScore'] = $stuScore;//考试最终成绩
                $data[$k]['child'] = $child;
            }
        }
        $examItems['stuScore'] =ExamResult::where('student_id',$studentMsg->id)->pluck('score');
        //dd($data);
         return view('osce::admin.statisticalanalysis.statistics_student_query',
             [
                 'examItems'=>$examItems,
                 'data'=>$data
             ]);

    }

}
