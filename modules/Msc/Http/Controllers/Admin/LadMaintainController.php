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
use Modules\Msc\Entities\Devices;
use Modules\Msc\Entities\Floor;
use Modules\Msc\Entities\Laboratory;
use Modules\Msc\Entities\LadDevice;
use Illuminate\Support\Facades\Cache;
use Modules\Msc\Http\Controllers\MscController;
use URL;
use DB;

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
     * * string        devicetype     资源类型名称
     *
     * @return view
     *
     * @version 1.0
     * @author zhouqiang <zhouqiang@misrobot.com>
     * @date ${DATE} ${TIME}
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */


    public function getLaboratoryList(Request $request){
        $this->validate($request, [
            'keyword' => 'sometimes',
            'devices_cate_id' => 'sometimes|integer'

        ]);
        $keyword = urldecode(e($request->input('keyword')));
        $devices_cate_id = (int)$request->input('devices_cate_id');
        //添加设备的资源数据
        $devices = new Devices();
        $resourceData = $devices->getDevicesList($keyword,$devices_cate_id);
        $deviceType = DB::connection('msc_mis')->table('device_cate')->get();

        //楼栋数据
        $location =Floor::where('status','=',1)->get();
//        dd($location);
        //楼栋的楼层及楼层试验室数据
        $FloorLad = $this->getFloorLab();
        dd($FloorLad);
        //试验室的设备数据
//        $LadDevices =new LadDevice();
//        $LadDevice = $LadDevices->getLadDevice();

//        dd($location);
        return view('msc::admin.labmanage.resource_maintain',[
            'resourceData' => $resourceData,
            'deviceType'  => $deviceType,
            'location'    => $location,
//            'LadDevice' =>  $LadDevice
        ]);
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





  //计算楼层层数
    public function getFloorNumber($ground ,$underground){
        $arr=array();
        $brr = array();
//        地下
        for($i=$underground;$i>0;$i--){
            $arr['-'.$i]='-'.$i;
        }
//        地上
        for($i=1;$i<=$ground;$i++){
            $brr[$i]=$i;
        }
        $data = array_merge($arr,$brr);
        return $data;
    }



    /**
     * 根据楼栋查找楼层及该楼层所有实验室
     * @method GET
     * @url /msc/admin/ladMaintain/floor-lab
     * @access public
     *
     * @param Request $request get请求<br><br>
     * <b>post请求字段：</b>
     * * string        lid     楼栋地址ID(必须的)
     *
     * @return  jsonl
     *
     * @version 1.0
     * @author zhouqiang <zhouqiang@misrobot.com>
     * @date ${DATE} ${TIME}
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function getFloorLab(){
        $cacheData = Cache::get('key',function() {
            $local_id = Input::get('lid');


            $local = Floor::where('id','=',$local_id)->first();

            $floor = $this->getFloorNumber($local['floor_top'],$local['floor_buttom']);

            $labArr = [];
            $where['status'] = 0;
            $where['location_id'] = $local_id;
            foreach($floor as $k=>$v){
                $where['floor'] = $v;
                $labArr[$k]['floor'] = $v;
                $labArr[$k]['lab'] = Laboratory::where($where)->get();

            }
            return $labArr;
        });
        $this->success_data($cacheData,1,'success');
//        return response()->json(
//            $this->success_data(['result' => true, 'cacheData' => $cacheData])
//        );
    }





}