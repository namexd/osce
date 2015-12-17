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
use Modules\Msc\Http\Controllers\MscController;

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
     * * string        order_name      排序字段名
     * * string        order_type      排序方式(1:Desc 0:asc)
     * * string        keyword         关键字
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
            'order_name' => 'sometimes|string|between:2:50',
            'order_type' => 'sometimes|in:0,1',
            'keyword' => 'sometimes', // TODO 查询关键字约束
        ]);

        $orderName = e($request->input('order_name'));
        $orderType = (int)$request->input('order_type');
        $keyword = urldecode(e($request->input('keyword')));

        // 排序
        if ($orderName) {
            if ($orderType) {
                $order = [$orderName, 'desc'];
            } else {
                $order = [$orderName, 'asc'];
            }
        } else {
            $order = ['id', 'desc']; // 默认按照ID降序排列
        }

        $student = new Student();
        $pagination = $student->getFilteredPaginateList($keyword, $order);

        $list = [];
        foreach ($pagination as $item) {
            $list[] = [
                'id' => $item->id,
                'name' => $item->name,
                'code' => $item->code,
                'grade' => $item->grade,
                'student_type' => $item->student_type,
                'profession_name' => is_null($item->professionalName) ? '-' : $item->professionalName->name,
                'mobile' => $item->userInfo->mobile,
                'idcard' => $item->userInfo->idcard,
                'gender' => $item->userInfo->gender,
                'status' => $item->userInfo->status,
            ];
        }

//        dd($list);
        return view('msc::admin.usermanage.student_manage', ['list' => $list]);
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
     * @return view
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
            'id' => $student->id,
            'name' => $student->name,
            'code' => $student->code,
            'grade' => $student->grade,
            'student_type' => $student->student_type,
            'profession_name' => $student->professionalName->name,
            'mobile' => $student->userInfo->mobile,
            'idcard' => $student->userInfo->idcard,
            'gender' => $student->userInfo->gender,
            'status' => $student->userInfo->status,
        ];

        dd($data);
    }

    /**
     * <<<<<<< HEAD
     * 编辑学生回显
     * @method GET
     * @url /msc/admin/user/student-edit/{id}
     * =======
     * 教师列表
     * @method GET
     * @url /msc/admin/user/teacher-list
     * @access public
     *
     * @param Request $request get请求<br><br>
     * <b>get请求字段：</b>
     * * int        $id        学生编号
     *
     * @return getStudentItem
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
     * @method POST
     * @url /msc/admin/user/student-save
     * @method GET
     * @url /msc/admin/user/student-submit/{id}
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
     * @date 2015-12-15 15:30
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function postStudentSave(Request $request)
    {
        dd(11);
        $this->validate($request, [
            'id' => 'required|min:0|max:10',
            'name' => 'required|max:50',
            'code' => 'required|integer|min:0|max:32',
            'gender' => 'required|min:0|max:1',
            'grade' => 'required|integer|min:0|max:11',
            'student_type' => 'required|integer|min:0|max:3',
            'professional_name' => 'required|max:50',
            'moblie' => 'required|unique|integer|max:11',
            'professional' => 'required|integer|min:0|max:11',
            'validated' => 'required|integer|min:0|max:1',
            'moblie' => 'required|integer|max:11',
            'idcard_type' => 'required|integer|min:0|max:1',
            'idcard' => 'required|integer|min:0|max:50',
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
     * @method POST
     * @url /msc/admin/user/student-add/{id}
     * @access public
     *
     * @param Request $request post请求<br><br>
     * <b>post数据：</b>
     * *
     *
     * @return blooean
     *
     * @version 0.8
     * @author zhouchong <zhouchong@misrobot.com>
     * @date 2015-12-15 16:00
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */

    public function postStudentAdd(Request $request,$status=1) {

        $this->validate($request, [
            'name' => 'required|max:50',
            'code' => 'required|integer|min:0|max:32',
            'gender' => 'required|min:0|max:1',
            'grade' => 'required|integer|min:0|max:11',
            'student_type' => 'required|integer|min:0|max:3',
            'professional_name' => 'required|max:50',
            'moblie' => 'required|integer|max:11',
            'idcard_type' => 'required|integer|min:0|max:1',
            'idcard' => 'required|integer|min:0|max:50',
        ]);


        $data = $request->only(['name', 'code', 'gender', 'grade', 'student_type', 'professional_name', 'validated', 'moblie', 'idcard_type', 'idcard']);
        $data['status']=$status;

        $data = $request->only(['name', 'code', 'gender', 'grade', 'student_type', 'professional', 'validated', 'moblie', 'idcard_type', 'idcard']);


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
     * @return blooean
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
     * 改变状态
     * @method GET
     * @url /msc/admin/user/student-status/{id}
     * =======
     * * string        order_name      排序字段名
     * * string        order_type      排序方式(1:Desc 0:asc)
     * * string        keyword         关键字
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
            'order_name' => 'sometimes|string|between:2,50',
            'order_type' => 'sometimes|in:0,1',
            'keyword' => 'sometimes', // TODO 查询关键字约束
        ]);

        $orderName = e($request->input('order_name'));
        $orderType = (int)$request->input('order_type');
        $keyword = urldecode(e($request->input('keyword')));

        // 排序
        if ($orderName) {
            if ($orderType) {
                $order = [$orderName, 'desc'];
            } else {
                $order = [$orderName, 'asc'];
            }
        } else {
            $order = ['id', 'desc']; // 默认按照ID降序排列
        }

        $teacher = new Teacher();
        $pagination = $teacher->getFilteredPaginateList($keyword, $order);

        $list = [];
        foreach ($pagination as $item) {
            $list[] = [
                'id' => $item->id,
                'name' => $item->name,
                'code' => $item->code,
                'dept_name' => is_null($item->dept) ? '-' : $item->dept->name,
                'mobile' => $item->userInfo->mobile,
                'gender' => $item->userInfo->gender,
                'status' => $item->userInfo->status,
                'role' => $item->userInfo->roles,
            ];
        }

        dd($list);
    }

    /**
     * 查看老师
     * @method GET
     * @url /msc/admin/user/teacher-item/{id}
     * >>>>>>> be479b948e07af3b3d2842357553dcda20a1a802
     * @access public
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


    /** *int        $id        老师编号
     *
     * @return view
     *
     * @version 0.8
     * @author wangjiang < wangjiang@misrobot . com >
     * @date 2015 - 12 - 15 14:04
     * @copyright 2013 - 2015 MIS misrobot . com Inc . All Rights Reserved
     */
    public function getTeacherItem($id)
    {
        $teacherId = intval($id);

        $teacher = Teacher::findOrFail($teacherId);

        $data = [
            'id' => $teacher->id,
            'name' => $teacher->name,
            'code' => $teacher->code,
            'dept_name' => is_null($teacher->dept) ? '-' : $teacher->dept->name,
            'mobile' => $teacher->userInfo->mobile,
            'gender' => $teacher->userInfo->gender,
            'status' => $teacher->userInfo->status,
            'role' => $teacher->userInfo->roles,
        ];

        dd($data);
//        return $data;
    }


    /**
     * 导入教师用户
     * @api GET /msc/admin/User/import-Teacher-user
     * @access public
     *
     * @param Request $request post请求<br><br>
     * <b>post请求字段：</b>
     * * string        courses-plan        课程文件的excl(必须的)
     * @return object
     * @version 0.8
     * @author zhouqiang <zhouqiang@misrobot.com>
     * @date 2015-11-27 10:24
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     *
     */
    public function  postImportTeacherUser(Request $request)
    {
        try {
            $data = Common::getExclData($request, 'teacher');
            $teacherInfo = array_shift($data);
            //将中文头转换翻译成英文
            $studentInfo = Common::arrayChTOEn($teacherInfo, 'msc.importForCnToEn.teacher_group');  //teacher_group还未定义
            dd($data);
            //已经存在的数据
            $dataHaven = [];
            //添加失败的数据
            $dataFalse = [];
            //判断是否存在这个学生用户
            foreach ($teacherInfo as $teacherData) {
                if ($teacherData['student_code'] && $teacherData['name']) {
                    if (Teacher::where('code', '=', $teacherData['student_code']->count() == 0)) {

                        $student = Student::create($teacherData);

                        if ($student == false) {
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
     * * string        courses-plan        课程文件的excl(必须的)
     *
     * @return object
     *
     * @version 0.8
     * @author zhouqiang <zhouqiang@misrobot.com>
     * @date 2015-11-27 10:24
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     *
     */
    public function  postImportStudentUser(Request $request)
    {
        try {
            $data = Common::getExclData($request, 'student');
            $studentInfo = array_shift($data);
            //将中文头转换翻译成英文
            $studentInfo = Common::arrayChTOEn($studentInfo, 'msc.importForCnToEn.student_group');
            dd($data);
            //已经存在的数据
            $dataHaven = [];
            //添加失败的数据
            $dataFalse = [];
            //判断是否存在这个学生用户
            foreach ($studentInfo as $studentData) {
                if ($studentData['student_code'] && $studentData['name']) {
                    if (Student::where('code', '=', $studentData['student_code']->count() == 0)) {

                        $student = Student::create($studentData);

                        if ($student == false) {
                            $dataFalse[] = $studentData;
                        }
                    } else {
                        $dataHaven[] = $studentData;
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
     * 导出学生用户
     * @api GET /msc/admin/User/Export-Student-User
     * @access public
     *
     * @param Request $request get请求<br><br>
     * <b>get请求字段：</b>
     * * string       keyword         关键字
     *
     * @return json
     *
     * @version 0.8
     * @author zhouqiang <zhouqiang@misrobot.com>
     * @date 2015-11-27 10:24
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     *
     */

    public function getExportStudentUser(Request $request)
    {

        $studentInfo = $this->getStudentInfo($request);
//        $studentInfo=array(
//            array("id" => 98,
//                "name" => "wulengmei111",
//                "code" => "100245",
//                "grade" => 2009,
//                "student_type" => "专科",
//                "profession_name" => "儿科",
//                "mobile" => "18215508437",
//                "idcard" => "511124199109030026",
//                "gender" => "女",
//                "status" => "正常",),
//            array("id" => 98,
//                "name" => "wulengmei222",
//                "code" => "100245",
//                "grade" => 2009,
//                "student_type" => "专科",
//                "profession_name" => "儿科",
//                "mobile" => "18215508437",
//                "idcard" => "511124199109030026",
//                "gender" => "女",
//                "status" => "正常",),
//
//
//        );
//        dd($studentInfo[0]['name']);

        $str = iconv('utf-8', 'gb2312', '序号,姓名,学号,年级,类别,专业,手机号,证件号,性别,状态') . "\n";
        if (empty($studentInfo)) {
            $str .= iconv('utf-8', 'gb2312', '无,无,无,无,无,无,无,无,无,无') . "\n";
        } else {
            foreach ($studentInfo as $row) {
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
    }


    /**
     * 导出教师用户
     * @api GET /msc/admin/User/Export-Teacher-User
     * @access public
     *
     * @param Request $request get请求<br><br>
     * <b>get请求字段：</b>
     * * string       keyword         关键字
     *
     * @return json
     *
     * @version 0.8
     * @author zhouqiang <zhouqiang@misrobot.com>
     * @date 2015-11-27 10:24
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     *
     */
    public function postTeacherAdd(Request $request,$status=1){
//        $teacherInfo = $this->getTeacherInfo($request);
                $teacherInfo=array(
            array(

                "id" => 98,
                "name" => "wulengmei111",
                "code" => "100245",
                "dept_name"=>"设备管理",
                "mobile" => "18215508437",
                "gender" => "女",
                "role"=>"设备管理员",
                "status" => "正常",),
            array(
                "id" => 99,
                "name" => "张三",
                "code" => "100245",
                "dept_name"=>"设备管理",
                "mobile" => "18215508437",
                "gender" => "女",
                "role"=>"设备管理员",
                "status" => "正常",),
        );


        $str = iconv('utf-8', 'gb2312', '序号,姓名,胸牌号,科室,手机号,性别,角色,状态') . "\n";
        if (empty($teacherInfo)) {
            $str .= iconv('utf-8', 'gb2312', '无,无,无,无,无,无,无,无') . "\n";
        } else {
            foreach ($teacherInfo as $row) {
                $ID = iconv('utf-8', 'gb2312', $row['id']); //中文转码
                $name = iconv('utf-8', 'gb2312', $row['name']);
                $code = iconv('utf-8', 'gb2312', $row['code']);
                $dept_name = iconv('utf-8', 'gb2312', $row['dept_name']);
                $mobile = iconv('utf-8', 'gb2312', $row['mobile']);
                $gender = iconv('utf-8', 'gb2312', $row['gender']);
                $role = iconv('utf-8', 'gb2312', $row['role']);
                $status = iconv('utf-8', 'gb2312', $row['status']);
                $str .= $ID . "," . $name . "," . $code . "," . $dept_name . "," . $mobile . "," . $gender .",".$role. "," . $status . "\n"; //用引文逗号分开
            }
        }
        $filename = date('Ymd') . '.csv';
        $this->export_csv($filename, $str);
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
     * 获取学生用户信息 用于导出
     * @method GET
     * @url /msc/admin/user/student-list
     * @access public
     *
     * @param Request $request get请求<br><br>
     * <b>get请求字段：</b>
     * * string        order_name      排序字段名
     * * string        order_type      排序方式(1:Desc 0:asc)
     * * string        keyword         关键字
     *
     * @return           list        数组
     *
     * @version 0.8
     * @author zhouqiang <zhouqiang@misrobot.com>
     * @date 2015-12-14 18:29
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function getStudentInfo(Request $request)
    {
//        echo '11111';exit;
        $this->validate($request, [
            'order_name' => 'sometimes|string|between:2:50',
            'order_type' => 'sometimes|in:0,1',
            'keyword' => 'sometimes', // TODO 查询关键字约束
        ]);

        $orderName = e($request->input('order_name'));
        $orderType = (int)$request->input('order_type');
        $keyword = urldecode(e($request->input('keyword')));

        // 排序
        if ($orderName) {
            if ($orderType) {
                $order = [$orderName, 'desc'];
            } else {
                $order = [$orderName, 'asc'];
            }
        } else {
            $order = ['id', 'desc']; // 默认按照ID降序排列
        }

        $student = new Student();
        $pagination = $student->getFilteredPaginateList($keyword, $order);

        $list = [];
        foreach ($pagination as $item) {
            $list[] = [
                'id' => $item->id,
                'name' => $item->name,
                'code' => $item->code,
                'grade' => $item->grade,
                'student_type' => $item->student_type,
                'profession_name' => is_null($item->professionalName) ? '-' : $item->professionalName->name,
                'mobile' => $item->userInfo->mobile,
                'idcard' => $item->userInfo->idcard,
                'gender' => $item->userInfo->gender,
                'status' => $item->userInfo->status,
            ];
        }

        return $list;

    }

    /**
     *获取教师用户信息 用于导出
     * @method GET
     * @url /msc/admin/user/teacher-info
     * @access public
     *
     * @param Request $request post请求<br><br>
     * <b>post请求字段：</b>
     *
     *string        keyword         关键字
     * @return          list        数组
     *
     * @version 1.0
     * @author zhouqiang <zhouqiang@misrobot.com>
     * @date ${DATE} ${TIME}
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */

    public function getTeacherInfo(Request $request)
    {
        $this->validate($request, [
            'order_name' => 'sometimes|string|between:2:50',
            'order_type' => 'sometimes|in:0,1',
            'keyword' => 'sometimes', // TODO 查询关键字约束
        ]);

        $orderName = e($request->input('order_name'));
        $orderType = (int)$request->input('order_type');
        $keyword = urldecode(e($request->input('keyword')));

        // 排序
        if ($orderName) {
            if ($orderType) {
                $order = [$orderName, 'desc'];
            } else {
                $order = [$orderName, 'asc'];
            }
        } else {
            $order = ['id', 'desc']; // 默认按照ID降序排列
        }

        $teacher = new Teacher();
        $pagination = $teacher->getFilteredPaginateList($keyword, $order);

        $list = [];
        foreach ($pagination as $item) {
            $list[] = [
                'id' => $item->id,
                'name' => $item->name,
                'code' => $item->code,
                'grade' => $item->grade,
                'student_type' => $item->student_type,
                'profession_name' => is_null($item->professionalName) ? '-' : $item->professionalName->name,
                'mobile' => $item->userInfo->mobile,
                'idcard' => $item->userInfo->idcard,
                'gender' => $item->userInfo->gender,
                'status' => $item->userInfo->status,
            ];
        }

//        dd($list);
//        $teacher=Teacher::all();
//        dd($teacher);
//        if(empty($keyword)){
//            $teacher=Teacher::all();
//            dd($teacher);
//        }else{
//            $teacherId = intval($id);
//            $teacher = Teacher::findOrFail($teacherId);
//        }
//        $list   = [
//            'id' => $teacher->id,
//            'name' => $teacher->name,
//            'code' => $teacher->code,
//            'dept_name' => is_null($teacher->dept) ? '-' : $teacher->dept->name,
//            'mobile' => $teacher->userInfo->mobile,
//            'gender' => $teacher->userInfo->gender,
//            'status' => $teacher->userInfo->status,
//            'role' => $teacher->userInfo->roles,
//        ];

        return $list;

    }


}
