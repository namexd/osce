<?php
/**
 * Created by PhpStorm.
 * User: wangjiang
 * Date: 2015/12/14 0014
 * Time: 18:17
 */
namespace Modules\Msc\Http\Controllers\Admin;

use App\Repositories\Common;
use Illuminate\Http\Request;
use Modules\Msc\Entities\Student;
use Modules\Msc\Entities\Teacher;
use Modules\Msc\Entities\StdProfessional;
use Modules\Msc\Entities\TeacherDept;
use Modules\Msc\Http\Controllers\MscController;
use Symfony\Component\HttpFoundation\Response;

class UserController extends MscController
{
    /**
     * 学生列表
     * @method GET
     * @url /msc/admin/user/student-list
     * @access public
     *
     * @param Request $request get请求<br><br>
     * <b>get请求字段：</b>
     * * string        keyword         关键字
     * * int           status          学生状态(1:正常 2:禁用 3:删除)
     * * int           grade           学生年级
     * * int           student_type    学生类别(1:本科 2:专科)
     * * int           profession      学生专业id
     *
     * @return view
     *
     * @version 0.8
     * @author wangjiang <wangjiang@sulida.com>
     * @date 2015-12-14 18:29
     * @copyright  2013-2017 sulida.com  Inc. All Rights Reserved
     */
    public function getStudentList(Request $request)
    {
        $this->validate($request, [
            'keyword'      => 'sometimes', // TODO 查询关键字约束
            'status'       => 'sometimes|in:1,2,3',
            'grade'        => 'sometimes|integer',
            'student_type' => 'sometimes|in:1,2',
            'profession'   => 'sometimes|integer',
        ]);

        $keyword     = urldecode(e($request->input('keyword')));
        $status      = (int) $request->input('status');
        $grade       = (int) $request->input('grade');
        $studentType = (int) $request->input('student_type');
        $profession  = (int) $request->input('profession');

        $student    = new Student();
        $pagination = $student->getFilteredPaginateList($keyword, $status, $grade, $studentType, $profession);

        $list = [];
        foreach ($pagination as $item) {
            $list[] = [
                'id'              => $item->id,
                'name'            => $item->name,
                'code'            => $item->code,
                'grade'           => $item->grade,
                'student_type'    => $item->student_type,
                'profession_name' => is_null($item->professionalName) ? '-' : $item->professionalName->name,
                'mobile'          => is_null($item->userInfo) ? '-' : $item->userInfo->mobile,
                'idcard'          => is_null($item->userInfo) ? '-' : $item->userInfo->idcard,
                'gender'          => is_null($item->userInfo) ? '-' : $item->userInfo->gender,
                'status'          => is_null($item->userInfo) ? '-' : $item->userInfo->status,
            ];
        }

        // 年级列表
        $gradeList = $student->getGradeList();

        // 学生类别
        $studentTypeList = config('msc.student_type');

        // 用户状态
        $studentStatusList = config('msc.user_status');

        // 专列列表
        $professionList = StdProfessional::get();

        return view('msc::admin.usermanage.student_manage', [
            'list'              => $list,
            'gradeList'         => $gradeList,
            'studentTypeList'   => $studentTypeList,
            'studentStatusList' => $studentStatusList,
            'professionList'    => $professionList,
            'pagination'        => $pagination,
        ]);

    }

    /**
     * 查看学生
     * @method GET
     * @url /msc/admin/user/student-item/{id}
     * @access public
     *
     * @param Request $request get请求<br><br>
     * <b>get请求字段：</b>
     * * int        $id        学生编号
     *
     * @return json
     *
     * @version 0.8
     * @author wangjiang <wangjiang@sulida.com>
     * @date 2015-12-15 10:58
     * @copyright  2013-2017 sulida.com  Inc. All Rights Reserved
     */
    public function getStudentItem($id)
    {
        $studentId = intval($id);

        $student = Student::findOrFail($studentId);

        $data = [
            'id'              => $student->id,
            'name'            => $student->name,
            'code'            => $student->code,
            'grade'           => $student->grade,
            'student_type'    => $student->student_type,
            'profession_name' => $student->professionalName->name,
            'mobile'          => $student->userInfo->mobile,
            'idcard'          => $student->userInfo->idcard,
            'gender'          => $student->userInfo->gender,
            'status'          => $student->userInfo->status,
        ];

        die(json_encode($data));
    }

    /**
     * 老师列表
     * @method GET
     * @url /msc/admin/user/teacher-list
     * * string        keyword           关键字
     * * int           status            学生状态(1:正常 2:禁用 3:删除)
     * * int           teacher_dept      科室id
     *
     * @return view
     *
     * @version 0.8
     * @author wangjiang <wangjiang@sulida.com>
     * @date 2015-12-15 11:32
     * @copyright  2013-2017 sulida.com  Inc. All Rights Reserved
     */
    public function getTeacherList(Request $request)
    {
        $this->validate($request, [
            'keyword'      => 'sometimes', // TODO 查询关键字约束
            'status'       => 'sometimes|in:1,2,3',
            'teacher_dept' => 'sometimes|integer',
        ]);

        $keyword     = urldecode(e($request->input('keyword')));
        $status      = (int) $request->input('status');
        $teacherDept = (int) $request->input('teacher_dept');

        $teacher    = new Teacher();
        $pagination = $teacher->getFilteredPaginateList($keyword, $status, $teacherDept);

        $list = [];
        foreach ($pagination as $item) {
            $list[] = [
                'id'        => $item->id,
                'name'      => $item->name,
                'code'      => $item->code,
                'dept_name' => is_null($item->dept) ? '-' : $item->dept->name,
                'mobile'    => is_null($item->userInfo) ? '-' : $item->userInfo->mobile,
                'gender'    => is_null($item->userInfo) ? '-' : $item->userInfo->gender,
                'status'    => is_null($item->userInfo) ? '-' : $item->userInfo->status,
                'role'      => is_null($item->userInfo) ? [] : $item->userInfo->roles,
            ];
        }

        // 用户状态
        $teacherStatusList = config('msc.user_status');

        // 科室列表
        $deptList = TeacherDept::get();

        return view('msc::admin.usermanage.teacher_manage', [
            'list'              => $list,
            'teacherStatusList' => $teacherStatusList,
            'deptList'          => $deptList,
            'pagination'        => $pagination,
        ]);
    }

    /**
     * 查看老师
     * @method GET
     * @url /msc/admin/user/teacher-item/{id}
     * @access public
     *
     * @param Request $request get请求<br><br>
     * <b>get请求字段：</b>
     * * int        id        老师编号
     *
     * @return json
     *
     * @version 0.8
     * @author wangjiang <wangjiang@sulida.com>
     * @date 2015-12-15 17:30
     * @copyright  2013-2017 sulida.com  Inc. All Rights Reserved
     */
    public function getTeacherItem($id)
    {
        $teacherId = intval($id);

        $teacher = Teacher::findOrFail($teacherId);

        $data = [
            'id'        => $teacher->id,
            'name'      => $teacher->name,
            'code'      => $teacher->code,
            'dept_name' => is_null($teacher->dept) ? '-' : $teacher->dept->name,
            'mobile'    => $teacher->userInfo->mobile,
            'gender'    => $teacher->userInfo->gender,
            'status'    => $teacher->userInfo->status,
            'role'      => $teacher->userInfo->roles,
        ];

        die(json_encode($data));
    }

    /**
     * 编辑学生回显
     * @method GET
     * @url /msc/admin/user/student-edit/{id}
     * @method GET
     * @access public
     *
     * @param Request $request get请求<br><br>
     * <b>get请求字段：</b>
     * * int        $id        学生编号
     *
     * @return bloo
     *
     * @version 0.8
     * @author fandian <fandian@sulida.com>
     * @date 2015-12-15 14:50
     * @copyright  2013-2017 sulida.com  Inc. All Rights Reserved
     */
    public function getStudentEdit($id)
    {

        $studentId = intval($id);


        return $this->getStudentItem($studentId);

    }

    /**
     * 编辑学生
     * @method GET
     * @url /msc/admin/user/student-save
     * @access public
     *
     * @param Request $request get请求<br><br>
     * <b>get请求字段：</b>
     * * int        $id        学生编号
     *
     * @return json
     *
     * @version 0.8
     * @author fandian <fandian@sulida.com>
     * @date 2015-12-15 15:30
     * @copyright  2013-2017 sulida.com  Inc. All Rights Reserved
     */
    public function postStudentSave(Request $request)
    {
        $this->validate($request, [
            'id' => 'required|min:0|max:10',
            'name' => 'required|max:50',
            'code' => 'required|min:0|max:32',
            'gender' => 'required|max:1',
            'grade' => 'required|max:11',
            'student_type' => 'required||max:3',
            'professional_name' => 'required|max:50',
            'mobile' => 'required|max:11',
            'idcard_type' => 'required|max:1',
            'idcard' => 'required|min:0|max:50',
        ]);


        $data = $request->only(['id', 'name', 'code', 'gender', 'grade', 'student_type', 'professional_name', 'mobile', 'idcard_type', 'idcard']);

//        dd($data['id']);

        $studentModel = new Student();

        $result = $studentModel->saveEditStudent($data);

        if ($result) {
            return redirect()->back()->withInput()->withErrors('修改成功');

        }
            return redirect()->back()->withInput()->withErrors('修改成功');

    }

    /**
     * 添加学生信息
     * @method POST
     * @url /msc/admin/user/student-add
     * @access public
     *
     * @param Request $request post请求<br><br>
     * <b>post数据：</b>
     * *
     *
     * @return json
     *
     * @version 0.8
     * @author fandian <fandian@sulida.com>
     * @date 2015-12-15 16:00
     * @copyright  2013-2017 sulida.com  Inc. All Rights Reserved
     */
    public function postStudentAdd(Request $request,$status=1)
    {
        $this->validate($request, [
            'name' => 'required|max:50',
            'code' => 'required|max:32',
            'gender' => 'required|max:1',
            'grade' => 'required|max:11',
            'student_type' => 'required|max:3',
            'profession_name' => 'required|max:50',
            'mobile' => 'required|max:11',
            'idcard_type' => 'required|max:1',
            'idcard' => 'required|max:50',
        ]);

        $data = $request->only(['name', 'code', 'gender', 'grade', 'student_type', 'profession_name', 'mobile', 'idcard_type', 'idcard']);

        $data['status']=$status;
        $studentModel = new Student();

        $result = $studentModel->postAddStudent($data);

        if ($result) {
            return redirect()->back()->withInput()->withErrors('新增成功');

        }
        return redirect()->back()->withInput()->withErrors('新增失败');

    }

    /**
     * 软删除
     * @method GET
     * @url /msc/admin/user/student-trashed/{id}
     * @access public
     *
     * @param Request $request get请求<br><br>
     * <b>get请求字段：</b>
     * * int        $id        学生编号
     *
     * @return json
     *
     * @version 0.8
     * @author fandian <fandian@sulida.com>
     * @date 2015-12-15 16:30
     * @copyright  2013-2017 sulida.com  Inc. All Rights Reserved
     */
    public function getStudentTrashed($id)
    {
        $id = intval($id);

        $studentModel = new Student();

        $result = $studentModel->SoftTrashed($id);

        if ($result) {
            return response()->json(
                ['success' => true]
            );
        }
        return response()->json(
            ['success' => false]
        );
    }

    /**
     * 更改学生状态
     * @method GET
     * @url /msc/admin/user/student-status/{id}
     * @access public
     *
     * @param Request $request get请求<br><br>
     * <b>get请求字段：</b>
     * * int        $id        学生编号
     *
     * @return blooean
     *
     * @version 0.8
     * @author fandian <fandian@sulida.com>
     * @date 2015-12-15 17:30
     * @copyright  2013-2017 sulida.com  Inc. All Rights Reserved
     */
    public function getStudentStatus($id)
    {

        $studentId = intval($id);

        $studentModel = new Student();

        $result = $studentModel->changeStatus($studentId);

        if ($result) {
            return response()->json(
                ['success' => true]
            );
        }
        return response()->json(
            ['success' => false]
        );
    }

    /**
     * 编辑时教职工回显
     * @method GET
     * @url /msc/admin/user/teacher-edit/{$id}
     * @access public
     *
     * @param Request $request get请求<br><br>
     * <b>get请求字段：</b>
     * * int        $id        学生编号
     *
     * @return blooean
     *
     * @version 0.8
     * @author fandian <fandian@sulida.com>
     * @date 2015-12-15 14:50
     * @copyright  2013-2017 sulida.com  Inc. All Rights Reserved
     */
    public function getTeacherEdit($id)
    {

        $teacherId = intval($id);


        return $this->getTeacherItem($teacherId);

    }

    /**
     * 提交编辑教职工
     * @method GET
     * @url /msc/admin/user/teacher-save
     * @access public
     *
     * @param Request $request get请求<br><br>
     * <b>get请求字段：</b>
     * * int        $id        教师编号
     *
     * @return json
     *
     * @version 0.8
     * @author fandian <fandian@sulida.com>
     * @date 2015-12-15 15:30
     * @copyright  2013-2017 sulida.com  Inc. All Rights Reserved
     */
    public function postTeacherSave(Request $request)
    {

        $this->validate($request, [
            'id' => 'sometimes|min:0|max:10',
            'name' => 'required|max:50',
            'code' => 'required|max:32',
            'gender' => 'required|max:1',
            'dept_name' => 'required|max:20',
            'mobile' => 'required|max:11',
        ]);

        $data = $request->only(['id','name', 'code', 'gender', 'role',  'dept_name','mobile']);

        $teacherModel = new Teacher();

        $result = $teacherModel->saveEditTeacher($data);

        if ($result) {
            return redirect()->back()->withInput()->withErrors('修改成功');

        }
        return redirect()->back()->withInput()->withErrors('修改失败');
    }

    /**
     * 添加教职工信息
     * @method GET
     * @url /msc/admin/user/teacher-add
     * @access public
     *
     * @param Request $request post请求<br><br>
     * <b>post数据：</b>
     * *
     *
     * @return json
     *
     * @version 0.8
     * @author fandian <fandian@sulida.com>
     * @date 2015-12-15 16:00
     * @copyright  2013-2017 sulida.com  Inc. All Rights Reserved
     */
    public function postTeacherAdd(Request $request,$status=1)
    {

        $this->validate($request, [
            'name' => 'required|max:50',
            'code' => 'required|max:32',
            'gender' => 'required|max:1',
            'dept_name' => 'required|max:20',
            'mobile' => 'required|max:11',
        ]);

        $data = $request->only(['name', 'code', 'gender',  'teacher_dept','role','dept_name','mobile']);
        $data['status']=$status;
        $teacherModel = new Teacher();

        $result = $teacherModel->postAddTeacher($data);

        if ($result) {
            return redirect()->back()->withInput()->withErrors('新增成功');

        }
        return redirect()->back()->withInput()->withErrors('新增失败');
    }

    /**
     * 软删除 只是更改状态
     * @method GET
     * @url /msc/admin/user/teacher-trashed/{id}
     * @access public
     *
     * @param Request $request get请求<br><br>
     * <b>get请求字段：</b>
     * * int        $id        教师编号
     *
     * @return json
     *
     * @version 0.8
     * @author fandian <fandian@sulida.com>
     * @date 2015-12-15 16:30
     * @copyright  2013-2017 sulida.com  Inc. All Rights Reserved
     */
    public function getTeacherTrashed($id)
    {
        $id = intval($id);

        $teacherModel = new Teacher();

        $result = $teacherModel->SoftTrashed($id);

        if ($result) {
            return response()->json(
                ['success' => true]
            );
        }
        return response()->json(
            ['success' => false]
        );
    }

    /**
     * 更改教师状态
     * @method GET
     * @url /msc/admin/user/teacher-status/{id}
     * @access public
     *
     * @param Request $request get请求<br><br>
     * <b>get请求字段：</b>
     * * int        $id        教师编号
     *
     * @return josn
     *
     * @version 0.8
     * @author fandian <fandian@sulida.com>
     * @date 2015-12-15 17:30
     * @copyright  2013-2017 sulida.com  Inc. All Rights Reserved
     */
    public function getTeacherStatus($id)
    {

        $teacherId = intval($id);

        $teacherModel = new Teacher();

        $result = $teacherModel->changeStatus($teacherId);

        if ($result) {
            return response()->json(
                ['success' => true]
            );
        }
        return response()->json(
            ['success' => false]
        );
    }

    /**
     * 导入教师用户
     * @api post /msc/admin/user/import-teacher-user
     * @access public
     * @param Request $request post请求<br><br>
     * <b>post请求字段：</b>
     * * string       training       课程文件的excl(必须的)
     * @return object
     * @version 0.8
     * @author zhouqiang <zhouqiang@sulida.com>
     * @date 2015-11-27 10:24
     * @copyright  2013-2017 sulida.com  Inc. All Rights Reserved
     *
     */
    public function  postImportTeacherUser(Request $request)
    {
//        die(json_encode($_FILES ));
        try {
            $data = Common::getExclData($request, 'training');
            $teacherInfo = array_shift($data);
            //将中文头转换翻译成英文
            $teacherInfo = Common::arrayChTOEn($teacherInfo, 'msc.importForCnToEn.teacher_group');

            //已经存在的数据
            $dataHaven = [];
            //添加失败的数据
            $dataFalse = [];
            //判断是否存在这个教师用户
            foreach ($teacherInfo as $teacherData) {

                //处理性别gender
                switch ( $teacherData['gender']){
                    case "男":
                        $teacherData['gender'] = 1;
                        break;
                    case "女":
                        $teacherData['gender'] = 2;
                        break;
                };
                //处理状态status
                switch ( $teacherData['status']){
                    case "正常":
                        $teacherData['status'] = 1;
                        break;
                    case "禁用":
                        $teacherData['status'] = 2;
                        break;
                };
//               //处理角色role
//                switch ( $teacherData['role']){
//                    case "正常":
//                        $teacherData['role'] = 1;
//                        break;
//                    case "禁用":
//                        $teacherData['role'] = 2;
//                        break;
//                };

                if ($teacherData['code'] && $teacherData['name']) {
                    if (Teacher::where('code', '=', $teacherData['code'])->count() == 0) {
//                                die(json_encode($teacherInfo ));
//                        $teacher =Teacher ::create($teacherData);
                        $teacher = new Teacher();
                        $result=$teacher->AddTeacher($teacherData);

                        if ( $result== 0) {
                            $dataFalse[] = $teacherData;
                        }
                    } else {
                        $dataHaven[] = $teacherData;
                    }
                }
            }
            return response()->json(
                $this->success_data(['result' => true, 'dataFalse' => $dataFalse, 'dataHaven' => $dataHaven])
            );
        } catch (\Exception $e) {
            return response()->json($this->fail($e));
        }
    }
    /**
     * 导入学生用户
     * @api GET /msc/admin/User/import-Student-user
     * @access public
     *
     * @param Request $request post请求<br><br>
     * <b>post请求字段：</b>
     * * string       Student      课程文件的excl(必须的)
     *
     * @return object
     *
     * @version 0.8
     * @author zhouqiang <zhouqiang@sulida.com>
     * @date 2015-11-27 10:24
     * @copyright  2013-2017 sulida.com  Inc. All Rights Reserved
     *
     */
    public function  postImportStudentUser(Request $request)
    {

        //die(json_encode($_FILES));
        //dd($request);
        try {
            $data = Common::getExclData($request, 'training');

            $studentInfo = array_shift($data);
//            die(json_encode($studentInfo ));
            //将中文头转换翻译成英文
            $studentInfo = Common::arrayChTOEn($studentInfo, 'msc.importForCnToEn.student_group');

//            die(json_encode($studentInfo));
//            dd($studentInfo);
            //已经存在的数据
            $dataHaven = [];
            //添加失败的数据
            $dataFalse = [];
            //判断是否存在这个学生用户//
            foreach ($studentInfo as $studentData) {
                //处理类别student_type
                switch ( $studentData['student_type']){
                    case "专科":
                        $studentData['student_type'] = 2;
                        break;
                    case "本科":
                        $studentData['student_type'] = 1;
                        break;
                };
                //处理专业professional
                switch ( $studentData['professional']){
                    case "儿科":
                        $studentData['professional'] = 1;
                        break;
                    case "设计":
                        $studentData['professional'] = 2;
                        break;
                };
                //处理性别gender
                switch ( $studentData['gender']){
                    case "男":
                        $studentData['gender'] = 1;
                        break;
                    case "女":
                        $studentData['gender'] = 2;
                        break;
                };
                //处理状态status
                switch ( $studentData['status']){
                    case "正常":
                        $studentData['status'] = 1;
                        break;
                    case "禁用":
                        $studentData['status'] = 2;
                        break;
                };

                if ($studentData['code'] && $studentData['name']) {

                    if (Student::where('code', '=', $studentData['code'])->count() == 0) {

                        $student = new Student();
                         $result=$student->AddStudent($studentData);
//                        die(json_encode($request));
                        if (!$result) {
//                                 Response::generate();
                            $dataFalse[] = $studentData;
//                            return redirect()->back()->withInput()->withErrors('导入成功');
                        }
                    } else {
//                        return redirect()->back()->withInput()->withErrors('已存在');
                        $dataHaven[] = $studentData;


                    }
                }
            }
//            die(json_encode($request));
            return response()->json(
                $this->success_data(['result' => true, 'dataFalse' => $dataFalse, 'dataHaven' => $dataHaven])
            );
        } catch (\Exception $e) {
            return response()->json($this->fail($e));
        }
    }



    /**
     * 导出学生用户
     * @api GET /msc/admin/user/export-student-user
     * @access public
     *
     * @param Request $request get请求<br><br>
     * <b>get请求字段：</b>
     * * string       keyword         关键字
     *
     * @return json
     *
     * @version 0.8
     * @author zhouqiang <zhouqiang@sulida.com>
     * @date 2015-11-27 10:24
     * @copyright  2013-2017 sulida.com  Inc. All Rights Reserved
     *
     */

    public function getExportStudentUser(Request $request)
    {
        $this->validate($request,[
            'keyword'            =>  'required',
        ],[ 'required' => '请输入关键字搜索导出']);
//        $keyword=$request->get('keyword');
        //同步学生列表的数据
        $studentInfo = $this->getStudentList($request);

//        dd($studentInfo->list);

        $str = iconv('utf-8', 'gb2312', '序号,姓名,学号,年级,类别,专业,手机号,证件号,性别,状态') . "\n";
        if (empty($studentInfo->list)) {
            $str .= iconv('utf-8', 'gb2312', '无,无,无,无,无,无,无,无,无,无') . "\n";
        } else {
            foreach ($studentInfo->list as $row) {
                $ID = iconv('utf-8', 'gb2312', $row['id']); //中文转码
                $name = iconv('utf-8', 'gb2312', $row['name']);
                $code = iconv('utf-8', 'gb2312', $row['code']);
                $grade = iconv('utf-8', 'gb2312', $row['grade']);
                $student_type = iconv('utf-8', 'gb2312', $row['student_type']);
                $profession_name = iconv('utf-8', 'gb2312', $row['profession_name']);
                $mobile = iconv('utf-8', 'gb2312', $row['mobile']);
                $idcard = iconv('utf-8', 'gb2312', $row['idcard']);
                $gender = iconv('utf-8', 'gb2312', $row['gender']);
                $status = iconv('utf-8', 'gb2312', $row['status']);
                $str .= $ID . "," . $name . "," . $code . "," . $grade . "," . $student_type . "," . $profession_name . "," . $mobile . "," . $idcard . "," . $gender . "," . $status . "\n"; //用引文逗号分开
            }
        }
        $filename = date('Ymd') . '.csv';
        $this->export_csv($filename, $str);

//        return 'res=1';

    }

    private function export_csv($filename, $data)
    {
        header("Content-type:text/csv");
        header("Content-Disposition:attachment;filename=" . $filename);
        header('Cache-Control:must-revalidate,post-check=0,pre-check=0');
        header('Expires:0');
        header('Pragma:public');
        echo $data;
    }



    /**
     * 导出教师用户
     * @api GET /msc/admin/user/export-teacher-user
     * @access public
     *
     * @param Request $request get请求<br><br>
     * <b>get请求字段：</b>
     * * string       keyword         关键字
     *
     * @return json
     *
     * @version 0.8
     * @author zhouqiang <zhouqiang@sulida.com>
     * @date 2015-11-27 10:24
     * @copyright  2013-2017 sulida.com  Inc. All Rights Reserved
     *
     */
    public function getExportTeacherUser(Request $request)
    {
        $this->validate($request,[
            'keyword'            =>  'required',
        ],[ 'required' => '请输入关键字搜索导出']);
        $teacherInfo = $this->getTeacherList($request);
//        dd($teacherInfo->list);
//        $teacherInfo = $this->getTeacherInfo($request);
        $str = iconv('utf-8', 'gb2312', '序号,姓名,胸牌号,科室,手机号,性别,状态,角色') . "\n";
        if (empty($teacherInfo->list)) {
            $str .= iconv('utf-8', 'gb2312', '无,无,无,无,无,无,无,无') . "\n";
        } else {
            foreach ($teacherInfo->list as $row) {
                $ID = iconv('utf-8', 'gb2312', $row['id']); //中文转码
                $name = iconv('utf-8', 'gb2312', $row['name']);
                $code = iconv('utf-8', 'gb2312', $row['code']);
                $dept_name = iconv('utf-8', 'gb2312', $row['dept_name']);
                $mobile = iconv('utf-8', 'gb2312', $row['mobile']);
                $gender = iconv('utf-8', 'gb2312', $row['gender']);
                $status = iconv('utf-8', 'gb2312', $row['status']);
//                $role = iconv('utf-8', 'gb2312', $row['role']);
                $str .= $ID . "," . $name . "," . $code . "," . $dept_name . "," . $mobile . "," . $gender . "," . $status . "\n"; //用引文逗号分开
            }
        }
//        dd($ID);
        $filename = date('Ymd') . '.csv';
        $this->export_csv($filename, $str);
    }

}
