<?php
/**
 * Created by PhpStorm.
 * User: wangjiang
 * Date: 2015/12/14 0014
 * Time: 18:17
 */
namespace Modules\Msc\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Modules\Msc\Entities\Student;
use Modules\Msc\Entities\Teacher;
use Modules\Msc\Entities\StdProfessional;
use Modules\Msc\Entities\TeacherDept;


class UserController extends BaseController
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
     * @author wangjiang <wangjiang@misrobot.com>
     * @date 2015-12-14 18:29
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
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
     * @author wangjiang <wangjiang@misrobot.com>
     * @date 2015-12-15 10:58
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
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
     * @author wangjiang <wangjiang@misrobot.com>
     * @date 2015-12-15 11:32
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
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
     * @author wangjiang <wangjiang@misrobot.com>
     * @date 2015-12-15 17:30
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
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
     * @return blooean
     *
     * @version 0.8
     * @author zhouchong <zhouchong@misrobot.com>
     * @date 2015-12-15 14:50
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
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
     * @author zhouchong <zhouchong@misrobot.com>
     * @date 2015-12-15 15:30
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function postStudentSave(Request $request)
    {
        $this->validate($request, [
            'id' => 'sometimes|min:0|max:10',
            'name' => 'required|max:50',
            'code' => 'required|unique|integer|min:0|max:32',
            'gender' => 'required|min:0|max:1',
            'grade' => 'required|integer|min:0|max:11',
            'student_type' => 'required|integer|min:0|max:3',
            'professional_name' => 'required|max:50',
            'validated' => 'required|integer|min:0|max:1',
            'moblie' => 'required|unique|integer|max:11',
            'idcard_type' => 'required|integer|min:0|max:1',
            'idcard' => 'required|unique|integer|min:0|max:50',
        ]);

        $data = $request->only(['id', 'name', 'code', 'gender', 'grade', 'student_type', 'professional', 'validated', 'moblie', 'idcard_type', 'idcard']);

        $studentModel = new Student();

        $result = $studentModel->saveEditStudent($data);

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
     * 添加学生信息
     * @method GET
     * @url /msc/admin/user/student-add/{id}
     * @access public
     *
     * @param Request $request post请求<br><br>
     * <b>post数据：</b>
     * *
     *
     * @return json
     *
     * @version 0.8
     * @author zhouchong <zhouchong@misrobot.com>
     * @date 2015-12-15 16:00
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function postStudentAdd(Request $request,$status=0)
    {
        $this->validate($request, [
            'name' => 'required|max:50',
            'code' => 'required|unique|integer|min:0|max:32',
            'gender' => 'required|min:0|max:1',
            'grade' => 'required|integer|min:0|max:11',
            'student_type' => 'required|integer|min:0|max:3',
            'professional_name' => 'required|max:50',
            'moblie' => 'required|integer|max:11',
            'idcard_type' => 'required|integer|min:0|max:1',
            'idcard' => 'required|unique|integer|min:0|max:50',
        ]);

        $data = $request->only(['name', 'code', 'gender', 'grade', 'student_type', 'professional_name', 'validated', 'moblie', 'idcard_type', 'idcard']);
        $data['status']=$status;
        $studentModel = new Student();

        $result = $studentModel->postAddStudent($data);

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
     * @author zhouchong <zhouchong@misrobot.com>
     * @date 2015-12-15 16:30
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
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
     * <<<<<<< HEAD
     * * int        $id        学生编号
     *
     * @return blooean
     *
     * @version 0.8
     * @author zhouchong <zhouchong@misrobot.com>
     * @date 2015-12-15 17:30
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
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
     * @url /msc/admin/user/teacher-list
     * @access public
     *
     * @param Request $request get请求<br><br>
     * <b>get请求字段：</b>
     * * int        $id        学生编号
     *
     * @return blooean
     *
     * @version 0.8
     * @author zhouchong <zhouchong@misrobot.com>
     * @date 2015-12-15 14:50
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function getTeacherEdit($id)
    {

        $teacherId = intval($id);


        return $this->getTeacherItem($teacherId);

    }

    /**
     * 提交编辑教职工
     * @method GET
     * @url /msc/admin/user/teacher-save/{id}
     * @access public
     *
     * @param Request $request get请求<br><br>
     * <b>get请求字段：</b>
     * * int        $id        教师编号
     *
     * @return json
     *
     * @version 0.8
     * @author zhouchong <zhouchong@misrobot.com>
     * @date 2015-12-15 15:30
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function postTeacherSave(Request $request)
    {

        $this->validate($request, [
            'id' => 'sometimes|min:0|max:10',
            'name' => 'required|max:50',
            'code' => 'required|unique|integer|min:0|max:32',
            'gender' => 'required|min:0|max:1',
            'teacher_dept' => 'required|integer|min:0|max:3',
            'moblie' => 'required|unique|integer|max:11',
        ]);

        $data = $request->only(['name', 'code', 'gender',  'teacher_dept',  'moblie']);

        $teacherModel = new Teacher();

        $result = $teacherModel->saveEditTeacher($data);

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
     * 添加教职工信息
     * @method GET
     * @url /msc/admin/user/teacher-add/{id}
     * @access public
     *
     * @param Request $request post请求<br><br>
     * <b>post数据：</b>
     * *
     *
     * @return json
     *
     * @version 0.8
     * @author zhouchong <zhouchong@misrobot.com>
     * @date 2015-12-15 16:00
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function postTeacherAdd(Request $request,$status=0)
    {

        $this->validate($request, [
            'name' => 'required|max:50',
            'code' => 'required|unique|integer|min:0|max:32',
            'gender' => 'required|min:0|max:1',
            'teacher_dept' => 'required|integer|min:0|max:3',
            'moblie' => 'required|unique|integer|max:11',
        ]);

        $data = $request->only(['name', 'code', 'gender',  'teacher_dept',  'moblie']);
        $data['status']=$status;
        $teacherModel = new Teacher();

        $result = $teacherModel->postAddTeacher($data);

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
     * @author zhouchong <zhouchong@misrobot.com>
     * @date 2015-12-15 16:30
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
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
     * @author zhouchong <zhouchong@misrobot.com>
     * @date 2015-12-15 17:30
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
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




}
