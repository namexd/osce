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
use Modules\Osce\Entities\Student;
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
     * * string        name        考试名称(必须的)
     * * string      begin_dt      开始时间(必须的)
     * * string       end_dt       结束时间(必须的)
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
            'status'         => 1,
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

        try{
            if($exam = $model -> addExam($examData, $examScreeningData))
            {
                return redirect()->route('osce.admin.exam.getExamList');
            } else {
                throw new \Exception('新增考试失败');
            }
        } catch(\Exception $ex) {
            throw $ex;
        }
    }

    /**
     * 编辑考试基本信息
     * @api   GET /osce/admin/exam/getEditExam
     * @access public
     *
     * @param Request $request post请求<br><br>
     * <b>post请求字段：</b>
     * * string        id        考试id(必须的)
     *
     * @return view
     *
     * @version 1.0
     * @author Zhoufuxiang <Zhoufuxiang@misrobot.com>
     * @date ${DATE} ${TIME}
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     *
     */
    public function getEditExam(Request $request)
    {
        //验证
        $this->validate($request, [
            'id' => 'required|integer'
        ]);

        //获得ID
        $id = $request->input('id');

        //通过id查到该条信息
        try {
            $data = Exam::findOrFail($id);

            return view('osce::admin.exammanage.add_basic',['data'=>$data]);
        } catch (\Exception $ex) {
            return redirect()->back()->withErrors($ex);
        }
    }

    /**
     * 考生管理
     * @api   GET /osce/admin/exam/getStudentManage
     * @access public
     *
     * @param Request $request post请求<br><br>
     * <b>post请求字段：</b>
     * * string        exam_id        考试id(必须的)
     *
     * @return view
     *
     * @version 1.0
     * @author Zhoufuxiang <Zhoufuxiang@misrobot.com>
     * @date ${DATE} ${TIME}
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function getExamineeManage(Request $request)
    {
        //验证规则，暂时留空
        $this->validate($request, [
            'id' => 'required|integer'
        ]);

        try {
            //获取id
            $exam_id = $request->input('id');

            $student = new Student();
            //从模型得到数据
            $data = $student->selectExamStudent($exam_id);

            //展示页面
            return view('osce::admin.exammanage.examinee_manage', ['id' => $exam_id ,'data' => $data]);

        } catch (\Exception $ex) {
            return redirect()->back()->withError($ex);
        }
    }

    /**
     * 删除考生
     * @api    POST /osce/admin/exam/postDelStudent
     * @access public
     *
     * @param Request $request post请求<br><br>
     * <b>post请求字段：</b>
     * * string        exam_id        考试id(必须的)
     * * string       student_id      考生id(必须的)
     *
     * @return redirect
     *
     * @version 1.0
     * @author Zhoufuxiang <Zhoufuxiang@misrobot.com>
     * @date ${DATE} ${TIME}
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     *
     */
    public function getDelStudent(Request $request, Student $student)
    {
        //验证
        $this->validate($request, [
            'id' => 'required|integer'
        ]);

        try {
            //获取id
            $exam_id = $request->get('exam_id');
            $student_id = $request->get('id');

            //进入模型逻辑
            $result = $student->deleteData($student_id);

            if ($result !== true) {
                throw new \Exception('删除考试失败，请重试！');
            } else {
                return redirect()->route('osce.admin.exam.getExamineeManage', ['id' => $exam_id]);
            }

        } catch (\Exception $ex) {
            return redirect()->back()->withError($ex);
        }
    }

    /**
     * 新增考生表单页面
     * @api GET /osce/admin/exam/getAddExaminee
     * @access public
     *
     * @param Request $request post请求<br><br>
     * <b>post请求字段：</b>
     * * string        exam_id        考试id(必须的)
     *
     * @return object
     *
     * @version 1.0
     * @author Zhoufuxiang <Zhoufuxiang@misrobot.com>
     * @date ${DATE} ${TIME}
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     *
     */
    public function getAddExaminee(Request $request){
        $id = $request->get('id');
        return view('osce::admin.exammanage.examinee_add', ['id' => $id]);
    }

    /**
     * 新增考生
     * @api post /osce/admin/exam/postAddExaminee
     * @access public
     *
     * @param Request $request post请求<br><br>
     * <b>post请求字段：</b>
     * * string        name        考生名(必须的)
     * * string       id_card      身份证号(必须的)
     * * string       exam_id      考试id(必须的)
     * * string     images_path    考生照片路径(必须的)
     *
     * @return object
     *
     * @version 1.0
     * @author Luohaihua <Luohaihua@misrobot.com>
     * @date ${DATE} ${TIME}
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     * '
     */
    public function postAddExaminee(Request $request, Student $model)
    {
        $this   ->  validate($request,[
            'name'          =>  'required',
            'id_card'       =>  'required',
        ],[
            'name.required'     =>  '姓名必填',
            'id_card.required'  =>  '身份证号必填',
        ]);
        $exam_id = $request->get('exam_id');
        $examineeData = [
            'name'           => $request  ->  get('name'),
            'id_card'        => $request  ->  get('id_card'),
            'exam_id'        => $exam_id,
            'images_path'    => $request  ->  get('images_path'),
        ];

        try{
            if($exam = $model -> addExaminee($examineeData))
            {
                return redirect()->route('osce.admin.exam.getExamineeManage', ['id' => $exam_id]);
            } else {
                throw new \Exception('新增考试失败');
            }
        } catch(\Exception $ex) {
            throw $ex;
        }
    }

    /**
     * 考生查询
     * @api GET /osce/admin/exam/getStudentQuery
     * @access public
     *
     * @param Request $request post请求<br><br>
     * <b>post请求字段：</b>
     * * string        参数英文名        参数中文名(必须的)
     * * string        参数英文名        参数中文名(必须的)
     * * string        参数英文名        参数中文名(必须的)
     * * string        参数英文名        参数中文名(必须的)
     *
     * @return view
     *
     * @version 1.0
     * @author Zhoufuxiang <Zhoufuxiang@misrobot.com>
     * @date ${DATE} ${TIME}
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function getStudentQuery(Request $request)
    {
        //验证规则，暂时留空

        //获取各字段
        $formData = $request->only('exam_name', 'student_name');
        //获取当前场所的类

        //从模型得到数据
        $data = [];

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

    /**
     * 获取考试列表 接口 （带翻页）
     * @api GET /osce/admin/invigilator/exam-list-data
     * @access public
     *
     * @return json {id:考试ID,name:考试名称}
     *
     * @version 1.0
     * @author Luohaihua <Luohaihua@misrobot.com>
     * @date ${DATE} ${TIME}
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     *
     */
    public function getExamListData(){
        $exam   = new Exam();
        $pagination = $exam->showExamList();

        $data   =   $pagination->toArray();
        return response()->json(
            $this->success_rows(1,'获取成功',$pagination->total(),config('msc.page_size'),$pagination->currentPage(),$data['data'])
        );
    }
}