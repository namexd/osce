<?php
/**
 * Created by PhpStorm.
 * User: j5110
 * Date: 2016/1/6
 * Time: 10:30
 */

namespace Modules\Osce\Http\Controllers\Admin;


use App\Entities\User;
use Cache;
use Illuminate\Http\Request;
use Modules\Osce\Entities\AutomaticPlanArrangement\AutomaticPlanArrangement;
use Modules\Osce\Entities\AutomaticPlanArrangement\ExamPlaceEntity;
use Modules\Osce\Entities\Exam;
use Modules\Osce\Entities\ExamArrange\ExamArrangeRepository;
use Modules\Osce\Entities\ExamFlow;
use Modules\Osce\Entities\ExamFlowRoom;
use Modules\Osce\Entities\ExamFlowStation;
use Modules\Osce\Entities\Examinee;
use Modules\Osce\Entities\ExamPlanForRoom;
use Modules\Osce\Entities\ExamPlanRecord;
use Modules\Osce\Entities\ExamRoom;
use Modules\Osce\Entities\ExamScreening;
use Modules\Osce\Entities\Flows;
use Modules\Osce\Entities\InformInfo;
use Modules\Osce\Entities\Invite;
use Modules\Osce\Entities\Room;
use Modules\Osce\Entities\ExamScreeningStudent;
use Modules\Osce\Entities\ExamSpTeacher;
use Modules\Osce\Entities\RoomStation;
use Modules\Osce\Entities\SmartArrange\SmartArrange;
use Modules\Osce\Entities\SmartArrange\SmartArrangeRepository;
use Modules\Osce\Entities\Station;
use Modules\Osce\Entities\StationTeacher;
use Modules\Osce\Entities\Student;
use Modules\Osce\Entities\Teacher;
use Modules\Osce\Entities\Watch;
use Modules\Osce\Entities\ExamPlan;
use Modules\Osce\Entities\WatchLog;
use Modules\Osce\Http\Controllers\CommonController;
use App\Repositories\Common;
use Auth;
use Symfony\Component\Translation\Interval;
use DB;
use Illuminate\Container\Container as App;

class ExamController extends CommonController
{
    /**
     * 配置考场安排里，考场、考站选项
     */
    private function getSelect(){
        $config =   [
            0   =>  '必考',
            1   =>  '必考',
            2   =>  '二选一',
            3   =>  '三选一',
            4   =>  '四选一',
            5   =>  '五选一',
            6   =>  '六选一',
            7   =>  '七选一',
            8   =>  '八选一',
            9   =>  '九选一',
            10  =>  '十选一'
        ];
        return  $config;
    }

    /**
     * 获取考试列表
     * @url       GET /osce/admin/exam/exam-list
     * @access    public
     * @param Request $request get请求<br><br>
     *                         <b>get请求字段：</b>
     *                         string        keyword         关键字
     *                         string        order_name      排序字段名 枚举 e.g 1:设备名称 2:预约人 3:是否复位状态自检 4:是否复位设备
     *                         string        order_by        排序方式 枚举 e.g:desc,asc
     * @param Exam $exam
     * @return view
     * @throws \Exception
     * @version   1.0
     * @author    jiangzhiheng <jiangzhiheng@misrobot.com>
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function getExamList(Request $request, Exam $exam)
    {
        //验证略
        $this->validate($request,[
            'exam_name' =>'sometimes'
        ]);

        $formData = $request->only('exam_name');

        //从模型得到数据
        $data = $exam->showExamList($formData);
        //得到考试组成，是否排考
        foreach ($data as &$item) {
            $item->constitute = $this->getExamConstitute($item['id']);
            $item->arranged   = count($item->examPlan);
        }

        return view('osce::admin.examManage.exam_assignment', ['data' => $data]);

    }

    /**
     * 删除考试
     * @url       POST /osce/admin/exam/delete
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
            $id = $request->input('id');  //id为考试id

            $exam->deleteData($id);
            return $this->success_data(['删除成功！']);
        } catch (\Exception $ex) {
            return $this->fail($ex);
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
        return view('osce::admin.examManage.exam_assignment_add');
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
            'time'          =>  'required',
            'address'       =>  'required',
        ],[
            'name.required'     =>  '考试名称必填',
            'time.required'     =>  '考试时间必填',
            'address.required'  =>  '考试地址必填',
        ]);
        $user   =   Auth::user();
        if(empty($user)){
            throw new \Exception('未找到当前操作人信息');
        }
        //考试场次 及时间
        $examScreeningData =  $request  ->  get('time');

        try{
            //处理考试场次时间
            $timeData = $model->handleScreeningTime($examScreeningData, $user);
            //获取相应信息,将$request中的数据分配到各个数组中,待插入各表
            $examData = [
                'code'           => 100,
                'name'           => e($request  ->  get('name')),
                'begin_dt'       => $timeData['begin_dt'],
                'end_dt'         => $timeData['end_dt'],
                'status'         => 0,
                'total'          => 0,
                'create_user_id' => $user -> id,
                'sequence_cate'  => e($request  ->  get('sequence_cate')),
                'sequence_mode'  => e($request  ->  get('sequence_mode')),
                'address'        => e($request  ->  get('address')),
                'same_time'      => intval($request  ->  get('same_time')),
                'real_push'      => intval($request  ->  get('real_push')),
            ];
            //阶段
            $gradation = intval($request->get('gradation_order', 1))? :1;

            //添加考试
            $result = $model -> addExam($examData, $timeData['examScreeningData'], $gradation);
            if(!$result){
                throw new \Exception('新增考试失败');
            }

            //成功后，重定向为编辑页面
            return redirect()->route('osce.admin.exam.getEditExam',['id'=>$result->id])->withErrors(['msg'=>'保存成功','code'=>1]);

        } catch(\Exception $ex) {
            //返回原来的页面，并抛出错误
            return redirect()->back()->withErrors($ex->getMessage());
        }
    }

    /**
     * 编辑考试基本信息表单页面
     * @url   GET /osce/admin/exam/edit-exam
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

        //获得exam_id
        $id = $request->input('id');
        //通过id查到该条信息
        try {
            $examData = Exam::findOrFail($id);
            $examScreeningData = ExamScreening::where(['exam_id' => $id])->get();

            return view('osce::admin.examManage.exam_basic_info',[
                'id'=>$id, 'examData'=>$examData, 'examScreeningData'=>$examScreeningData
            ]);

        } catch (\Exception $ex) {
            return redirect()->back()->withErrors($ex->getMessage());
        }
    }

    /**
     * 保存编辑考试基本信息
     * @url POST /osce/admin/exam/postEditExam
     * @access public
     *
     * @param Request $request post请求<br><br>
     * <b>post请求字段：</b>
     * * string        exam_id          考试id(必须的)
     * * string        name             考试名称(必须的)
     * * string        time             场次时间(必须的)
     * * string        address          考试地址(必须的)
     *
     * @return redirect
     *
     * @version 1.0
     * @author Zhoufuxiang <Zhoufuxiang@misrobot.com>
     * @date ${DATE} ${TIME}
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     *
     */
    public function postEditExam(Request $request, Exam $examModel, ExamArrangeRepository $examArrangeRepository)
    {
        //验证,略过
        $this->validate($request, [
            'exam_id'   => 'required',
            'name'      => 'required',
            'time'      => 'required',
            'address'   => 'required'
        ],[
            'name.required'     => '考试名称必须',
            'time.required'     => '考试时间必须',
            'address.required'  => '考试地址必须',
        ]);

        //处理相应信息,将$request中的数据分配到各个数组中,待插入各表
        $exam_id = $request->input('exam_id');
        $examScreeningData = $request -> get('time');

        //查询操作人信息
        $user = Auth::user();
        if (empty($user)) {
            throw new \Exception('未找到当前操作人信息');
        }

        try{
            //处理考试场次时间
            $timeData = $examModel->handleScreeningTime($examScreeningData, $user);

            //处理相应信息,将$request中的数据分配到各个数组中,待插入各表
            $examData = [
                'name'          => e($request  ->  get('name')),
                'begin_dt'      => $timeData['begin_dt'],
                'end_dt'        => $timeData['end_dt'],
                'total'         => count(Student::where('exam_id', $exam_id)->get()),
                'sequence_cate' => $request  ->  get('sequence_cate'),
                'sequence_mode' => $request  ->  get('sequence_mode'),
                'address'       => e($request  ->  get('address')),
                'same_time'     => intval($request  ->  get('same_time')),
                'real_push'     => intval($request  ->  get('real_push')),
            ];
            //阶段
            $gradation = intval($request->input('gradation_order',1))? :1;

            //编辑考试相关信息
            $result = $examModel -> editExam($exam_id, $examData, $timeData['examScreeningData'], $gradation, $examArrangeRepository);
            if(!$result)
            {
                throw new \Exception('修改考试失败');
            }
            return redirect()->route('osce.admin.exam.getEditExam', ['id'=>$exam_id,'succ'=>1]);

        } catch(\Exception $ex) {

            return redirect()->back()->withErrors($ex->getMessage());
        }
    }

    /**
     * 考生管理
     * @url   GET /osce/admin/exam/examinee-manage
     * @access public
     *
     * @param Request $request post请求<br><br>
     * <b>post请求字段：</b>
     * * string        exam_id        考试id(必须的)
     * * string        keyword        关键字(姓名，学号，身份证号，电话)
     *
     * @return view
     *
     * @version 1.0
     * @author Zhoufuxiang <Zhoufuxiang@misrobot.com>
     * @date ${DATE} ${TIME}
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function getExamineeManage(Request $request, Student $student)
    {
        //验证规则
        $this->validate($request, [
            'id' => 'required|integer'
        ]);

        try {
            $exam_id = intval($request->input('id'));            //获取id
            $keyword = trim(e($request->input('keyword')));            //获取搜索关键字

            //从模型得到数据
            $data = $student->selectExamStudent($exam_id, $keyword);

            $status=Exam::where('id','=',$exam_id)->select()->first()->status;
            //展示页面
            return view('osce::admin.examManage.examinee_manage', ['id' => $exam_id ,'data' => $data,'keyword'=>$keyword,'status'=>$status]);

        } catch (\Exception $ex) {
            return redirect()->back()->withError($ex);
        }
    }

    /**
     * 删除考生
     * @url    POST /osce/admin/exam/postDelStudent
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
     */
    public function postDelStudent(Request $request, Student $student)
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
            $result = $student->deleteStudent($student_id,$exam_id);

            if ($result === true) {
                return $this->success_data(['删除成功！']);
            }

        } catch (\Exception $ex) {
            return $this->fail($ex);
        }
    }

    /**
     * 新增考生表单页面
     * @url GET /osce/admin/exam/add-examinee
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
     */
    public function getAddExaminee(Request $request){
        $id = $request->get('id');
        return view('osce::admin.examManage.examinee_manage_add', ['id' => $id]);
    }

    /**
     * 新增考生
     * @url post /osce/admin/exam/add-examinee
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
     * @author Zhoufuxiang <Zhoufuxiang@misrobot.com>
     * @date ${DATE} ${TIME}
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function postAddExaminee(Request $request, Student $model)
    {
        $this   ->  validate($request,[
            'exam_id'       =>  'required',
            'name'          =>  'required',
            'idcard'        =>  'required',
            'mobile'        =>  'required',
            'code'          =>  'required',
            'images_path'   =>  'required',
            'exam_sequence' =>  'required',
            'grade_class'   =>  'required',
            'teacher_name'  =>  'required'
        ],[
            'name.required'         =>  '姓名必填',
            'idcard.required'       =>  '身份证号必填',
            'mobile.required'       =>  '手机号必填',
            'code.required'         =>  '学号必填',
            'images_path.required'  =>  '请上传照片',
            'exam_sequence.required'=>  '准考证号必填',
            'grade_class.required'  =>  '班级必填',
            'teacher_name.required' =>  '班主任姓名必填'
        ]);

        //考试id
        $exam_id = $request->get('exam_id');
        $images  = $request->get('images_path');      //照片
        //用户数据(姓名,性别,身份证号,手机号,学号,邮箱,照片)
        $userData = $request->only('name','gender','idcard','mobile','code','email');
        $userData['avatar'] = $images[0];      //照片
        //考生数据(姓名,性别,身份证号,手机号,学号,邮箱,备注,准考证号,班级,班主任姓名)
        $examineeData = $request->only('name','idcard','mobile','code','description','exam_sequence','grade_class','teacher_name');
        $examineeData['avator'] = $images[0];  //照片

        try{
            if($exam = $model -> addExaminee($exam_id, $examineeData, $userData))
            {
                return redirect()->route('osce.admin.exam.getExamineeManage', ['id' => $exam_id]);
            } else {
                throw new \Exception('新增考试失败');
            }
        } catch(\Exception $ex) {
            return redirect()->back()->withErrors($ex->getMessage());
        }
    }

    public function getEidtExaminee(Request $request){
        $this   ->  validate($request,[
            'id'            =>  'required',
        ]);

        $id =   $request    ->  get('id');
        $student    =   Student::findOrFail($id);

        return view('osce::admin.examManage.examinee_manage_edit', ['item' => $student]);
    }

    public function postEditExaminee(Request $request){
        $this   ->  validate($request,[
            'id'            =>  'required',
            'name'          =>  'required',
            'idcard'        =>  'required',
            'code'          =>  'sometimes',
            'gender'        =>  'required',
            'mobile'        =>  'required',
            'description'   =>  'sometimes',
            'images_path'   =>  'required',
            'exam_sequence' =>  'required',
            'grade_class'   =>  'required',
            'teacher_name'  =>  'required'
        ],[
            'name.required'         =>  '姓名必填',
            'idcard.required'       =>  '身份证号必填',
            'mobile.required'       =>  '手机号必填',
            'images_path.required'  =>  '请上传照片',
            'exam_sequence.required'=>  '准考证号必填',
            'grade_class.required'  =>  '班级必填',
            'teacher_name.required' =>  '班主任姓名必填'
        ]);
        $id         =   $request->get('id');
        $student    =   Student::find($id);
        $images     =   $request->get('images_path');   //照片
        //考生数据(姓名,性别,身份证号,手机号,学号,邮箱,备注,准考证号,班级,班主任姓名)
        $data = $request->only('name','idcard','mobile','code','description','exam_sequence','grade_class','teacher_name');
        $data['avator'] = $images[0];  //照片

        try{
            if($student) {
                //查询学号是否存在
                $code = Student::where('code', $data['code'])->where('user_id','<>',$student->user_id)->first();
                if(!empty($code)){
                    throw new \Exception('该学号已经有别人使用！');
                }
                //查询手机号码是否已经被别人使用
                $mobile = User::where(['mobile' => $data['mobile']])->where('id','<>',$student->user_id)->first();
                if(!empty($mobile)){
                    throw new \Exception('手机号已经存在，请输入新的手机号');
                }
                foreach($data as $feild => $value) {
                    if(!empty($value)){
                        $student->  $feild  =   $value;
                    }
                }

                if($student->save()) {
                    $user   =   $student->userInfo;
                    $user   ->  email  = $request->get('email');
                    $user   ->  gender = $request->get('gender');
                    $user   ->  avatar = $data['avator'];
                    if(!$user->save()) {
                        throw new \Exception('用户信息修改失败');
                    }
                    return redirect()->route('osce.admin.exam.getExamineeManage',['id'=>$student->exam_id]);
                } else {
                    throw new \Exception('考生信息修改失败');
                }

            } else {
                throw new \Exception('没有找到该考生');
            }

        } catch(\Exception $ex) {
            return redirect()->back()->withErrors($ex->getMessage());
        }
    }

    /**
     * Excel导入考生
     * @url POST /osce/admin/exam/import-student
     * @access public
     *
     * @param Request $request post请求<br><br>
     * <b>post请求字段：</b>
     * * string        id        考试id(必须的)
     *
     * @return object
     *
     * @version 1.0
     * @author Zhoufuxiang <Zhoufuxiang@misrobot.com>
     * @date ${DATE} ${TIME}
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function postImportStudent($id,Request $request, Student $student)
    {
        try {
            //获得上传的数据
            $exam_id= $id;
            $data   = Common::getExclData($request, 'student');
            $exam   = Exam::find($exam_id);
            if($exam->status!=0)
            {
                throw new \Exception('此考试当前状态下不允许新增');
            }
            //去掉sheet
            $studentList = array_shift($data);
            //判断模板 列数、表头是否有误
            $student->judgeTemplet($studentList);
            //将中文表头转为英文
            $examineeData = Common::arrayChTOEn($studentList, 'osce.importForCnToEn.student');
            $result = $student->importStudent($exam_id, $examineeData);
            if(!$result){
                throw new \Exception('学生导入数据失败，请参考模板修改后重试');
            }

            return json_encode($this->success_data([], 1, "成功导入{$result}个学生！"));

        } catch (\Exception $ex) {
            return json_encode($this->fail($ex));
        }
    }

    /**
     * 考生查询
     * @url GET /osce/admin/exam/student-query
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
     * @author Zhoufuxiang <Zhoufuxiang@misrobot.com>  zhouchong <Zhouchong@misrobot.com>
     * @date ${DATE} ${TIME}
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function getStudentQuery(Request $request)
    {
        //验证规则，暂时留空
        $this -> validate($request,[
              'exam_name'      => 'sometimes',
              'student_name'   => 'sometimes',
        ]);
        //获取各字段
        $exam_name    = trim(e($request->get('exam_name')));
        $student_name = trim($request->get('student_name'));
        $formData     = ['exam_name'=>$exam_name, 'student_name'=>$student_name];

        //获取当前场所的类
        $examModel = new Student();
        //从模型得到数据
        $data = $examModel->getList($formData);
        //展示页面
        return view('osce::admin.examManage.examinee_query', ['data'=>$data, 'exam_name'=>$exam_name, 'student_name'=>$student_name]);
    }

    /**
     * 通过考试的id获取考站
     * @url       GET /osce/admin/exam/station-list
     * @access    public
     * @param Request $request get请求<br><br>
     *                         <b>get请求字段：</b>
     *                                      id  考试id
     * @return view
     * @version   1.0
     * @author    jiangzhiheng <jiangzhiheng@misrobot.com>
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function getStationList(Request $request, Exam $exam)
    {
        $this->validate($request, [
            'id' => 'required|integer'
        ]);

        //$id为考试id
        $id = $request->input('id');

        //如果在考试考场表里查不到信息的话，就说明还没有选择跳到上一个选项卡去
        $result = ExamScreening::where('exam_screening.exam_id','=',$id)->first();
        if (!$result) {
            return redirect()->back()->withErrors('对不起，请选择房间');
        }

        //得到room_id
        $roomId = $result->room_id;
        //得到sp老师的编号
        $spTeacherId = ExamSpTeacher::where('exam_sp_teacher.exam_screening_id', '=' , $result->id)
            ->select('teacher_id')->get();


        //TODO:请蒋志恒 确认行代码还可以跑    如果 非必要 请及时删除 2016-01-17 22:28 罗海华
        //return view('osce::admin.examManage.sp_invitation', ['data' => $data]);
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


    /**
     * 判断是以考室还是以考站的考试安排着陆页
     * @url GET /osce/admin/exam/choose-exam-arrange
     * @access public
     * @param Request $request
     * <b>get请求字段：</b>
     * id    考试id
     * @return View
     * @version 1.0
     * @author Jiangzhiheng <Jiangzhiheng@misrobot.com>
     * @date  2016-01-18
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     *
     */
    public function getChooseExamArrange(Request $request)
    {

        $this->validate($request ,[
            'id' => 'required|integer',
        ]);

        try {

            $id = $request->get('id');
            // todo  清空考试安排临时表
            
            //通过id找到对应的模式
            $examMode = Exam::findOrFail($id)->sequence_mode;
            switch ($examMode) {
                case '1' :
                    $result =  $this->getStationAssignment($request);
                    break;
                case '2' :

                    $result = $this->getStationAssignment($request);

                    break;
                default:
                    $result =  $this->getStationAssignment($request);
            }
            return $result;
        } catch (\Exception $ex) {
            return redirect()->back()->withErrors($ex->getMessage());
        }
    }

    /**
     * 考场安排
     * @url GET /osce/admin/exam/getExamroomAssignment
     * * string        参数英文名        参数中文名(必须的)
     *
     * @param Request $request
     * @return object
     * @version 1.0
     * @author Zhoufuxiang <Zhoufuxiang@misrobot.com>
     * @date ${DATE} ${TIME}
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function getExamroomAssignment(Request $request)
    {
        $this->validate($request,[
            'id' => 'required|integer'
        ]);
        $exam_id = $request -> get('id');
        $examRoom = new ExamRoom();
        //获取考试id对应的考场数据
        $examRoomData = $examRoom -> getExamRoomData($exam_id);

        $serialnumberGroup = [];
        foreach ($examRoomData as $item) {
            $serialnumberGroup[$item->serialnumber][$item->id] = $item;
        }
        //获取考试对应的考站数据
        $examStationData = $examRoom -> getExamStation($exam_id) -> groupBy('station_id');
        $inviteData = Invite::status($exam_id);

        //将邀请状态插入$stationData
        $examRoomData=  [];
        foreach ($examStationData as $key=>&$items) {
            foreach ($items as &$item) {
                $item->invite_status = 0;
                foreach ($inviteData as $value) {
                    if ($item->id == $value->invite_user_id) {

                        $item->invite_status = $value->invite_status;
//
                    }
//                    else {
//                        $item->invite_status = 0;
//                    }
                }
            }
        }

        $status=Exam::where('id',$exam_id)->select('status')->first()->status;

        return view('osce::admin.examManage.exam_room_assignment', [
            'id'                => $exam_id,
            'status'            => $status,
            'examRoomData'      => $serialnumberGroup,
            'examStationData'   => $examStationData,
            'getSelect'         => $this->getSelect(),
            'succ'              => $request->get('succ')
        ]);
    }

    /**
     * 保存 考场安排数据
     * @url POST /osce/admin/exam/postExamroomAssignmen
     * @access public
     *
     * @param Request $request post请求<br><br>
     * <b>post请求字段：</b>
     * * string        参数英文名        参数中文名(必须的)
     * @return redirect
     * @throws \Exception
     * @version 1.0
     * @author Zhoufuxiang <Zhoufuxiang@misrobot.com>
     * @date ${DATE} ${TIME}
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function postExamroomAssignmen(Request $request)
    {
//        try{

            //处理相应信息,将$request中的数据分配到各个数组中,待插入各表
            $exam_id        = $request  ->  get('id');          //考试id
            $roomData       = $request  ->  get('room');        //考场数据
            $stationData    = $request  ->  get('station');     //考站数据
            //生成一个方案标识，solution_mark
            //流程id
            //查看是否有本场考试
            Exam::findOrFail($exam_id);
            //查询 考试id是否有对应的考场数据
            $examRoom = new ExamRoom();
            $examRoomData = $examRoom -> getExamRoomData($exam_id);
            //判断是否存在已有数据
            $flows = new Flows();
            if(count($examRoomData) != 0){
                if(!$flows -> editExamroomAssignmen($exam_id, $roomData, $stationData)){
                    throw new \Exception('考场安排保存失败，请重试！');
                }

            }else{
                if(!$flows -> saveExamroomAssignmen($exam_id, $roomData, $stationData)){
                    throw new \Exception('考场安排保存失败，请重试！');
                }
            }
            return redirect()->route('osce.admin.exam.getExamroomAssignment', ['id'=>$exam_id,'succ'=>1]);

//        } catch(\Exception $ex){
//            return redirect()->back()->withErrors($ex->getMessage());
//        }
    }

    /**
     * 获取考场列表 接口
     * @api GET /osce/admin/exam/Room-list-data
     * @access public
     *
     * @return json  {id:考场ID,name:考场名称}
     *
     * @version 1.0
     * @author Zhoufuxiang <Zhoufuxiang@misrobot.com>
     * @date ${DATE} ${TIME}
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     *
     */

    public function getRoomListData()
    {
        //如果改考场下面没有关联考站，就不给展示在列表
        //获得所有的在room_station考场列表id
        $isExist = RoomStation::select(['room_id'])->groupBy('room_id')->get()->pluck('room_id');
        $data = Room::whereIn('id',$isExist)->select(['id', 'name'])->get();

        return response()->json(
            $this->success_data($data, 1, 'success')
        );
    }
    /**
     * 获取考站数据 接口
     * @api GET /osce/admin/exam/getStationData
     * @access public
     *
     * @param Request $request post请求<br><br>
     * <b>post请求字段：</b>
     * * string        room_id        考场ID(必须的)
     *
     * @return  json  {station_id:考站ID,name:考场名称}
     *
     * @version 1.0
     * @author Zhoufuxiang <Zhoufuxiang@misrobot.com>
     * @date ${DATE} ${TIME}
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function getStationData(Request $request)
    {
        $this->validate($request, [
            'id' => 'required|integer'
        ]);
        //获取考场ID：room_id
        $room_id = $request -> get('id');

        $roomStation = new RoomStation();
        $data = $roomStation->getRoomStationData($room_id);
        return response()->json(
            $this->success_data($data, 1, 'success')
        );
    }

    /**
     * 获取老师列表数据 接口
     * @api GET /osce/admin/exam/getTeacherListData
     * @access public
     *
     * @param Request $request post请求<br><br>
     * <b>post请求字段：</b>
     * * string        teacher        老师id数组(必须的)
     *
     * @return json
     *
     * @version 1.0
     * @author Zhoufuxiang <Zhoufuxiang@misrobot.com>
     * @date ${DATE} ${TIME}
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function getTeacherListData(Request $request)
    {
        //获取老师ID数组：teacher_id
        $formData = $request -> get('teacher');
        $teacher = new Teacher();
        $data = $teacher->getTeacherList($formData);
        return response()->json(
            $this->success_data($data, 1, 'success')
        );
    }

    /**
     *检测是否绑定
     * @method GET 接口
     * @url exam/watch-status
     * @access public
     *
     * @param Request $request post请求<br><br>
     * <b>post请求字段：</b>
     * * int        id        腕表Id(必须的)
     *
     * @return ${response}
     *
     * @version 1.0
     * @author zhouchong <zhouchong@misrobot.com>
     * @date ${DATE} ${TIME}
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function getWatchStatus(Request $request){
        $this->validate($request,[
            'id' =>'required|integer'
        ]);
        $id=$request->get('id');
        $IsEnd=ExamScreeningStudent::where('watch_id',$id)->select('is_end')->first()->is_end;
        if($IsEnd==1){
            return response()->json(
                $this->success_rows(1,'已绑定')
            );
        }
        if($IsEnd==0){
            return response()->json(
                $this->success_rows(0,'未绑定')
            );
        }
    }

    /**
     *绑定腕表
     * @method GET 接口
     * @url exam/bound-watch
     * @access public
     *
     * @param Request $request post请求<br><br>
     * <b>post请求字段：</b>
     * * int        id        腕表id(必须的)
     *
     * @return ${response}
     *
     * @version 1.0
     * @author zhouchong <zhouchong@misrobot.com>
     * @date ${DATE} ${TIME}
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function getBoundWatch(Request $request){
        $this->validate($request,[
            'id' =>'required|integer'
        ]);
        $id=$request->get('id');
        $action='绑定';
        $userId=ExamScreeningStudent::where('watch_id',$id)->select()->first()->student_id;
        $result=ExamScreeningStudent::where('watch_id',$id)->update(['is_end'=>1]);
        if($result){
            $signinDt=ExamScreeningStudent::where('watch_id',$id)->select()->first()->signin_dt;
            $result=Watch::where('id',$id)->update(['status'=>1]);
            if($result){
                $data=array(
                    'watch_id'       =>$id,
                    'action'         =>$action,
                    'context'        =>array('time'=>$signinDt,'is_end'=>1,'status'=>1),
                    'create_user_id' =>$userId,
                );
                $watchModel=new WatchLog();
                $watchModel->historyRecord($data);

                return response()->json(
                    $this->success_rows(1,'绑定成功')
                );
            }
        }else{
            return response()->json(
                $this->success_rows(0,'绑定失败','false')
            );
        }
    }

    /**
     *解除绑定腕表
     * @method GET 接口
     * @url exam/unwrap-watch
     * @access public
     *
     * @param Request $request post请求<br><br>
     * <b>post请求字段：</b>
     * * int        id        腕表ID(必须的)
     *
     * @return ${response}
     *
     * @version 1.0
     * @author zhouchong <zhouchong@misrobot.com>
     * @date ${DATE} ${TIME}
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function getUnwrapWatch(Request $request){
        $this->validate($request,[
            'id' =>'required|integer'
        ]);
        $id=$request->get('id');
        $action='解绑';
        $userId=ExamScreeningStudent::where('watch_id',$id)->select()->first()->student_id;
        $result=ExamScreeningStudent::where('watch_id',$id)->update(['is_end'=>0]);
        if($result){
            $updated_at=ExamScreeningStudent::where('watch_id',$id)->select('updated_at')->first()->updated_at;
            $result=Watch::where('id',$id)->update(['status'=>0]);
            if($result){
                $data=array(
                    'watch_id'       =>$id,
                    'action'         =>$action,
                    'context'        =>array('time'=>$updated_at,'is_end'=>0,'status'=>0),
                    'create_user_id' =>$userId,
                );
                $watchModel=new WatchLog();
                $watchModel->historyRecord($data);
                return response()->json(
                    $this->success_rows(1,'解绑成功')
                );
            }
        }else{
            return response()->json(
                $this->success_rows(0,'解绑失败')
            );
        }
    }

    /**
     *检测学生状态
     * @method GET
     * @url /user/
     * @access public
     *
     * @param Request $request post请求<br><br>
     * <b>post请求字段：</b>
     * * string        参数英文名        参数中文名(必须的)
     * * string        参数英文名        参数中文名(必须的)
     * * string        参数英文名        参数中文名(必须的)
     * * string        参数英文名        参数中文名(必须的)
     *
     * @return ${response}
     *
     * @version 1.0
     * @author zhouchong <zhouchong@misrobot.com>
     * @date ${DATE} ${TIME}
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function getStudentDetails(Request $request){
        $this->validate($request,[
            'id_card' => 'required'
        ]);

        $idCard=$request->get('id_card');

        $students=Student::where('id_card',$idCard)->select('id','code')->get();
        foreach($students as $item){
            $student=[
                'id'    =>$item->id,
                'code'  =>$item->code,
//                'exam_id'  =>$item->exam_id,
            ];
        }
        if(!$student){
           return response()->json(
               $this->success_rows(2,'未找到学生相关信息')
           );
        }
        $student['is_end']=ExamScreeningStudent::where('student_id',$student['id'])->select('is_end')->first()->is_end;
//        $student['exam']=Exam::where('exam_id',$student['exam_id'])->select()->first(); //查询准考证号

        if($student['is_end']==1){
            return response()->json(
                $this->success_data($student,0,'已绑定')
            );
        }
         return response()->json(
                 $this->success_data($student,1,'未绑定')
                );
    }

    /**
     * 智能排考接口
     * @url GET /osce/admin/exam/intelligence-eaxm-plan
     * @access public
     *
     * <b>get请求字段：</b>
     * * string        参数英文名        参数中文名(必须的)
     * * string        参数英文名        参数中文名(必须的)
     * * string        参数英文名        参数中文名(必须的)
     * * string        参数英文名        参数中文名(必须的)
     *
     * @return json
     *
     * @version 1.0
     * @author Luohaihua <Luohaihua@misrobot.com>
     * @date 2015-12-29 17:09
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     *
     */
    public function getIntelligenceEaxmPlan(Request $request){
        $this   ->  validate($request,[
            'id'    =>  'required|integer'
        ]);

        $id         =   $request    ->  get('id');
        try
        {
            $exam       =   Exam::find($id);
            if(is_null($exam))
            {
                throw new \Exception('没有找到该考试');
            }


            $user   =   Auth::user();
            Cache::pull('plan_'.$exam->id.'_'.$user->id);
            Cache::pull('plan_time_'.$exam->id.'_'.$user->id);

            if($exam->sequence_mode==1)
            {
                $ExamPlanModel =   new ExamPlanForRoom();
                $plan   =   $ExamPlanModel    ->  IntelligenceEaxmPlan($exam);
            }
            else
            {
                $ExamPlanModel   =   new ExamPlan();
                Cache::pull('plan_station_student_'.$exam->id.'_'.$user->id);
                $plan   =   $ExamPlanModel   ->  IntelligenceEaxmPlan($exam);

                $timeList   =   Cache::rememberForever('plan_station_student_'.$exam->id.'_'.$user->id,function() use ($ExamPlanModel){
                    return $ExamPlanModel->getStationStudent();
                });
                $timeList   =   Cache::rememberForever('plan_time_'.$exam->id.'_'.$user->id,function() use ($ExamPlanModel){
                    return $ExamPlanModel->getTimeList();
                });
            }

            $plan = Cache::rememberForever('plan_'.$exam->id.'_'.$user->id, function() use($plan) {
                return $plan;
            });
            return response()->json(
                $this->success_data($plan)
            );
        }
        catch (\Exception $ex)
        {
            return response()->json(
                $this->fail($ex)
            );
        }
    }

    /**
     * 智能排考着陆页
     * @url GET /osce/admin/exam/intelligence
     * @access public
     *
     * @param Request $request
     * <b>get请求字段：</b>
     * * string        id        考试ID(必须的)
     *
     * @return View {'id':$exam->id}
     *
     * @version 1.0
     * @author Luohaihua <Luohaihua@misrobot.com>
     * @date 2015-12-29 17:09
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     *
     */
    public function getIntelligence(Request $request){
        $this->validate($request,[
            'id'    =>  'required|integer'
        ]);

        $id         =   $request    ->  get('id');

        $exam       =   Exam::find($id);
        if(is_null($exam))
        {
            throw new \Exception('没有找到该考试');
        }
        $ExamPlanModel  =   new ExamPlan();
        try {
            $plan   =   $ExamPlanModel  ->  showPlans($exam);
        } catch (\Exception $ex) {
            if ($ex->getCode() == 9999) {
                $user   =   Auth::user();
                $plan = [];
                return view('osce::admin.examManage.smart_assignment',['exam'=>$exam,'plan'=>$plan])->withErrors($ex->getMessage());
            }
        }
//        $plan   =   $this           ->  getEmptyTime($plan);
        $user   =   Auth::user();
        //如果$plan为空，就判断该考试在临时表中是否有数据

        try {
            if (count($plan) == 0) {
                if (ExamPlanRecord::where('exam_id', $id)->first()) {
                    $app = new App();
                    $smartArrangeRepository = new SmartArrangeRepository($app);
                    $plan = $smartArrangeRepository->output($exam);
                    return view('osce::admin.examManage.smart_assignment', ['exam' => $exam, 'plan' => $plan])->withErrors('当前排考计划没有保存！');
                } else {
                    $plan = [];
                }
            }
        } catch (\Exception $ex) {
            return view('osce::admin.examManage.smart_assignment',['exam'=>$exam,'plan'=>$plan])->withErrors($ex->getMessage());
        }
        return view('osce::admin.examManage.smart_assignment',['exam'=>$exam,'plan'=>$plan]);
    }

    /**
     * 保存当前智能排考方案
     * @url POST /osce/admin/exam/intelligence
     * @access public
     *
     * <b>get请求字段：</b>
     * * string        id        考试ID(必须的)
     *
     * @return void
     *
     * @version 1.0
     * @author Luohaihua <Luohaihua@misrobot.com>
     * @date 2015-12-29 17:09
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     *
     */
    public function postIntelligence(Request $request){
        $this->validate($request,[
            'id'    =>  'required|integer'
        ]);
    }

    /**
     * 交换考生
     * @url GET /osce/admin/exam/change-student
     * @access public
     *
     * @param Request $request
     * <b>get请求字段：</b>
     * * string        first        交换第第一个学生(必须的) e.g:场次ID-批次序号-考场ID-考站ID-学生ID
     * * string        second       被交换学生(必须的)
     * * string        exam_id      考试ID(必须的)
     *
     * @return json
     *
     * @version 1.0
     * @author Luohaihua <Luohaihua@misrobot.com>
     * @date 2015-12-29 17:09
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     *
     */
    public function getChangeStudent(Request $request){
        $this->validate($request,[
            'first'     =>  'required',
            'second'    =>  'required',
            'exam_id'   =>  'required',
        ]);
        $first  =   e($request->get('first'));
        $second =   e($request->get('second'));
        $id     =   e($request->get('exam_id'));
        try{
            $exam       =   Exam::find($id);
            $user       =   Auth::user();

            $studentA   =   explode('-',$first);
            $studentB   =   explode('-',$second);
            $studentAInfo   =   [
                'screening_id'  =>  $studentA[0],
                'room_id'       =>  $studentA[1],
                'station_id'    =>  $studentA[2],
                'batch_index'   =>  $studentA[3],
                'student_id'    =>  $studentA[4],
            ];
            $studentBInfo   =   [
                'screening_id'  =>  $studentB[0],
                'room_id'       =>  $studentB[1],
                'station_id'    =>  $studentB[2],
                'batch_index'   =>  $studentB[3],
                'student_id'    =>  $studentB[4],
            ];
            $ExamPlanModel   =   new ExamPlan();
            $redMan =   $ExamPlanModel      ->changePerson($studentAInfo,$studentBInfo,$exam,$user);

            return response()->json(
                $this->success_data(['redmanList'=>$redMan])
            );
        }
        catch(\Exception $ex)
        {
            return response()->json(
                $this->fail($ex)
            );
        }
    }

    public function getExamAssignmentByStation(Request $request)
    {
        //验证
        $this->validate($request, [
            'id' => 'required'
        ]);

        //获取传入的exam_id值
        $id = $request->input('id');

        //将其传入对应的模型查询数据

    }

    /**
     *以考站为中心的考试安排着陆页
     * @url GET /osce/admin/exam/station-assignment
     * @access public
     *
     * @param Request $request
     * <b>get请求字段：</b>
     * * string        参数英文名        参数中文名(必须的)
     * * string        参数英文名        参数中文名(必须的)
     * * string        参数英文名        参数中文名(必须的)
     * * string        参数英文名        参数中文名(必须的)
     * @return \Illuminate\View\View
     * @internal param Teacher $teacher
     * @version 1.0
     * @author Jiangzhiheng <Jiangzhiheng@misrobot.com>
     * @date 2016-01-16
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function getStationAssignment(Request $request)
    {
        $this->validate($request, [
            'id' => 'required|integer'
        ]);

        $exam_id = $request->input('id');


        //展示已经关联的考站和老师列表
        $station = new Station();
        $roomData = $station->stationEcho($exam_id)->groupBy('serialnumber');
        $stationData = $station->stationTeacherList($exam_id)->groupBy('station_id');
//        $invite = new Invite();
        $inviteData = Invite::status($exam_id);

        //将邀请状态插入$stationData
        foreach ($stationData as &$items) {
            foreach ($items as &$item) {
                foreach ($inviteData as $value) {
                    if ($item->teacher_id == $value->invite_user_id) {
                        $item->invite_status = $value->invite_status;
                    } else {
                        $item->invite_status = 0;
                    }
                }
            }
        }
       $status=Exam::where('id',$exam_id)->select('status')->first()->status;

        return view('osce::admin.examManage.exam_station_assignment', [
            'id'          => $exam_id,
            'roomData'    => $roomData,
            'stationData' => $stationData,
            'status'      => $status,
            'getSelect'   => $this->getSelect(),
            'succ'        => $request->get('succ')
        ]);
    }

    /**
     *以考站为中心的考试安排逻辑处理页
     * @url GET /osce/admin/exam/station-assignment
     * @access public
     *
     * @param Request $request
     * <b>get请求字段：</b>
     * * string        参数英文名        参数中文名(必须的)
     * * string        参数英文名        参数中文名(必须的)
     * * string        参数英文名        参数中文名(必须的)
     * * string        参数英文名        参数中文名(必须的)
     *
     * @param ExamFlowStation $examFlowStation
     * @version 1.0
     * @author Jiangzhiheng <Jiangzhiheng@misrobot.com>
     * @date  2016-01-16
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     * @return $this|\Illuminate\Http\RedirectResponse
     */
    public function postStationAssignment(Request $request , ExamFlowStation $examFlowStation)
    {
//        try {
            //验证
            $this->validate($request, [
                'form_data' => 'required|array',
                'id' => 'required|integer'
            ]);

            //获取数据
            $examId = $request->get('id');
            $room = $request->get('room');
            $formData = $request->get('form_data'); //所有的考站数据

            //判断是否有本场考试
            //查看是新建还是编辑
            if (count(ExamFlowStation::where('exam_id',$examId)->get()) == 0) {  //若是为真，就说明是添加
                $examFlowStation -> createExamAssignment($examId, $room, $formData);
            } else { //否则就是编辑
                //如果考试已经开始或者是结束了，就不能允许继续进行了
                if (Exam::findOrFail($examId)->status != 0) {
                    throw new \Exception('当场考试已经开始或已经考完，无法再修改！');
                }
                $examFlowStation -> updateExamAssignment($examId, $room, $formData);
            }

            return redirect()->route('osce.admin.exam.getStationAssignment',['id'=>$examId, 'succ'=>1]);
//        } catch (\Exception $ex) {
//            return redirect()->back()->withErrors($ex->getMessage());
//        }
    }

    /**
     * 保存智能排考计划
     * @url /osce/admin/exam/save-exam-plan
     * @access public
     *
     * * @param Request $request
     * <b>get 请求字段：</b>
     * * string        exam_id        考试ID(必须的)
     *
     * @return view
     *
     * @version 1.0
     * @author Luohaihua <Luohaihua@misrobot.com>
     * @date ${DATE}${TIME}
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     *
     */
    public function postSaveExamPlan(Request $request){
        $this->validate($request, [
            'exam_id' => 'required|integer'
        ]);
        $exam_id    =   $request    ->  get('exam_id');
        $exam   =   Exam::find($exam_id);
        $user   =   Auth::user();
        $plan   =   Cache::get('plan_'.$exam->id.'_'.$user->id);
        $ExamPlanModel  =   new ExamPlan();
//
        try{
            if($ExamPlanModel  ->savePlan($exam_id,$plan))
            {
                return redirect()->route('osce.admin.exam.getIntelligence',['id'=>$exam->id]);
            }
        }
        catch(\Exception $ex)
        {
            return redirect()->back()->withErrors($ex->getMessage());
        }
    }


    /**
     * 用ajax的方式返回考站数据
     * @url GET /osce/admin/exam/ajax-station
     * @access public
     * @param Request $request
     * <b>get请求字段：</b>
     * id    考试id
     * @return void
     * @version 1.0
     * @author Jiangzhiheng <Jiangzhiheng@misrobot.com>
     * @date  2016-01-18
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     *
     */
    public function getAjaxStation(Request $request)
    {
        $this->validate($request, [
            'station_id' => 'sometimes|array'
        ]);

        $stationIds = $request->get('station_id');
        $stationIds = empty($stationIds) ? [] : $stationIds;
        //是用ajax返回
        $ajax = true;
        //在模型里查询
        $station = new Station();
        $data = $station->showList($stationIds, $ajax);

        return $this->success_data($data);
    }

    /**
     * 用ajax的方式返回一条考站数据
     * @url GET /osce/admin/exam/ajax-station-row
     * @access public
     * @param Request $request
     * <b>get请求字段：</b>
     * id    考试id
     * @return void
     * @version 1.0
     * @author Jiangzhiheng <Jiangzhiheng@misrobot.com>
     * @date  2016-01-18
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     *
     */
    public function getAjaxStationRow(Request $request)
    {
        $this->validate($request, [
            'id' => 'required|integer'
        ]);

        $id = $request->input('id');
        //获得考站的信息
        $data = Station::where('id',$id)->get();

        return $this->success_data($data);

    }
    /**
     * 下载学生导入模板
     * @url GET /osce/admin/exam/download-student-improt-tpl
     * @access public
     *
     *
     * @return void
     *
     * @version 1.0
     * @author Luohaihua <Luohaihua@misrobot.com>
     * @date 2015-12-29 17:09
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     *
     */
    public function getdownloadStudentImprotTpl(){
        $this->downloadfile('student.xlsx',public_path('download').'/student.xlsx');
    }

    private function downloadfile($filename,$filepath){
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename='.basename($filename));
        header('Content-Transfer-Encoding: binary');
        header('Expires: 0');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Pragma: public');
        header('Content-Length: ' . filesize($filepath));
        readfile($filepath);
    }

    /**
     * @url  GET /osce/admin/exam/exam-waiting-area
     * 代考区说明
     */
    public function getExamRemind(Request $request){
        //验证
        $this->validate($request, [
            'id' => 'required|integer'
        ]);

        //获得exam_id
        $id = $request->input('id');
        $suc= $request->get('suc');
        $data = Exam::where('id',$id)->select(['rules','status'])->first();
        return view('osce::admin.examManage.exam_waiting_area', ['id'=>$id, 'data'=>$data, 'suc'=>$suc]);
    }

    public function postExamRemind(Request $request)
    {
        $this->validate($request,[
            'content'  => 'required',
            'id'       => 'required|integer'
        ],[
            'content.required'  => '说明内容必填'
        ]);

        try{
            $content = $request->get('content');
            $id      = $request->get('id');
            //保存代考区说明信息
            $result  = Exam::where('id',$id)->update(['rules'  => $content]);
            if($result){
                return redirect()->route('osce.admin.exam.getExamRemind',['id'=>$id,'suc'=>1]);
            }else{
                throw new \Exception('保存失败！');
            }

        } catch(\Exception $ex){
            return redirect()->back()->withErrors($ex->getMessage());
        }
    }

    private function getEmptyTime($plan){
        $data   =   [];
        foreach($plan as $scringId=>$scring)
        {
            $scringData =   [];
            foreach($scring as $key=>$item)
            {
                //dd($item);
                $itemData   =   [
                    'name'  =>  $item['name'],
                    'child' =>  []
                ];
                $end    =   0;
                foreach($item['child'] as $child)
                {
                    if($end!=0)
                    {
                        if($child['start']>$end+10)
                        {
                            $emptyItem  =   [
                                "start" => $end,
                                "end"   => $child['start'],
                                'items' =>  [],
                            ];
                            $itemData['child'][]=$emptyItem;
                        }
                    }
                    $itemData['child'][]    =   $child;
                    $end    =   $child['end'];
                }

                $scringData[$key]   =   $itemData;
            }
            $data[$scringId]        =   $scringData;
        }
        return $data;
    }

    /**
     * 展示考试组成的方法
     * @author Jiangzhiheng
     * @param $examId
     * @return string
     * @throws \Exception
     */
    private function getExamConstitute ($examId) {
        try {
            $tempString = '';
            $tempType1 = 0;
            $tempType2 = 0;
            $tempType3 = 0;
            $temp = StationTeacher::where('exam_id',$examId)->groupBy('station_id')->get();
            if (!$temp->isEmpty()) {
                //获得每个考站数据的type
                foreach ($temp as $item) {
                    switch ($item->station->type) {
                        case 1:
                            $tempType1 = $tempType1+1;
                            break;
                        case 2:
                            $tempType2 = $tempType2+1;
                            break;
                        case 3:
                            $tempType3 = $tempType3+1;
                            break;
                        default:
                            throw new \Exception('系统错误，请重试');
                    }
                }
                if ($tempType1 != 0) {
                    $tempString .= $tempType1 . '技能站';
                }
                if ($tempType2 != 0) {
                    $tempString .= '+' . $tempType2 . 'sp站';
                }
                if ($tempType3 != 0) {
                    $tempString .= '+' . $tempType3 . '理论站';
                }

                //如果字符串开头为+号，则替换掉
                if (strpos($tempString,'+') === 0) {
                    $tempString = substr($tempString,1);
                }

                return $tempString;
            }
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    /**
     *考生查询详情页
     * @method GET
     * @url /exam/check-student
     * @access public
     *
     * @param Request $request post请求<br><br>
     * <b>post请求字段：</b>
     * * string        参数英文名        参数中文名(必须的)
     * * string        参数英文名        参数中文名(必须的)
     * * string        参数英文名        参数中文名(必须的)
     * * string        参数英文名        参数中文名(必须的)
     *
     * @return ${response}
     *
     * @version 1.0
     * @author zhouchong <zhouchong@misrobot.com>
     * @date ${DATE} ${TIME}
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function getCheckStudent(Request $request){
        $this   ->  validate($request,[
            'id'            =>  'required',
        ]);

        $id =   $request    ->  get('id');
        $student    =   Student::find($id);

        return view('osce::admin.examManage.examinee_query_detail', ['item' => $student]);
    }

    /**
     * 判断准考证号是否已经存在
     * @url POST /osce/admin/exam/exam-sequence-unique
     * @author zhouchong <zhouchong@misrobot.com>     *
     */
    public function postExamSequenceUnique(Request $request)
    {
        $this->validate($request,[
            'exam_id'        => 'required',
            'exam_sequence'  => 'sometimes',
            'mobile'         => 'sometimes',
            'idcard'         => 'sometimes',
            'code'           => 'sometimes',
            'id'             => 'sometimes',
        ]);
        $examId      = $request->input('exam_id');
        $examSequence= $request->input('exam_sequence');
        $mobile      = $request->input('mobile');
        $idcard      = $request->input('idcard');
        $code        = $request->input('code');
        $studentId   = $request->input('id');
        //根据条件判断
        if(!empty($mobile)){
            $userU = User::where('username','=',$mobile)->first();
            if (is_null($userU)){
                $userM = User::where('mobile','=',$mobile)->first();
                if (!is_null($userM)){
                    return json_encode(['valid' =>false]);
                }
            }
            $where = ['exam_id'=>$examId, 'mobile' => $mobile];

        }elseif(!empty($idcard)){
            $where = ['exam_id'=>$examId, 'idcard' => $idcard];
        }elseif(!empty($code)){
            $where = ['exam_id'=>$examId, 'code' => $code];
        }elseif(!empty($examSequence)){
            $where = ['exam_id'=>$examId, 'exam_sequence' => $examSequence];
        }else{
            return json_encode(['valid' =>false]);
        }
        if(empty($studentId)){
            $result = Student::where($where)->first();
        }else{
            $result = Student::where($where)->where('id', '<>', $studentId)->first();
        }
        //是否已存在
        if($result){
            return json_encode(['valid' =>false]);      //存在
        }else{
            return json_encode(['valid' =>true]);
        }
    }


}