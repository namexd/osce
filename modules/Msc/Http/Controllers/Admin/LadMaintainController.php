<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/1/4 0004
 * Time: 14:14
 */

namespace Modules\Msc\Http\Controllers\Admin;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Modules\Msc\Entities\LadDevice;
use Modules\Msc\Http\Controllers\MscController;

class LadMaintainController extends MscController
{
    /**
     *实验室资源信息列表
     * @method GET
     * @url /msc/admin/ladMaintain/laboratory-list
     * @access public
     *
     * @param Request $request post请求<br><br>
     * <b>post请求字段：</b>
     * * string        keyword       试验室相关地址
     * * string        devicename     资源名称
     * * string        devicetype     资源名称
     *
     * @return view
     *
     * @version 1.0
     * @author zhouqiang <zhouqiang@misrobot.com>
     * @date ${DATE} ${TIME}
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */


    public function getLaboratoryList(Request $request){
        $this->validate($request,[
            'keyword' => 'sometimes'
        ]);

        dd(1111111111111);
        return view('msc::admin.labmanage.lab_maintain');

    }

    /**
     *   设备添加
     * @method GET
     * @url /msc/admin/ladMaintain/devices-add
     * @access public
     *
     * @param Request $request post请求<br><br>
     * <b>post请求字段：</b>
     * * int       lad_id        实验室id(必须的)
     * * int       device_id      设备资源id(必须的)
     * * int       total           设备数量(必须的)
     *
     * @return json
     *
     * @version 1.0
     * @author zhouqiang <zhouqiang@misrobot.com>
     * @date ${DATE} ${TIME}
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */

    public  function postDevicesAdd(Request $request){
        $this->validate($request,[
            'lad_id' => 'required|integer',
            'device_id' => 'required|integer',
            'total'    => 'required|integer',
        ]);

        $data=[
            'lad_id'=> Input::get('lad_id'),
            'device_id' =>Input::get('device_id'),
            'total'    => Input::get('total')
        ];
        $add = LadDevice::create($data);
        if($add != false){
            return redirect()->back()->withInput()->withErrors('添加成功');
        }else{
            return redirect()->back()->withInput()->withErrors('系统异常');
        }
    }

    /**
     *实验室设备数量编辑
     * @method GET
     * @url /msc/admin/ladMaintain/devices-total-edit
     * @access public
     *
     * @param Request $request get请求<br><br>
     * <b>post请求字段：</b>
     * * string        id       (必须的)
     *
     * @return json
     *
     * @version 1.0
     * @author zhouqiang <zhouqiang@misrobot.com>
     * @date ${DATE} ${TIME}
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */

    public function getDevicesTotalEdit(Request $request,LadDevice $ladDevice){
//        dd(111111111);
            $this->validate($request,[
                'total'    => 'required|integer',
            ]);
        $data = $request->only(['total']);
        $update = $ladDevice->where('id','=',urlencode(e(Input::get('id'))))->update($data);
        if($update != false){
            return redirect()->back()->withInput()->withErrors('编辑成功');
        }else{
            return redirect()->back()->withInput()->withErrors('系统异常');
        }

    }

    /**
     *实验室设备删除
     * @method GET
     * @url /msc/admin/ladMaintain/lad-devices-deletion
     * @access public
     *
     * @param Request $request get请求<br><br>
     * <b>post请求字段：</b>
     * * string        id       (必须的)
     *
     * @return  Errors
     *
     * @version 1.0
     * @author zhouqiang <zhouqiang@misrobot.com>
     * @date ${DATE} ${TIME}
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */

    public function getLadDevicesDeletion(){
//        dd(22222222222);

        $id = urlencode(e(Input::get('id')));
        //dd($id);
        if($id){
            $data = LadDevice::find($id);
            $del = $data->delete();
            //dd($del);
            if($del != false){
                return redirect()->back()->withInput()->withErrors('删除成功');
            }else{
                return redirect()->back()->withInput()->withErrors('系统异常');
            }
        }else{
            return redirect()->back()->withInput()->withErrors('系统异常');
        }

    }





}