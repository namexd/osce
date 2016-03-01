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
        $paginate = $request->input('paginate');
        //在模型中拿到数据
        $CaseModel = new CaseModel();
        $data = $CaseModel->getList($paginate);

        return view('osce::admin.resourcemanage.clinical_case_manage', ['data' => $data]);
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
        return view('osce::admin.resourcemanage.clinical_case_manage_add');
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
        $this->validate($request, [
            'name' => 'required|unique:osce_mis.cases,name'
        ],[
            'name.required'     =>  '病例名称不能为空',
            'name.unique'       =>  '病例名称必须唯一'
        ]);

        //获得提交的字段
        $formData = $request->only('name', 'description');

        DB::connection('osce_mis')->beginTransaction();
        if (CaseModel::where('name', str_replace(' ','',$formData['name']))->first()) {
            DB::connection('osce_mis')->rollBack();
            return redirect()->back()->withErrors('该病例名称已存在!');
        }
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
            return view('osce::admin.resourcemanage.clinical_case_manage_edit',['data'=>$data]);
        } catch (\Exception $ex) {
            return redirect()->back()->withErrors($ex->getMessage());
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
        $this->validate($request, [
            'id' => 'required|integer',
            'name' => 'required'
        ],[
            'id.required'     =>  '病例id不能为空',
            'name.required'     =>  '病例名称不能为空'
        ]);

        try {
        $id = $request->input('id');
        $formData = $request->only('name', 'description');

        $caseModel->updateCase($id, $formData);

        return redirect()->route('osce.admin.case.getCaseList');
        } catch (\Exception $ex) {
            return redirect()->back()->withErrors($ex->getMessage());
        }
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
                return $this->success_data(['删除成功！']);
            }
        } catch (\Exception $ex) {
            return $this->fail($ex);
        }
    }

    /**
     * 判断名称是否已经存在
     * @url POST /osce/admin/resources-manager/postNameUnique
     * @author Zhoufuxiang <Zhoufuxiang@misrobot.com>     *
     */
    public function postNameUnique(Request $request)
    {
        $this->validate($request, [
            'name'      => 'required',
        ]);

        $id     = $request  -> get('id');
        $name   = $request  -> get('name');

        //实例化模型
        $model =  new CaseModel();
        //查询 该名字 是否存在
        if(empty($id)){
            $result = $model->where('name', $name)->first();
        }else{
            $result = $model->where('name', $name)->where('id', '<>', $id)->first();
        }
        if($result){
            return json_encode(['valid' =>false]);
        }else{
            return json_encode(['valid' =>true]);
        }
    }

}