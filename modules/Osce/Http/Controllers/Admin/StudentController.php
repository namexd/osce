<?php
/**
 * Created by PhpStorm.
 * User: Zhoufuxiang
 * Date: 2016/3/9 0009
 * Time: 16:37
 */

namespace Modules\Osce\Http\Controllers\Admin;


use Illuminate\Http\Request;
use Modules\Osce\Entities\Exam;
use Modules\Osce\Entities\Student;
use Modules\Osce\Http\Controllers\CommonController;
use Modules\Osce\Entities\ExamPlan;
use DB;

class StudentController extends CommonController
{
    /**
     *
     * @api POST /osce/admin/exam/judge-student
     * @access public
     *
     * @param Request $request post请求<br><br>
     * <b>post请求字段：</b>
     * * string        参数英文名        参数中文名(必须的)
     *
     * @return object
     *
     * @version 1.0
     * @author Zhoufuxiang <Zhoufuxiang@misrobot.com>
     * @date ${DATE} ${TIME} 201-3-9
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     *
     */
    public function postJudgeStudent(Request $request)
    {
        //验证
        $this->validate($request, [
            'id'        => 'required|integer',
            'exam_id'   => 'required|integer',
        ]);

        try {
            $student_id = $request->get('id');          //获取student_id
            $exam_id    = $request->get('exam_id');     //获取考试id

            //进入模型逻辑
            $result = ExamPlan::where('student_id', $student_id)->where('exam_id', $exam_id)->first();

            if ($result) {
                return $this->success_data([], $code = 1, $message = '删除后，智能排考会被删除，确认删除？');
            }else{
                return $this->success_data([], $code = 1, $message = '确认删除？');
            }

        } catch (\Exception $ex) {
            return $this->fail($ex);
        }
    }

    /**
     *学生通知
     * @method GET
     * @url /osce/admin/ Student/student-inform
     * @access public
     *
     * @param Request $request get请求<br><br>
     * <b>post请求字段：</b>
     *
     *
     * @return ${response}
     *
     * @version 1.0
     * @author zhouqiang <zhouqiang@misrobot.com>
     * @date ${DATE} ${TIME}
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */


    public function getStudentInform(Request $request ,Student $student)
    {
        //验证
        $this->validate($request, [
            'exam_id'   => 'required|integer',
        ]);
        $examId = $request->get('exam_id');
        
        try{
            //更据考试id拿到所有学生通知数据
            $studentOpenid =$student->getStudentsOpendIds($examId);
            if(empty($studentOpenid)){
                throw new \Exception('没有学生信息');
            }else{
                $sendMsg = $student->sendMsg($studentOpenid);
            }
       
            
        } catch (\Exception $ex){
            throw $ex;
        }


    }




}