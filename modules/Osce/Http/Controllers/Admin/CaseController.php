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
use Modules\Osce\Entities\StationCase;
use Modules\Osce\Http\Controllers\CommonController;
use Modules\Osce\Entities\CaseModel as CaseModel;

class CaseController extends CommonController
{
    /**
     * 获取病历列表
     * @api       GET /osce/admin/case/case-list
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

        return view('osce::admin.resourcemanage.clinicalcase', ['data' => $data]);
    }

    /**
     * 新增数据的着陆页
     * @api       GET /osce/admin/case/create-case
     * @access    public
     * @param Request $request get请求<br><br>
     *                         <b>get请求字段：</b>
     *
     * @param CaseModel $caseModel
     * @return view
     * @version   1.0
     * @author    jiangzhiheng <jiangzhiheng@misrobot.com>
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function getCreateCase()
    {
        return view('osce::admin.resourcemanage.clinicalcase_add');
    }

    /**
     * 往数据库里插入一条数据
     * @api       POST /osce/admin/case/create-case
     * @access    public
     * @param Request $request post请求<br><br>
     *                         <b>get请求字段：</b>
     *
     * @param CaseModel $caseModel
     * @return view
     * @version   1.0
     * @author    jiangzhiheng <jiangzhiheng@misrobot.com>
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function postCreateCase(Request $request, CaseModel $caseModel)
    {
        //验证略过

        //获得提交的字段
        $formData = $request->only('name', 'description');

        DB::connection('osce_mis')->beginTransaction();
        $result = $caseModel->insertData($formData);
        if ($result == false) {
            DB::connection('osce_mis')->rollBack();
            return redirect()->back()->withErrors('数据插入失败,请重试!');
        }

        DB::connection('osce_mis')->commit();
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

            return view('osce::admin.resourcemanage.clinicalcase_edit',['data'=>$data]);
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
     * @param CaseModel $caseModel
     * @return view
     * @version   1.0
     * @author    jiangzhiheng <jiangzhiheng@misrobot.com>
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function postEditCase(Request $request, CaseModel $caseModel)
    {
        //验证,略过

        $id = $request->input('id');
        $formData = $request->only('name', 'description');

        DB::connection('osce_mis')->beginTransaction();
        $result = $caseModel->updateData($id, $formData);
        if ($result != true) {
            DB::connection('osce_mis')->rollBack();
            return redirect()->back()->withInput()->withErrors('数据未能成功修改,请重试!');
        }
        DB::connection('osce_mis')->commit();
        return redirect()->route('osce.admin.case.getCaseList');
    }

    /**
     * 根据id删除病历
     * @api       POST /osce/admin/case/delete
     * @access    public
     * @param Request $request post请求<br><br>
     *                         <b>post请求字段：</b>
     * @param CaseModel $caseModel
     * @return view
     * @version   1.0
     * @author    jiangzhiheng <jiangzhiheng@misrobot.com>
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function postDelete(Request $request, CaseModel $caseModel)
    {
        try {
            //获取删除的id
            $id = $request->input('id');
            //将id传入删除的方法
            $result = $caseModel->deleteData($id);
            if ($result) {
                return redirect()->route('osce.admin.case.getCaseList');
            }
        } catch (\Exception $ex) {
            return response()->json($this->fail($ex));
        }
    }
}