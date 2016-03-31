<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/3/31 0031
 * Time: 15:47
 */

namespace Modules\Osce\Http\Controllers\Admin;


use Illuminate\Http\Request;
use Modules\Osce\Entities\Supply;
use Modules\Osce\Http\Controllers\CommonController;
class SupplyController extends CommonController
{
    
    
   
    /**
     * 获取用物列表
     * @method GET
     * @url osce/admin/supply/list
     * @access public
     * @param Request $request get请求<br><br>
     * <b>post请求字段：</b>
     * * string        name  用物名
     * *
     * @return ${response}
     *
     * @version 1.0
     * @author zhouqiang <zhouqiang@misrobot.com>
     * @date 2016-3-31
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public  function getList(Request $request)
    {


        $name = e($request->get('name'));

        $supplyModel = new Supply();

        $list = $supplyModel->getList($name);

        return view('osce::admin.resourceManage.res_manage', ['list' => $list, 'name' => $name]);
    }


    /**
     * 新增用物着陆页面
     * @method GET
     * @url osce/admin/supply/add-supply
     * @access public
     * @param Request $request get请求<br><br>
     * <b>get请求字段：</b>
     * @return ${response}
     * string        name  用物名
     * @version 1.0
     * @author zhouqiang <zhouqiang@misrobot.com>
     * @date 2016-3-31
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */

    public function getAddSupply(){
        return view('osce::admin.resourceManage.res_manage_add');
    }



    /**
     * 提交新增用物
     * @method GET
     * @url osce/admin/supply/add-supply
     * @access public
     * @param Request $request post请求<br><br>
     * <b>post请求字段：</b>
     * string        name  用物名
     * @return ${response}

     * @version 1.0
     * @author zhouqiang <zhouqiang@misrobot.com>
     * @date 2016-3-31
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */

    public function postAddSupply(Request $request){
        $this->validate($request,[
            'name'=>'required'
        ]);
        $name = $request->get('name');
        //添加进数据库
        $data=[
            'name'=>$name,
        ];

        if(!Supply::create($data)){
            throw new \Exception('添加用物失败');
        }else{
            return redirect()->route('osce.admin.supply.getList');
        }
    }




    /**
     * 编辑用物着陆页面
     * @method GET
     * @url osce/admin/supply/edit-supply
     * @access public
     * @param Request $request post请求<br><br>
     * <b>get请求字段：</b>
     * int        id  用物编号
     * @return ${response}

     * @version 1.0
     * @author zhouqiang <zhouqiang@misrobot.com>
     * @date 2016-3-31
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function getEditSupply(Request $request){
        $id = $request->get('id');

        $data = Supply::find($id);
        if(!$data){
            throw new \Exception('没有找到相关用物');
        }

        return view('osce::admin.resourceManage.res_manage_edit',['data'=>$data]);

    }
    
    /**
     * 提交编辑用物表单
     * @method GET
     * @url osce/admin/supply/edit-supply
     * @access public
     * @param Request $request post请求<br><br>
     * <b>post请求字段：</b>
     * int        id  用物编号
     * @return ${response}
     * @version 1.0
     * @author zhouqiang <zhouqiang@misrobot.com>
     * @date 2016-3-31
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public  function postEditSupply(Request $request){
        $this->validate($request,[
            'id'=>'required',
            'name'=>'required'
        ]);
        $id = $request->get('id');
        $name = $request->get('name');
        $Supply =Supply::find($id);
        $Supply->name =  $name;
        if(!$Supply->save()){
            throw new \Exception('修改用物失败');
        }else{
            return redirect()->route('osce.admin.supply.getList');
        }
    }



    /**
     * 删除用物
     * @method GET
     * @url osce/admin/supply/del-supply
     * @access public
     * @param Request $request post请求<br><br>
     * <b>post请求字段：</b>
     * int        id  用物编号
     * @return ${response}
     * @version 1.0
     * @author zhouqiang <zhouqiang@misrobot.com>
     * @date 2016-3-31
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public  function getDelSupply(Request $request){

        $this->validate($request, [
            'id' => 'required'
        ]);
        $id = $request->get('id');
        $SupplyModel = new Supply();
        $subject = $SupplyModel->find($id);
        try {

            $SupplyModel->delSubject($subject);
            return \Response::json(array('code' => 1));
        } catch (\Exception $ex) {
            return response()->json(
                $this->fail($ex)
            );
            //return redirect()->back()->withErrors($ex->getMessage());
        }

    }

    //ajax 获取用物列表

//    public function get


}