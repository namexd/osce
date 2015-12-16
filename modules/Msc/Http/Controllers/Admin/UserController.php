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

        //dd($list);
        return view('msc::admin.usermanage.student_manage', ['list'=>$list]);
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
     * 
     * 编辑学生回显
     * @method GET
     * @url /msc/admin/user/student-edit/{id}
     * @access public
     * @param Request $request get请求<br><br>
     * <b>get请求字段：</b>
     * 
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
     * @method GET
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
    public function postStudentAdd(Request $request)
    {
        dd(11);
        $this->validate($request, [
            'name' => 'required|max:50',
            'code' => 'required|integer|min:0|max:32',
            'gender' => 'required|min:0|max:1',
            'grade' => 'required|integer|min:0|max:11',
            'student_type' => 'required|integer|min:0|max:3',
            'professional' => 'required|integer|min:0|max:11',
            'validated' => 'required|integer|min:0|max:1',
            'moblie' => 'required|integer|max:11',
            'idcard_type' => 'required|integer|min:0|max:1',
            'idcard' => 'required|integer|min:0|max:50',
        ]);

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
     * 
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
     * 修改学生状态
     * @param Request $request get请求<br><br>
     * <b>get请求字段：</b>
     * int        $id        学生编号
     * @return blooean
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
     * 查看老师
     * @method GET
     * @url /msc/admin/user/teacher-item/{id}
     * @param Request $request get请求<br><br>
     * @access public
	 * int        $id        老师编号
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
    public function  postImportStudentUser(Request $request){
        try{
            $data= Common::getExclData($request,'student');
            $studentInfo = array_shift($data);
            //将中文头转换翻译成英文
            $studentInfo = Common::arrayChTOEn($studentInfo,'msc.importForCnToEn.student_group');
            dd($data);
            //已经存在的数据
            $dataHaven=[];
            //添加失败的数据
            $dataFalse=[];
            //判断是否存在这个学生用户
            foreach($studentInfo as $studentData){
                if($studentData['student_code']&&$studentData['name']){
                    if(Student::where('code','=',$studentData['student_code']->count()==0)){

                        $student=Student::create($studentData);

                        if($student==false){
                            $dataFalse[]=$studentData;
                        }
                    }
                    else{
                        $dataHaven[]=$studentData;
                    }
                }
            }
            return response()->json(
                $this->success_data(['result'=>true,'dataFalse'=>$dataFalse,'dataHaven'=>$dataHaven])
            );
        }
        catch(\Exception $e)
        {
            return response()->json($this->fail($e));
        }
    }



    public  function getExportStudentUser(){







        
    }




}
