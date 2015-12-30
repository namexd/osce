<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2015/12/30 0030
 * Time: 10:01
 */

namespace Modules\Msc\Http\Controllers\Admin;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Modules\Msc\Entities\Devices;
use Modules\Msc\Http\Controllers\MscController;

class ResourcesController extends MscController
{
    /**
     *资源列表
     * @method GET
     * @url /msc/admin/resources/resources-index
     * @access public
     *
     * @param Request $request get请求<br><br>
     * <b>post请求字段：</b>
     * * string        keyword       专业名称
     * * int           status        专业状态(1：正常，2：停用)
     * @return  view
     *
     * @version 1.0
     * @author zhouqiang <zhouqiang@misrobot.com>
     * @date ${DATE} ${TIME}
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */

    public function  getResourcesIndex(Request $request)
    {
        $this->validate($request, [
            'keyword ' => 'sometimes',
            'status' => 'sometimes|in:1,2',
            'devices_cate_id' => 'sometimes|in:1,2,3,4'
        ]);
        $keyword = urldecode(e($request->input('keyword ')));
        $status = (int)$request->input('status');
        $devices_cate_id = (int)$request->input('devices_cate_id');
        $devices = new Devices();
        $pagination = $devices->getDevicesList($keyword, $status, $devices_cate_id);
//        dd($pagination);


        $list = [];
        foreach ($pagination as $itme) {
            $list[] = [
                'id' => $itme->id,
                'name' => $itme->name,
                'detail' => $itme->detail,
                'catename'=>$itme->catename,
                'devices_cate_id'=>$itme->devices_cate_id,
                'status' => is_null($itme->status) ? '-' : $itme->status,
            ];
        }
        dd($list);
        return view('msc::admin.systemtable.resource_table',[
            'list'         =>       $list,
        ]);
    }


    /**
     * 新增资源
     *
     * @method post
     * @url /msc/admin/resources/resources-add
     * @access public
     *
     * @param Request $request post请求<br><br>
     * <b>post请求字段：</b>
     * * string        name       设备名(必须的)
     * *string         detail     设备说明(必须的)
     * * int            devices_cate_id   资源类型 (必须的)
     * * int            status       状态(必须的)
     * @return   json
     *
     * @version 1.0
     * @author zhouqiang <zhouqiang@misrobot.com>
     * @date ${DATE} ${TIME}
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */

    public function postResourcesAdd(Request $request){
        $this->validate($request,[
            'name'   => 'required|max:20',
            'devices_cate_id'=>'required',
            'detail'   =>  'required|max:20',
            'status' =>   'required|in:1,2'
        ]);
         $data=[
             'name'=>Input::get('name'),
             'devices_cate_id'=>Input::get('devices_cate_id'),
             'detail'=>Input::get('detail'),
             'status'=>Input::get('status'),
         ];
        $ResourcesAdd= Devices::create($data);
        if($ResourcesAdd != false){
            return redirect()->back()->withInput()->withErrors('添加成功');
        }else{
            return redirect()->back()->withInput()->withErrors('系统异常');
        }
    }




}


