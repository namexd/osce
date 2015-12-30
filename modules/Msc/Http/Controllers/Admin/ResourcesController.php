<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2015/12/30 0030
 * Time: 10:01
 */

namespace Modules\Msc\Http\Controllers\Admin;


use Illuminate\Http\Request;
use Modules\Msc\Entities\Devices;
use Modules\Msc\Http\Controllers\MscController;

class ResourcesController extends MscController
{
    //资源列表

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
        dd($pagination);


        $list = [];
        foreach ($pagination as $itme) {
            $list[] = [
                'id' => $itme->id,
                'name' => $itme->name,
                ' detail' => $itme->detail,
//            'status' =>$itme->status,
                'status' => is_null($itme->status) ? '-' : $itme->status,
            ];
        }
        return view('msc::admin.systemtable.resource_table',[
            'list'         =>       $list,
        ]);


    }






}


