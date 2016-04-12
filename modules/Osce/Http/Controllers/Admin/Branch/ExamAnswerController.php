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
use Modules\Osce\Repositories\Common;


class ExamAnswerController extends CommonController
{

     //将为秒的时间转化为  XX分XX秒
     public function timeTransformation($time){

         $exam_result_time = Common::handleTime($time);

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
                    foreach ($v->ExamQuestionFormal as $key => $item) {
                        $child[$key]['exam_question_name'] = $key + 1 . '.' . '' . $item['name'] .'?'; // 拼接试题名称
                        $child[$key]['exam_question_image'] = unserialize($item['image']); //试题图片
                        $child[$key]['contentItem'] = explode('|%|', $item['content']); //试题内容（A.内容，B.内容，C.内容）用,拼接试题内容
                        $child[$key]['exam_question_id'] =  $item['id']; // 拼接试题名称
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

                                if(strlen($item['student_answer'])&&$item['student_answer']==0){
                                    $studentAnswer =$answerArr[$item['student_answer']];
                                }else {
                                    $studentAnswer = empty($item['student_answer']) ? '未作答' : $answerArr[$item['student_answer']];
                                }
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
                $data[$k]['Title'] = $this->numToWord($k+1) . '、' . $v['name'] . ' ' . '共' . $v['number'] . '题，' . '每题' . $v['score'] . '分' . ' ';
                $data[$k]['questionType']=$v['exam_question_type_id'];
                //$examItems['stuScore'] = $stuScore;//考试最终成绩
                $data[$k]['child'] = $child;
            }
        }

        $examItems['stuScore'] =ExamResult::where('student_id',$studentMsg->id)->pluck('score');

         return view('osce::admin.statisticalanalysis.statistics_student_query',
             [
                 'examItems'=>$examItems,
                 'data'=>$data
             ]);

    }

    /**
     * 题目数字话
     * @method  GET
     * @url   /osce/admin/answer/student-answer
     * @access public
     * @param
     * @author wt <wangtao@misrobot.com>
     * @date    2016年4月5日15:09:43
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    protected function numToWord($num)
    {
        $chiNum = array('零', '一', '二', '三', '四', '五', '六', '七', '八', '九');
        $chiUni = array('','十', '百', '千', '万', '亿', '十', '百', '千');
        $chiStr = '';
        $num_str = (string)$num;
        $count = strlen($num_str);
        $last_flag = true; //上一个 是否为0
        $zero_flag = true; //是否第一个
        $temp_num = null; //临时数字
        $chiStr = '';//拼接结果
        if ($count == 2) {//两位数
            $temp_num = $num_str[0];
            $chiStr = $temp_num == 1 ? $chiUni[1] : $chiNum[$temp_num].$chiUni[1];
            $temp_num = $num_str[1];
            $chiStr .= $temp_num == 0 ? '' : $chiNum[$temp_num];
        }else if($count > 2){
            $index = 0;
            for ($i=$count-1; $i >= 0 ; $i--) {
                $temp_num = $num_str[$i];
                if ($temp_num == 0) {
                    if (!$zero_flag && !$last_flag ) {
                        $chiStr = $chiNum[$temp_num]. $chiStr;
                        $last_flag = true;
                    }
                }else{
                    $chiStr = $chiNum[$temp_num].$chiUni[$index%9] .$chiStr;
                    $zero_flag = false;
                    $last_flag = false;
                }
                $index ++;
            }
        }else{
            $chiStr = $chiNum[$num_str[0]];
        }
        return $chiStr;
    }



}
