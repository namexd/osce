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
use Modules\Osce\Entities\Exam;

use Modules\Osce\Entities\ExamFlow;
use Modules\Osce\Entities\ExamFlowRoom;
use Modules\Osce\Entities\ExamFlowStation;
use Modules\Osce\Entities\ExamRoom;
use Modules\Osce\Entities\ExamScreening;
use Modules\Osce\Entities\Flows;
use Modules\Osce\Entities\Room;
use Modules\Osce\Entities\ExamScreeningStudent;
use Modules\Osce\Entities\ExamSpTeacher;
use Modules\Osce\Entities\RoomStation;
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
        $this->validate($request,[
            'exam_name' =>'sometimes'
        ]);

        $formData = $request->only('exam_name');

        //从模型得到数据
        $data = $exam->showExamList($formData);

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
            $id = $request->input('id');  //id为考试id

            //开启事务
            DB::beginTransaction();
            //进入模型逻辑
            //删除与考场相关的流程
            $flowIds = ExamFlow::where('exam_id',$id)->select('flow_id')->get(); //获得流程的id
            $examScreening = ExamScreening::where('exam_id',$id);


            //删除考试考场学生表
                foreach ($examScreening->select('id')->get() as $item) {
                    if (count(ExamScreeningStudent::where('exam_screening_id',$item->id)->get()) != 0) {
                        if (!ExamScreeningStudent::where('exam_screening_id',$item->id)->delete()) {
                            throw new \Exception('删除考试考场学生关系表失败，请重试！');
                        }
                    }
                }

            //删除考试考场关联表
            if (count($examScreening-> get())) {
                if (!$examScreening-> first() ->delete()) {
                    throw new \Exception('删除考试考场关系表失败，请重试！');
                }
            }

            //删除考试考场关联
            if (ExamRoom::where('exam_id',$id)->first()) {
                if (!ExamRoom::where('exam_id',$id)->delete()) {
                    throw new \Exception('删除考试考场关联失败，请重试！');
                }
            }


            //删除考试流程关联
            if (ExamFlow::where('exam_id',$id)->first()) {
                if (!ExamFlow::where('exam_id',$id)->delete()) {
                    throw new \Exception('删除考试流程关联失败，请重试！');
                }
            }

            //删除考试考场流程关联
            if (ExamFlowRoom::where('exam_id',$id)->first()) {
                if (!ExamFlowRoom::where('exam_id',$id)->delete()) {
                    throw new \Exception('删除考试考场流程关联失败，请重试！');
                }
            }

            //通过考试流程-考站关系表得到考站信息

            $station = ExamFlowStation::whereIn('flow_id',$flowIds);
            $stationIds = $station->select('station_id')->get();
            if (count($stationIds) != 0) {
                //删除考试流程-考站关系表信息
                if (!$station->delete()) {
                    throw new \Exception('删除考试考站流程关联失败，请重试！');
                }

                //通过考站id找到对应的考站-老师关系表
                foreach ($stationIds as $stationId) {
                    if (!StationTeacher::where('station_id',$stationId)->delete()) {

                        throw new \Exception('删除考站老师关联失败，请重试！');
                    }
                }

            }

            //删除考试本体
            $result = $exam->where('id',$id)->delete();
            if ($result != true) {
                throw new \Exception('删除考试失败，请重试！');
            }



            //如果有flow的话，就删除
            if (count($flowIds) != 0) {
                foreach ($flowIds as $flowId) {
                    if (!Flows::where('id',$flowId)->delete()) {
                        throw new \Exception('删除流程失败，请重试！');
                    }
                }
            }

            DB::commit();
            return response()->json($this->success_data(['删除成功！']));
        } catch (\Exception $ex) {
            DB::rollback();
            return response()->json($this->fail($ex));
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

        $examScreeningData =  $request  ->  get('time');
        //考试的最早时间（开始时间）、最晚时间（结束时间）
        $begin_dt = '';
        $end_dt = '';

        //判断输入的时间是否有误
        foreach($examScreeningData as $key => $value){
            if(!strtotime($value['begin_dt']) || !strtotime($value['end_dt'])){
                throw new \Exception('输入的时间有误！');
            }
            //获取第一组时间数据
            if($key == 1){
                $begin_dt = $value['begin_dt'];
                $end_dt = $value['end_dt'];
            }
            //获取最早开始时间，最晚结束时间
            if($key>1 && (strtotime($begin_dt) > strtotime($value['begin_dt']))){
                $begin_dt = $value['begin_dt'];
            }
            if($key>1 && (strtotime($end_dt) < strtotime($value['end_dt']))){
                $end_dt = $value['end_dt'];
            }
            $examScreeningData[$key]['create_user_id'] = $user -> id;
        }
        //处理相应信息,将$request中的数据分配到各个数组中,待插入各表
        $examData = [
            'name'           => $request  ->  get('name'),
            'begin_dt'       => $begin_dt,
            'end_dt'         => $end_dt,
            'status'         => 1,
            'create_user_id' => $user     ->  id
        ];

        try{
            if($exam = $model -> addExam($examData, $examScreeningData))
            {
                return redirect()->route('osce.admin.exam.getExamList');
            } else {
                throw new \Exception('新增考试失败');
            }
        } catch(\Exception $ex) {
            return response()->back()->withError($ex->getMessage());
        }
    }

    /**
     * 编辑考试基本信息表单页面
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

        //获得exam_id
        $id = $request->input('id');
        //通过id查到该条信息
        try {
            $examData = Exam::findOrFail($id);
            $examScreeningData = ExamScreening::where(['exam_id' => $id])->get();

            return view('osce::admin.exammanage.add_basic',['id'=>$id, 'examData'=>$examData, 'examScreeningData'=>$examScreeningData]);
        } catch (\Exception $ex) {
            return redirect()->back()->withErrors($ex);
        }
    }

    /**
     * 保存编辑考试基本信息
     * @api POST /osce/admin/exam/postEditExam
     * @access public
     *
     * @param Request $request post请求<br><br>
     * <b>post请求字段：</b>
     * * string        exam_id        考试id(必须的)
     * * string        name           考试名(必须的)
     * * string        begin_dt       开始时间(必须的)
     * * string        end_dt         结束时间(必须的)
     *
     * @return redirect
     *
     * @version 1.0
     * @author Zhoufuxiang <Zhoufuxiang@misrobot.com>
     * @date ${DATE} ${TIME}
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     *
     */
    public function postEditExam(Request $request, Exam $exam)
    {
        //验证,略过

        //处理相应信息,将$request中的数据分配到各个数组中,待插入各表
        $exam_id = $request->input('exam_id');
        $examScreeningData = $request -> get('time');

        //查询操作人信息
        $user = Auth::user();
        if (empty($user)) {
            throw new \Exception('未找到当前操作人信息');
        }
        //考试的最早时间（开始时间）、最晚时间（结束时间）
        $begin_dt = '';
        $end_dt = '';

        //判断输入的时间是否有误
        foreach($examScreeningData as $key => $value){
            if(!strtotime($value['begin_dt']) || !strtotime($value['end_dt'])){
                throw new \Exception('输入的时间有误！');
            }
            //获取第一组时间数据
            if($key == 0){
                $begin_dt = $value['begin_dt'];
                $end_dt = $value['end_dt'];
            }
            //获取最早开始时间，最晚结束时间
            if($key>0 && strtotime($begin_dt) > strtotime($value['begin_dt'])){
                $begin_dt = $value['begin_dt'];
            }
            if($key>0 && strtotime($end_dt) < strtotime($value['end_dt'])){
                $end_dt = $value['end_dt'];
            }
            $examScreeningData[$key]['create_user_id'] = $user -> id;
        }
        //处理相应信息,将$request中的数据分配到各个数组中,待插入各表
        $examData = [
            'name'           => $request  ->  get('name'),
            'begin_dt'       => $begin_dt,
            'end_dt'         => $end_dt,
            'sequence_cate'  => $request  ->  get('sequence_cate'),
            'sequence_mode'  => $request  ->  get('sequence_mode'),
        ];

        try{
            if($exam = $exam -> editExam($exam_id, $examData, $examScreeningData))
            {
                return redirect()->route('osce.admin.exam.getEditExam', ['id'=>$exam_id]);
            } else {
                throw new \Exception('修改考试失败');
            }
        } catch(\Exception $ex) {
            throw $ex;
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
     * * string        keyword        关键字(姓名，学号，身份证号，电话)
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
        //验证规则
        $this->validate($request, [
            'id' => 'required|integer'
        ]);

        try {
            //获取id
            $exam_id = $request->input('id');
            //获取各字段
            $keyword = $request->only('keyword');

            $student = new Student();
            //从模型得到数据
            $data = $student->selectExamStudent($exam_id, $keyword);

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
     * @api GET /osce/admin/exam/add-examinee
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
     * @api post /osce/admin/exam/add-examinee
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
     * '
     */
    public function postAddExaminee(Request $request, Student $model)
    {
        $this   ->  validate($request,[
            'exam_id'       =>  'required',
            'name'          =>  'required',
            'idcard'        =>  'required',
            'tell'          =>  'required',
            'images_path'   =>  'required',
        ],[
            'name.required'         =>  '姓名必填',
            'idcard.required'       =>  '身份证号必填',
            'tell.required'         =>  '手机号必填',
            'images_path.required'  =>  '请上传照片',
        ]);

        //考试id
        $exam_id = $request->get('exam_id');
        //考生数据
        $examineeData = [
            'name'           => $request  ->  get('name'),          //姓名
            'gender'         => $request  ->  get('sex'),           //性别
            'idcard'         => $request  ->  get('idcard'),        //身份证号
            'mobile'         => $request  ->  get('tell'),          //手机号
            'code'           => $request  ->  get('examinee_id'),   //学号
            'avatar'         => $request  ->  get('images_path')[0],//照片
            'email'          => $request  ->  get('email'),         //邮箱
        ];

        try{
            if($exam = $model -> addExaminee($exam_id, $examineeData))
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
     * Excel导入考生
     * @api GET /osce/admin/exam/getImportStudent
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
     *
     */
    public function getImportStudent(Request $request){

        $exam_id = $request->get('id');
        return view('osce::admin.exammanage.import',['id' => $exam_id]);
    }


    public function postImportStudent($id,Request $request, Student $student)
    {
        try {

            //获得上传的数据
            $exam_id= $id;
            $data = Common::getExclData($request, 'student');
            //去掉sheet
            $studentList = array_shift($data);
            //将中文表头转为英文
            $examineeData = Common::arrayChTOEn($studentList, 'osce.importForCnToEn.student');

            //将数组导入到模型中的addInvigilator方法
            foreach($examineeData as $studentData)
            {
                if (!$student->addExaminee($exam_id, $studentData))
                {
                    throw new \Exception('学生导入数据失败，请稍后重试');
                }


            }
//               echo json_encode(['result' => true, 'data' =>['code'=>1] ]);
            echo json_encode($this->success_data(['code'=>1]));
        } catch (\Exception $ex) {
            echo json_encode($this->fail($ex));
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
     * @author Zhoufuxiang <Zhoufuxiang@misrobot.com>  zhouchong <Zhouchong@misrobot.com>
     * @date ${DATE} ${TIME}
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function getStudentQuery(Request $request)
    {
        //验证规则，暂时留空
        $this   ->    validate($request,[
              'exam_name'      => 'sometimes',
              'student_name'   => 'sometimes',
        ]);
        //获取各字段
        $formData = $request->only('exam_name', 'student_name');
        //获取当前场所的类
         $examModel= new Exam();
        //从模型得到数据
        $data=$examModel->getList($formData);
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

    /**
     * 考场安排
     * @api GET /osce/admin/exam/getExamroomAssignment
     * * string        参数英文名        参数中文名(必须的)
     *
     * @param Request $request
     * @return object
     * @version 1.0
     * @author Zhoufuxiang <Zhoufuxiang@misrobot.com>
     * @date ${DATE} ${TIME}
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function getExamroomAssignment(Request $request, Exam $exam)
    {
        $this->validate($request,[
            'id' => 'required|integer'
        ]);
        $exam_id = $request -> get('id');
        $examRoom = new ExamRoom();
        //获取考试id对应的考场数据
        $examRoomData = $examRoom -> getExamRoomData($exam_id);
//        dd($examRoomData->all());
        //获取考试对应的考站数据
        $examStationData = $examRoom -> getExamStation($exam_id);
//        dd($examRoomData->all(),$examStationData->all());
        return view('osce::admin.exammanage.examroom_assignment', ['id' => $exam_id, 'examRoomData' => $examRoomData, 'examStationData' => $examStationData]);
    }

    /**
     * 保存 考场安排数据
     * @api POST /osce/admin/exam/postExamroomAssignmen
     * @access public
     *
     * @param Request $request post请求<br><br>
     * <b>post请求字段：</b>
     * * string        参数英文名        参数中文名(必须的)
     *
     * @return redirect
     *
     * @version 1.0
     * @author Zhoufuxiang <Zhoufuxiang@misrobot.com>
     * @date ${DATE} ${TIME}
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     *
     */
    public function postExamroomAssignmen(Request $request)
    {
        try{
            DB::beginTransaction();
            //处理相应信息,将$request中的数据分配到各个数组中,待插入各表
            $exam_id        = $request  ->  get('id');       //考试id
            $roomData       = $request  ->  get('room');        //考场数据
            $stationData    = $request  ->  get('station');     //考站数据
//            dd($request->all());
//            $flows = new Flows();
//            if(!$flows -> saveExamroomAssignmen($exam_id, $roomData, $stationData)) {
//                throw new \Exception('考场安排保存失败，请重试！');
//            }
            //查询 考试id是否有对应的考场数据
            $examRoom = new ExamRoom();
            $examRoomData = $examRoom -> getExamRoomData($exam_id);
            //判断是否存在已有数据
            $flows = new Flows();
            if(count($examRoomData) != 0){
                if(!$flows -> editExamroomAssignmen($exam_id, $roomData, $stationData)){
                    DB::rollback();
                    throw new \Exception('考场安排保存失败，请重试！');
                }

            }else{
                if(!$flows -> saveExamroomAssignmen($exam_id, $roomData, $stationData)){
                    DB::rollback();
                    throw new \Exception('考场安排保存失败，请重试！');
                }
            }
            DB::commit();
            return redirect()->route('osce.admin.exam.getExamroomAssignment', ['id'=>$exam_id]);

        } catch(\Exception $ex){
            return redirect()->back()->withErrors($ex->getMessage());
        }

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
        $data = Room::select(['id', 'name'])->get();

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
     *
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
     *
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
     * @return void
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

        $exam       =   Exam::find($id);
        if(is_null($exam))
        {
            throw new \Exception('没有找到该考试');
        }
        $ExamPlanModel   =   new ExamPlan();
        $plan   =   $ExamPlanModel   ->  IntelligenceEaxmPlan($exam);
        $user   =   Auth::user();
        Cache::pull('plan_'.$exam->id.'_'.$user->id);
        $plan = Cache::rememberForever('plan_'.$exam->id.'_'.$user->id, function() use($plan) {
            return $plan;
        });
        return response()->json(
            $this->success_data($plan)
        );
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
        return view('osce::admin.exammanage.smart_assignment',['exam'=>$exam]);
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
     *
     * @url GET /osce/admin/exam/change-student
     * @access public
     *
     * @param Request $request
     * <b>get请求字段：</b>
     * * string        参数英文名        参数中文名(必须的)
     * * string        参数英文名        参数中文名(必须的)
     * * string        参数英文名        参数中文名(必须的)
     * * string        参数英文名        参数中文名(必须的)
     *
     * @return void
     *
     * @version 1.0
     * @author Luohaihua <Luohaihua@misrobot.com>
     * @date 2015-12-29 17:09
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     *
     */
    public function getChangeStudent(Request $request){
        $id =   17;
        $exam       =   Exam::find($id);
        $user       =   Auth::user();
        $fist   ='17-3-0-3';
        $sec    ='17-3-1-1';
        $studentA   =   explode('-',$fist);
        $studentB   =   explode('-',$sec);
        $studentAInfo   =   [
            'screening_id'  =>  $studentA[0],
            'room_id'       =>  $studentA[1],
            'batch_index'   =>  $studentA[2],
            'student_id'    =>  $studentA[3],
        ];
        $studentBInfo   =   [
            'screening_id'  =>  $studentB[0],
            'room_id'       =>  $studentB[1],
            'batch_index'   =>  $studentB[2],
            'student_id'    =>  $studentB[3],
        ];
        $ExamPlanModel   =   new ExamPlan();
        $ExamPlanModel      ->changePerson($studentAInfo,$studentBInfo,$exam,$user);
    }
}