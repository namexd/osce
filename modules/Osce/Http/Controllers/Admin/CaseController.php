<?php
/**
 * Created by PhpStorm.
 * User: j5110
 * Date: 2015/12/30
 * Time: 11:21
 */

namespace Modules\Osce\Http\Controllers\Admin;

use DB;
use Illuminate\Http\Request;
use Modules\Osce\Http\Controllers\CommonController;
use Modules\Osce\Entities\CaseModel as CaseModel;

class CaseController extends CommonController
{
    /**
     * 获取病历列表
     * @api       GET /osce/admin/place/case-list
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
    public function getCaseList(Request $request)
    {
        //验证暂时空置

        //获得提交的各个值
        $formData = $request->only('keyword', 'order_name', 'order_by');
        //在模型中拿到数据
        $CaseModel = new CaseModel();
        $data = $CaseModel->getList($formData);
        dd($data);

        return view('', ['data' => $data]);
    }

    /**
     * 往数据库里插入一条数据
     * @api       POST /osce/admin/place/create-case
     * @access    public
     * @param Request $request get请求<br><br>
     *                         <b>get请求字段：</b>
     *
     * @return view
     * @version   1.0
     * @author    jiangzhiheng <jiangzhiheng@misrobot.com>
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function postCreateCase(Request $request, CaseModel $caseModel)
    {
        //验证略过
        $this->validate($request, [
            'name' => 'required',
            'status' => 'required|integer',
            'detail' => 'required'
        ]);
        //获得提交的字段
        $formData = $request->only('name', 'status', 'detail');

        DB::connection('osce_mis')->beginTransaction();
        $result = $caseModel->insertData($formData);
        if ($result !== true) {
            DB::connection('osce_mis')->rollBack();
            return redirect()->back()->withErrors('数据插入失败,请重试!')->withInput();
        }
        return redirect()->route('osce.admin.case.getCaseList');


    }

    /**
     * 根据id修改病历 着陆页面
     * @api       GET /osce/admin/place/edit-case
     * @access    public
     * @param Request $request get请求<br><br>
     *                         <b>get请求字段：</b>
     *                         id           主键
     * @return view
     * @version   1.0
     * @author    jiangzhiheng <jiangzhiheng@misrobot.com>
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function getEditCase(Request $request)
    {
        //验证
        $this->validate($request, [
            'id' => 'required|integer'
        ]);

        //获得ID
        $id = $request->input('id');

        //通过id查到该条信息
        try {
            $data = CaseModel::findOrFail($id);

//            return view('',['data'=>$data]);
        } catch (\Exception $ex) {
            return redirect()->back()->withErrors($ex);
        }
    }

    /**
     * 根据id修改病历
     * @api       POST /osce/admin/place/edit-case
     * @access    public
     * @param Request $request get请求<br><br>
     *                         <b>get请求字段：</b>
     * @return view
     * @version   1.0
     * @author    jiangzhiheng <jiangzhiheng@misrobot.com>
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function postEditCase(Request $request, CaseModel $caseModel)
    {
        //验证
        $this->validate($request, [
            'id' => 'required|integer',
            'name' => 'required',
            'status' => 'required|integer',
            'detail' => 'required'
        ]);
        $id = $request->input('id');
        $formData = $request->only('name', 'status', 'detail');

        DB::connection('osce_mis')->beginTransaction();
        $result = $caseModel->updateData($id, $formData);
        if ($result !== true) {
            DB::connection('osce_mis')->rollBack();
            return redirect()->back()->withErrors('数据未能成功修改,请重试!')->withInput();
        }
        DB::connection('osce_mis')->commit();
        return redirect()->route('osce.admin.case.getCaseList');




    }
}