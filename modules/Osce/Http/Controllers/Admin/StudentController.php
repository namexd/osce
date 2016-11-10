<?php
/**
 * Created by PhpStorm.
 * User: Zhoufuxiang
 * Date: 2016/3/9 0009
 * Time: 16:37
 */

namespace Modules\Osce\Http\Controllers\Admin;


use Illuminate\Http\Request;
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
}