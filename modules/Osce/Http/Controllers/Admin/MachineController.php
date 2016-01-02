<?php
/**
 * Created by PhpStorm.
 * User: fengyell <Luohaihua@misrobot.com>
 * Date: 2015/12/28
 * Time: 15:52
 */

namespace Modules\Osce\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Modules\Osce\Entities\Vcr;
use Modules\Osce\Entities\MachineCategory;
use Modules\Osce\Http\Controllers\CommonController;

class MachineController extends CommonController
{
    /**
     * 设备类型列表
     * @api GET /osce/admin/machine/category-list
     * @access public
     *
     * @param Request $request get请求<br><br>
     * <b>get请求字段：</b>
     * * string        参数英文名        参数中文名(必须的)
     *
     * @return view
     *
     * @version 1.0
     * @author Luohaihua <Luohaihua@misrobot.com>
     * @date 2015-12-30 10:59
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     *
     */
    public function getCategoryList(){
        $MachineCategoryModel   =   new MachineCategory();

        $pagination             =   $MachineCategoryModel   ->  paginate('osce.page_size');

        //return view('',['list'=>$pagination]);
    }

    /**
     *  新增设备类别
     * @url POST /osce/admin/machine/add-category
     * @access public
     *
     * @param Request $request post请求<br><br>
     * <b>post请求字段：</b>
     * * string        参数英文名        参数中文名(必须的)
     * * string        参数英文名        参数中文名(必须的)
     * * string        参数英文名        参数中文名(必须的)
     * * string        参数英文名        参数中文名(必须的)
     *
     * @return view
     *
     * @version 1.0
     * @author Luohaihua <Luohaihua@misrobot.com>
     * @date 2015-12-30 11:26
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     *
     */
    public function postAddCategory(Request $request){
        $this   ->  validate($request,[
            'name'      =>  'required',
        ]);

        $data   =   [
            'name'  =>  e($request    ->  get('name')),
        ];
        try
        {
            $category   =   MachineCategory::firstOrCreate($data);
            if($category)
            {
                return redirect()->route('osce.admin.machine.getCategoryList');
            }
            else
            {
                throw new \Exception('新增失败');
            }
        }
        catch(\Exception $ex)
        {
            return redirect()->back()->withErrors($ex);
        }
    }

    /**
     *  新增设备类别（表单显示页面）
     * @url GET /osce/admin/machine/add-category
     * @access public
     *
     * @param Request $request get请求<br><br>
     * <b>get请求字段：</b>
     * * string        参数英文名        参数中文名(必须的)
     * * string        参数英文名        参数中文名(必须的)
     * * string        参数英文名        参数中文名(必须的)
     * * string        参数英文名        参数中文名(必须的)
     *
     * @return view
     *
     * @version 1.0
     * @author Luohaihua <Luohaihua@misrobot.com>
     * @date 2015-12-30 11:27
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     *
     */
    public function getAddCategory(Request $request){

        //return view();
    }

    /**
     * 获取设备列表
     * @api GET /osce/admin/machine/machine-list
     * @access public
     *
     * @param Request $request post请求<br><br>
     * <b>post请求字段：</b>
     * * string        参数英文名        参数中文名(必须的)
     * * string        参数英文名        参数中文名(必须的)
     * * string        参数英文名        参数中文名(必须的)
     * * string        参数英文名        参数中文名(必须的)
     *
     * @return view
     *
     * @version 1.0
     * @author Luohaihua <Luohaihua@misrobot.com>
     * @date 2015-12-30 11:45
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     *
     */
    public function getMachineList(Request $request){
        $this   ->  validate($request,[
            'cate_id'   =>  'sometimes|integer'
        ]);

        $cate_id    =   intval($request   ->  get('cate_id'));
        if(empty($cate_id))
        {
            $cate   =   MachineCategory::first();
            if(is_null($cate))
            {
                abort(404,'设备类别不存在，请检查数据或联系管理员');
            }
            $cate_id    =   $cate   ->  id;
        }

        $model  =   $this   ->  getMachineModel($cate_id);
        $categroyList   =   MachineCategory::all(['id','name']);
        $list   =   $model  ->  paginate(config('osce.page_size'));
        $machineStatuValues   =   $model  ->  getMachineStatuValues();

        return view('osce::admin.resourcemanage.equ_manage_vcr',['list'=>$list,'options'=>$categroyList,'machineStatuValues'=>$machineStatuValues]);
    }

    /**
     * 获取对应类别下的扩展模型(可能是临时方法，可能会在数据库正式出来以后删除，目前就只有getMachineList方法使用)
     * @access private
     * @param
     * * string        $cate_id        类别ID(必须的)
     *
     * @return object
     *
     * @version 1.0
     * @author Luohaihua <Luohaihua@misrobot.com>
     * @date 2015-12-30 14：17
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     *
     */
    private function getMachineModel($cate_id){
        $config =   [
            1   =>  'Vcr',
        ];
        $name   =   '\Modules\Osce\Entities\\'.$config[$cate_id];
        return  new $name;
    }

    /**
     * 新增设备
     * @api POST /osce/admin/invigilator/add-machine
     * @access public
     *
     * @param Request $request post请求<br><br>
     * <b>post请求字段：</b>
     * 摄像机
     * * string        参数英文名        参数中文名(必须的)
     * * string        参数英文名        参数中文名(必须的)
     * * string        参数英文名        参数中文名(必须的)
     * * string        参数英文名        参数中文名(必须的)
     *  Pad
     * * string        参数英文名        参数中文名(必须的)
     * * string        参数英文名        参数中文名(必须的)
     * * string        参数英文名        参数中文名(必须的)
     * * string        参数英文名        参数中文名(必须的)
     *  腕表
     * * string        参数英文名        参数中文名(必须的)
     * * string        参数英文名        参数中文名(必须的)
     * * string        参数英文名        参数中文名(必须的)
     * * string        参数英文名        参数中文名(必须的)
     *
     * @return redirect
     *
     * @version 1.0
     * @author Luohaihua <Luohaihua@misrobot.com>
     * @date 2015-12-30 15:26
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     *
     */
    public function postAddMachine(Request $request){
        $this   ->  validate($request,[
            'cate_id'   =>  'required|integer'
        ]);

        $cate_id    =   $request    ->  get('cate_id');
        try{
            switch($cate_id)
            {
                case 1:
                    $machine    =     $this   ->  addCameras($request);
                    break;
                default :
                    $machine    =     $this   ->  addCameras($request);
            }
            if($machine)
            {
                return redirect()   ->  route('osce.admin.machine.getMachineList',['cate_id'=>$cate_id]) ;
            }
            else
            {
                throw new \Exception('新增设备失败');
            }
        }
        catch(\Exception $ex)
        {
            return redirect()->back()->withErrors($ex);
        }
    }

    /**
     * 编辑设备信息
     * @url /osce/admin/machine/edit-machine
     * @access public
     *
     * * @param Request $request
     * <b>get 请求字段：</b>
     * * string        id           摄像机ID(必须的)
     * * string        name         摄像机名称(必须的)
     * * string        code         摄像机编码(必须的)
     * * string        ip           摄像机IP(必须的)
     * * string        username     摄像机用户名(必须的)
     * * string        password     摄像机密码(必须的)
     * * string        port         摄像机端口(必须的)
     * * string        channel      摄像机频道(必须的)
     * * string        description  摄像机描述(必须的)
     *
     * @return redirect
     *
     * @version 1.0
     * @author Luohaihua <Luohaihua@misrobot.com>
     * @date 2016-01-02 15:15
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     *
     */
    public function postEditMachine(Request $request){
        $this   ->  validate($request,[
            'cate_id'   =>  'required|integer'
        ]);

        $cate_id    =   $request    ->  get('cate_id');
        try{
            switch($cate_id)
            {
                case 1:
                    $machine    =     $this   ->  editCameras($request);
                    break;
                default :
                    $machine    =     $this   ->  editCameras($request);
            }
            if($machine)
            {
                return redirect()   ->  route('osce.admin.machine.getMachineList',['cate_id'=>$cate_id]) ;
            }
            else
            {
                throw new \Exception('编辑设备失败');
            }
        }
        catch(\Exception $ex)
        {
            return redirect()->back()->withErrors($ex);
        }
    }

    /**
     * 新增摄像头
     * @access private
     *
     * * @param Request $request
     * * string        name         摄像机名称(必须的)
     * * string        code         摄像机编码(必须的)
     * * string        ip           摄像机IP(必须的)
     * * string        username     摄像机用户名(必须的)
     * * string        password     摄像机密码(必须的)
     * * string        port         摄像机端口(必须的)
     * * string        channel      摄像机频道(必须的)
     * * string        description  摄像机描述(必须的)
     *
     * @return pbject
     *
     * @version 1.0
     * @author Luohaihua <Luohaihua@misrobot.com>
     * @date 2015-12-30 18.49
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     *
     */
    private function addCameras(Request $request){
        $this   ->  validate($request,[
            'name'          =>  'required',
            'code'          =>  'required',
            'ip'            =>  'required',
            'username'      =>  'required',
            'password'      =>  'required',
            'port'          =>  'required',
            'channel'       =>  'required',
            'description'   =>  'sometimes',
            'status'        =>  'required',
        ],[
            'name.required'     =>'设备名称必填',
            'code.required'     =>'设备编码必填',
            'ip.required'       =>'设备IP地址必填',
            'username.required' =>'设备登录用户名必填',
            'password.required' =>'设备登录密码必填',
            'port.required'     =>'设备端口必填',
            'channel.required'  =>'设备网口必填',
            'status.required'   =>'设备状态必选',
        ]);
        $data   =   [
            'name'          =>  $request    ->  get('name'),
            'code'          =>  $request    ->  get('code'),
            'ip'            =>  $request    ->  get('ip'),
            'username'      =>  $request    ->  get('username'),
            'password'      =>  $request    ->  get('password'),
            'port'          =>  $request    ->  get('port'),
            'channel'       =>  $request    ->  get('channel'),
            'description'   =>  $request    ->  get('description'),
            'status'        =>  $request    ->  get('status'),
        ];
        $cate_id    =   $request    ->  get('cate_id');
        try{

            $model      =   $this   ->  getMachineModel($cate_id);
            if($cameras =   $model  ->  addMachine($data))
            {
                return $cameras;
            }
            else
            {
                throw new \Exception('新增摄像头失败');
            }
        }
        catch(\Exception $ex)
        {
            throw $ex;
        }
    }

    /**
     * 编辑摄像头
     * @access private
     *
     * * @param Request $request
     * <b>get 请求字段：</b>
     * * string        参数英文名        参数中文名(必须的)
     * * string        参数英文名        参数中文名(必须的)
     * * string        参数英文名        参数中文名(必须的)
     * * string        参数英文名        参数中文名(必须的)
     *
     * @return object
     *
     * @version 1.0
     * @author Luohaihua <Luohaihua@misrobot.com>
     * @date 2016-01-02 15:16
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     *
     */
    private function editCameras(Request $request){
        $this   ->  validate($request,[
            'id'            =>  'required',
            'name'          =>  'required',
            'code'          =>  'required',
            'ip'            =>  'required',
            'username'      =>  'required',
            'password'      =>  'required',
            'port'          =>  'required',
            'channel'       =>  'required',
            'description'   =>  'sometimes',
        ],[
            'id.required'       =>'设备ID必填',
            'name.required'     =>'设备名称必填',
            'code.required'     =>'设备编码必填',
            'ip.required'       =>'设备IP地址必填',
            'username.required' =>'设备登录用户名必填',
            'password.required' =>'设备登录密码必填',
            'port.required'     =>'设备端口必填',
            'channel.required'  =>'设备网口必填',
        ]);
        $data   =   [
            'id'            =>  $request    ->  get('id'),
            'name'          =>  $request    ->  get('name'),
            'code'          =>  $request    ->  get('code'),
            'ip'            =>  $request    ->  get('ip'),
            'username'      =>  $request    ->  get('username'),
            'password'      =>  $request    ->  get('password'),
            'port'          =>  $request    ->  get('port'),
            'channel'       =>  $request    ->  get('channel'),
            'description'   =>  $request    ->  get('description'),
        ];
        $cate_id    =   $request    ->  get('cate_id');
        try{
            $model      =   $this   ->  getMachineModel($cate_id);
            if($cameras =   $model  ->  editMachine($data))
            {
                return $cameras;
            }
            else
            {
                throw new \Exception('编辑摄像头失败');
            }
        }
        catch(\Exception $ex)
        {
            throw $ex;
        }
    }
    public function getAddCameras(){
        return view('osce::admin.resourcemanage.vcr_add');
    }
    public function getEditCameras(Request $request){
        $this   ->  validate($request,[
            'id'   =>  'required|integer'
        ]);

        $id     =   intval($request    ->  get('id'));
        $vcr    =   Vcr::find($id);

        return view('osce::admin.resourcemanage.vcr_edit',['item'=>$vcr]);
    }
}