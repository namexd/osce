<?php
/**
 * Created by PhpStorm.
 * User: j5110
 * Date: 2016/1/6
 * Time: 10:30
 */

namespace Modules\Osce\Http\Controllers\Admin;


use Illuminate\Http\Request;
use Modules\Osce\Entities\Exam;
use Modules\Osce\Entities\ExamScreening;
use Modules\Osce\Entities\Station;
use Modules\Osce\Http\Controllers\CommonController;
use Auth;

class ExamController extends CommonController
{
    /**
     * 获取考试列表
     * @api       GET /osce/admin/exam/exam-list
     * @access    public
     * @param Request $request get请求<br><br>
     *                         <b>get请求字段：</b>
     *                         string        keyword         关键字
     *                         string        order_name      排序字段名 枚举 e.g 1:设备名称 2:预约人 3:是否复位状态自检 4:是否复位设备
     *                         string        order_by        排序方式 枚举 e.g:desc,asc
     * @return view
     * @version   1.0
     * @author    jiangzhiheng <jiangzhiheng@misrobot.com>
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function getExamList(Request $request, Exam $exam)
    {
        //验证略

        //从模型得到数据
        $data = $exam->showExamList();

        return view('osce::admin.exammanage.exam_assignment', ['data' => $data]);

    }

    /**
     * 删除考试
     * @api       POST /osce/admin/exam/delete
     * @access    public
     * @param Request $request post请求<br><br>
     *                         <b>post请求字段：</b>
     * id 考试id
     * @param Exam $exam
     * @return view
     * @version   1.0
     * @author    jiangzhiheng <jiangzhiheng@misrobot.com>
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function postDelete(Request $request, Exam $exam)
    {
        //验证
        $this->validate($request, [
            'id' => 'required|integer'
        ]);

        try {
            //获取id
            $id = $request->input('id');

            //进入模型逻辑
            $result = $exam->deleteData($id);

            if ($result !== true) {
                throw new \Exception('删除考试失败，请重试！');
            } else {
                return redirect()->route('osce.admin.exam.getExamList');
            }

        } catch (\Exception $ex) {
            return redirect()->back()->withError($ex);
        }
    }

    /**
     * 新增考试表单页面
     * @url /osce/admin/exam/add-exam
     * @access public
     * @return view
     *
     * @version 1.0
     * @author Zhoufuxiang <Zhoufuxiang@misrobot.com>
     * @date 2016-01-02 13:30
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function getAddExam(){
        return view('osce::admin.exammanage.exam_add');
    }

    /**
     * 新增考试
     * @url     POST /osce/admin/exam/add-exam
     * @access  public
     *
     * @param Request $request post请求<br><br>
     * <b>post请求字段：</b>
     * * string        参数英文名        参数中文名(必须的)
     * * string        参数英文名        参数中文名(必须的)
     * * string        参数英文名        参数中文名(必须的)
     * * string        参数英文名        参数中文名(必须的)
     *
     * @return redirect
     *
     * @version 1.0
     * @author Zhoufuxing <Zhoufuxing@misrobot.com>
     * @date 2016-01-06 14:25
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function postAddExam(Request $request, Exam $model)
    {
        $this   ->  validate($request,[
            'name'          =>  'required',
        ],[
            'name.required'     =>  '考试名称必填',
        ]);

        $user   =   Auth::user();
        if(empty($user)){
            throw new \Exception('未找到当前操作人信息');
        }

        //处理相应信息,将$request中的数据分配到各个数组中,待插入各表
        $examData = [
            'name'           => $request  ->  get('name'),
            'create_user_id' => $user     ->  id
        ];

        $examScreeningData =  $request  ->  get('time');
        //判断输入的时间是否有误
        foreach($examScreeningData as $key => $value){
            if(!strtotime($value['begin_dt'])){
                throw new \Exception('输入的时间有误！');
            }
            if(!strtotime($value['end_dt'])){
                throw new \Exception('输入的时间有误！');
            }
            $examScreeningData[$key]['create_user_id'] = $user -> id;
        }

        $data   =   [$examData, $examScreeningData];

        try{
            if($exam = $model -> addExam($data))
            {
                return redirect()->route('osce.admin.exam.getExamList');
            } else {
                throw new \Exception('新增考试失败');
            }
        } catch(\Exception $ex) {
            throw $ex;
        }
    }


    public function getStudentList(Request $request)
    {
        //验证规则，暂时留空

        //获取各字段
        $formData = $request->only('exam_name', 'student_name');
        //获取当前场所的类
        $model = new Student();

        //从模型得到数据
        $data = $model->showStudentList();

        //展示页面
        return view('osce::admin.exammanage.examinee_query', ['data' => $data]);
    }

    /**
     * 通过考试的id获取考站
     * @api       GET /osce/admin/exam/station-list
     * @access    public
     * @param Request $request get请求<br><br>
     *                         <b>get请求字段：</b>
     *                                      id  考试id
     * @return view
     * @version   1.0
     * @author    jiangzhiheng <jiangzhiheng@misrobot.com>
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function getStationList(Request $request)
    {
        $this->validate($request, [
            'id' => 'required|integer'
        ]);

        //$id为考试id
        $id = $request->input('id');

        //如果在考试考场表里查不到信息的话，就说明还没有选择跳到上一个选项卡去
        $result = ExamScreening::where('exam_screening.exam_id','=',$id)->first();
        if (!$result) {
            return redirect()->route('')->withErrors('对不起，请选择房间');
        }

        //得到room_id
        $roomId = $result->room_id;

        //根据room_id得到考站列表
        $data = Station::where('station.room_id' , '=' , $roomId)
            ->select([
                'station.id as id',
                'station.name as name'
            ])->get();

        return view('osce::admin.exammanage.sp_invitation', ['data' => $data]);
    }
}