<?php
/**
 * Name: KindController.php
 * User: Hsiaowei(phper.tang@qq.com)
 * Date: 2018/2/7
 * Time：17:45
 */

namespace Modules\Osce\Http\Controllers\Admin;

use DB;
use Illuminate\Http\Request;
use Modules\Osce\Http\Controllers\CommonController;
use Modules\Osce\Entities\Casekind;

class KindController extends CommonController
{
    /*
     * name：获取病历列表
     * date：2018/2/7 17:55
     * author:Hsiaowei(phper.tang@qq.com)
     * param： int *
     * param： string *
     * return： array
     * */
    public function getKindList(Request $request)
    {
        //验证暂时空置

        //获得提交的各个值
        $paginate = $request->input('paginate');
        //在模型中拿到数据
        $data = Casekind::paginate(10);

        return view('osce::admin.resourceManage.clinical_kind_manage', ['data' => $data]);
    }

    /*
     * name：创建病种视图
     * date：2018/2/7 17:55
     * author:Hsiaowei(phper.tang@qq.com)
     * param： int *
     * param： string *
     * return： array
     * */
    public function getCreateKind()
    {
        return view('osce::admin.resourceManage.clinical_kind_manage_add');
    }

    /*
     * name：创建病种数据
     * date：2018/2/7 17:55
     * author:Hsiaowei(phper.tang@qq.com)
     * param： int *
     * param： string *
     * return： array
     * */
    public function postCreateKind(Request $request)
    {
        //验证略过
        $this->validate($request, [
            'name' => 'required|unique:osce_mis.cases_kind,name'
        ],[
            'name.required'     =>  '病种名称不能为空',
            'name.unique'       =>  '病种名称必须唯一'
        ]);

        //获得提交的字段
        $formData = $request->only('name', 'description');

        Casekind::create($formData);

        return redirect()->route('osce.admin.kind.getKindList');

    }

    /*
     * name：根据id修改病历 着陆页面
     * date：2018/2/7 18:04
     * author:Hsiaowei(phper.tang@qq.com)
     * param： int *
     * param： string *
     * return： array
     * */
    public function getEditKind(Request $request)
    {
        //验证
        $this->validate($request, [
            'id' => 'required|integer'
        ]);

        //获得ID
        $id = $request->input('id');

        //通过id查到该条信息
        try {
            $data = Casekind::findOrFail($id);
            return view('osce::admin.resourceManage.clinical_kind_manage_edit',['data'=>$data]);
        } catch (\Exception $ex) {
            return redirect()->back()->withErrors($ex->getMessage());
        }
    }

    /*
     * name：根据id修改病历
     * date：2018/2/7 17:54
     * author:Hsiaowei(phper.tang@qq.com)
     * param： int *
     * param： string *
     * return： array
     * */
    public function postEditKind(Request $request)
    {
        //验证,略过
        $this->validate($request, [
            'id' => 'required|integer',
            'name' => 'required'
        ],[
            'id.required'       =>  '病种id不能为空',
            'name.required'     =>  '病种名称不能为空'
        ]);

        try {
            $id = $request->input('id');
            $formData = $request->only('name', 'description');

            Casekind::where('id',$id)->update($formData);

            return redirect()->route('osce.admin.kind.getKindList');

        } catch (\Exception $ex) {
            return redirect()->back()->withErrors($ex->getMessage());
        }
    }
    /*
     * name：根据id删除病历
     * date：2018/2/7 17:53
     * author:Hsiaowei(phper.tang@qq.com)
     * param： int *
     * param： string *
     * return： array
     * */
    public function postDelete(Request $request)
    {
        try {
            //获取删除的id
            $id = $request->input('id');
            //将id传入删除的方法
            $result = Casekind::find($id)->delete();
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
     * @author fandian <fandian@sulida.com>     *
     */
    public function postNameUnique(Request $request)
    {
        $this->validate($request, [
            'name'      => 'required',
        ]);

        $id     = $request  -> get('id');
        $name   = $request  -> get('name');

        //查询 该名字 是否存在
        if(empty($id)){
            $result = Casekind::where('name', $name)->first();
        }else{
            $result = Casekind::where('name', $name)->where('id', '<>', $id)->first();
        }
        if($result){
            return json_encode(['valid' =>false]);
        }else{
            return json_encode(['valid' =>true]);
        }
    }

}