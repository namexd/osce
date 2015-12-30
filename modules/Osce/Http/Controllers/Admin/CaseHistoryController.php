<?php
/**
 * Created by PhpStorm.
 * User: j5110
 * Date: 2015/12/30
 * Time: 11:21
 */

namespace Modules\Osce\Http\Controllers\Admin;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Modules\Osce\Http\Controllers\CommonController;
use Modules\Osce\Repositories\Factory;
use Modules\Osce\Entities\CaseHistory as CaseHistory;
class CaseHistoryController extends CommonController
{
    /**
     * 获取病历列表
     * @api       GET /osce/admin/place/case-history-list
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
    public function getCaseHistoryList(Request $request)
    {
        //验证暂时空置

        //获得提交的各个值
        $formData = $request->only('keyword','order_name','order_by');
        //在模型中拿到数据
        $caseHistory = new CaseHistory();
        $data = $caseHistory->getList($formData);
        dd($data);
    }

    /**
     * 往数据库里插入一条数据
     * @api       POST /osce/admin/place/edit-case-history
     * @access    public
     * @param Request $request get请求<br><br>
     *                         <b>get请求字段：</b>
     *
     * @return view
     * @version   1.0
     * @author    jiangzhiheng <jiangzhiheng@misrobot.com>
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function postAddCaseHistory(Request $request)
    {
        //验证略过

        //将传入的数据放入create方法中
        $model = 'CaseHistory';
        $result = $this->create($request,$model);
        try {
            if (!$result) {
                throw new \Exception('数据插入失败，请重试');
            } else {
                $this->success_data($this->toArray($result));
            }
        } catch (\Exception $ex) {
            return response()->json($this->fail($ex));
        }
    }

    /**
     * 根据id修改病历
     * @api       GET /osce/admin/place/edit-case-history
     * @access    public
     * @param Request $request get请求<br><br>
     *                         <b>get请求字段：</b>
     *                         id           主键
     * @return view
     * @version   1.0
     * @author    jiangzhiheng <jiangzhiheng@misrobot.com>
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function getEditCaseHistory(Request $request)
    {
        //验证
        $this->validate($request,[
            'id' => 'required|integer'
        ]);

        //获得ID
        $id = $request->input('id');

        //通过id查到该条信息
        try {
            $data = CaseHistory::findOrFail($id);

//            return view('',['data'=>$data]);
        } catch (\Exception $ex) {
            return response()->json($this->fail($ex));
        }
    }

    /**
     * 根据id修改病历
     * @api       POST /osce/admin/place/edit-case-history
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
    public function postEditCaseHistory(Request $request)
    {
        //验证略过

        //
    }
}