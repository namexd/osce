<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/3/31 0031
 * Time: 15:47
 */

namespace Modules\Osce\Http\Controllers\Admin;


use Illuminate\Http\Request;
use Modules\Osce\Entities\Supplies;
use Modules\Osce\Http\Controllers\CommonController;
class SuppliesController extends CommonController
{
    //获取用物列表
    public  function getList(Request $request)
    {


        $name = e($request->get('name'));

        $suppliesModel = new Supplies();

        $list = $suppliesModel->getList($name);

        return view('osce::admin.resourceManage.res_manage', ['list' => $list, 'name' => $name]);
    }

    //新增用物着陆页面
    public function getAddSupplies(){
        return view('osce::admin.resourceManage.res_manage_add');
    }

    //提交新增用物
    public function postAddSupplies(Request $request){
        $this->validate($request,[
            'name'=>'required'
        ]);

        $name = $request->get('name');
        //添加进数据库
        $data=[
            'name'=>$name,
        ];

        if(!Supplies::create($data)){
            throw new \Exception('添加用物失败');
        }
    }

    //编辑用物着陆页面
    public function getEditSupplies(Request $request){
        $id = $request->get('id');

        $data = Supplies::find($id);

        return view('osce::admin.resourceManage.res_manage_add',['data'=>$data]);

    }
    
    //提交编辑用物表单
    public  function postEditSupplies(Request $request){
        $this->validate($request,[
            'id'=>'required',
            'name'=>'required'
        ]);
        $id = $request->get('id');
        $name = $request->get('name');
        $data=[
            'id'=>$id,
            'name'=>$name
        ];

//        if(){
//
//        }

    }

    //删除用物
    public  function getDelSupplies(Request $request){

        $this->validate($request, [
            'id' => 'required'
        ]);
        $id = $request->get('id');
        $SuppliesModel = new Supplies();
        $subject = $SuppliesModel->find($id);
        try {
            $SuppliesModel->delSubject($subject);
            return \Response::json(array('code' => 1));
        } catch (\Exception $ex) {
            return response()->json(
                $this->fail($ex)
            );
            //return redirect()->back()->withErrors($ex->getMessage());
        }

    }



}