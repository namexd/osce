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

class UserController extends BaseController {

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
    public function getStudentList (Request $request)
    {
        $this->validate($request, [
            'order_name' 		=> 	'sometimes|max:50',
            'order_type'		=> 	'sometimes|integer|min:0|max:1',
            'keyword' 		    => 	'sometimes', // TODO 查询关键字约束
        ]);

        $orderName = e($request->input('order_name'));
        $orderType = (int) $request->input('order_type');
        $keyword   = urldecode(e($request->input('keyword')));

        // 排序
        $order = [];
        if (!empty($orderName))
        {
            if ($orderType)
            {
                $order = [$orderName, 'desc'];
            }
            else
            {
                $order = [$orderName, 'asc'];
            }
        }
        else
        {
            $order = ['id', 'desc']; // 默认按照ID降序排列
        }

        $student = new Student();
        $pagination = $student->getFilteredPaginateList($keyword, $order);

        $list = [];
        foreach ($pagination as $item)
        {
            $list[] = [
                'id'              => $item->id,
                'name'            => $item->name,
                'code'            => $item->code,
                'grade'           => $item->grade,
                'student_type'    => $item->student_type,
                'profession_name' => $item->profession_name,
                'mobile'          => $item->userInfo->mobile,
                'idcard'          => $item->userInfo->idcard,
                'gender'          => $item->userInfo->gender,
                'status'          => $item->userInfo->status,
            ];
        }

        dd($list);
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
    public function getStudentItem ($id)
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

        dd($data);
    }
}