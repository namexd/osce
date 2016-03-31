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
    
    
   
    /**
     * 获取用物列表
     * @method GET
     * @url osce/admin/supplies/list
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

        $suppliesModel = new Supplies();

        $list = $suppliesModel->getList($name);

        return view('osce::admin.resourceManage.res_manage', ['list' => $list, 'name' => $name]);
    }


    /**
     * 新增用物着陆页面
     * @method GET
     * @url osce/admin/supplies/add-supplies
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

    public function getAddSupplies(){
        return view('osce::admin.resourceManage.res_manage_add');
    }



    /**
     * 提交新增用物
     * @method GET
     * @url osce/admin/supplies/add-supplies
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
        }else{
            return redirect()->route('osce.admin.supplies.getList');
        }
    }




    /**
     * 编辑用物着陆页面
     * @method GET
     * @url osce/admin/supplies/edit-supplies
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
    public function getEditSupplies(Request $request){
        $id = $request->get('id');

        $data = Supplies::find($id);
        if(!$data){
            throw new \Exception('没有找到相关用物');
        }

        return view('osce::admin.resourceManage.res_manage_edit',['data'=>$data]);

    }
    
    /**
     * 提交编辑用物表单
     * @method GET
     * @url osce/admin/supplies/edit-supplies
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
    public  function postEditSupplies(Request $request){
        $this->validate($request,[
            'id'=>'required',
            'name'=>'required'
        ]);
        $id = $request->get('id');
        $name = $request->get('name');
        $Supplies =Supplies::find($id);
        $Supplies->name =  $name;
        if(!$Supplies->save()){
            throw new \Exception('修改用物失败');
        }else{
            return redirect()->route('osce.admin.supplies.getList');
        }
    }



    /**
     * 删除用物
     * @method GET
     * @url osce/admin/supplies/del-supplies
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

    //ajax 获取用物列表

//    public function get


}