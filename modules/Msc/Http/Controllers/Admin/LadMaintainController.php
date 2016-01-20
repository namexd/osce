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
use Illuminate\Support\Facades\Auth;
use URL;
use DB;

class LadMaintainController extends MscController
{
    /**
     *实验室资源信息列表
     * @method GET
     * @url /msc/admin/ladMaintain/laboratory-list
     * @access public
     * @version 1.0
     * @author zhouqiang <zhouqiang@misrobot.com>
     * @date 2016年1月5日16:48:52
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function getLaboratoryList(){
//        dd(11111);
        $location =Floor::where('status','=',1)->get();
        return view('msc::admin.labmanage.resource_maintain',[
            'location'    => $location,
        ]);
    }

    /**
     *实验室相关设备信息
     *
     * @method GET
     * @url /msc/admin/LadMaintain/lab-id-get-laboratory-device-list
     * @access public
     *
     * @param Request $request post请求<br><br>
     * <b>post请求字段：</b>
     * *int      lad_id       实验室id(必须的)
     *
     * @return json
     *
     * @version 1.0
     * @author zhouqiang <zhouqiang@misrobot.com>
     * @date ${DATE} ${TIME}
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */

    public function getLaboratoryDeviceList(){
        $lab_id = Input::get('lab_id');
        $page = Input::get('page',1);
        $LadDevice = new LadDevice;


        //TODO 解决AJAX 翻页传递过来的页码过大  无法查询出来数据的问题
        $total = $LadDevice->where('lab_id','=',$lab_id)->count();
        $total_page = (int)ceil($total/config('msc.page_size',10));
        if($total_page<$page){
            return redirect()->route('msc.admin.LadMaintain.LaboratoryDeviceList', ['lab_id' => $lab_id,'page'=>$total_page]);
        }

        $LadDeviceList = $LadDevice->GetLadDevice($lab_id);
        return response()->json(
            $this->success_rows(1,'获取成功',$LadDeviceList->total(),config('msc.page_size',10),$LadDeviceList->currentPage(),array('LadDeviceList'=>$LadDeviceList->toArray()))
        );
    }

    /**
     *设备添加数据
     * @method GET
     * @url /msc/admin/ladMaintain/laboratory-list-data
     * @access public
     *
     * @param Request $request post请求<br><br>
     * <b>post请求字段：</b>
     * * string        keyword       试验室相关地址
     * * string        devicename     资源名称
     * * string        devicetype     资源类型名称
     *
     * @return json
     *
     * @version 1.0
     * @author zhouqiang <zhouqiang@misrobot.com>
     * @date 2016年1月5日16:49:00
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function getLaboratoryListData(Request $request){

        $this->validate($request, [
            'keyword' => 'sometimes',
            'devices_cate_id' => 'sometimes|integer',
            'lab_id'=>'required|integer'
        ]);
        $keyword = urldecode(e($request->input('keyword')));
        $devices_cate_id = (int)$request->input('devices_cate_id');
        $LadDevice = new LadDevice;
        //TODO 获取属于当前实验室的 设备ID
        $DeviceIdArr = $LadDevice->getLadDeviceId((int)$request->input('lab_id'));
        $devices = new Devices();
        $resourceData = $devices->getDevicesList($DeviceIdArr,$keyword,2,$devices_cate_id);
        $list = [];
        foreach ($resourceData as $itme) {
            $list[] = [
                'id' => $itme->id,
                'name' => $itme->name,
                'detail' => $itme->detail,
                'catename'=>$itme->catename,
                'devices_cate_id'=>$itme->devices_cate_id,
                'status' => is_null($itme->status) ? '-' : $itme->status,
            ];
        }
        $deviceType = DB::connection('msc_mis')->table('device_cate')->get();
        $data = [
            'list'=> $list,
            'keyword'=>$request->input('keyword')?$request->input('keyword'):'',
            'deviceType'  => $deviceType
        ];
        return response()->json(
            $this->success_rows(1,'获取成功',$resourceData->total(),config('msc.page_size',10),$resourceData->currentPage(),$data)
        );

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
            'lab_id' => 'required|integer',
            'device_id_num' => 'required'
        ]);
        $insertArr = [];
        $req = $request->all();
        $user = Auth::user();
        if(is_array($req['device_id_num'])){
            foreach($req['device_id_num'] as $v){
                $arr = explode(",",$v);
                $LabDevices['lab_id'] = $req['lab_id'];
                $LabDevices['device_id'] = $arr[0];
                $LabDevices['total'] = $arr[1];
                $LabDevices['created_at'] = date('Y-m-d H:i:s',time());
                $LabDevices['updated_at'] = date('Y-m-d H:i:s',time());
                $LabDevices['created_user_id'] = $user->id;
                $insertArr [] = $LabDevices;
            }
        }
        $return = DB::connection('msc_mis')->table('lab_device')->insert($insertArr);
        if($return){
            return response()->json(
                $this->success_data([],1,'添加成功')
            );
        }else{
            return response()->json(
                $this->success_data([],2,'添加失败')
            );
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
            $this->validate($request,[
                'lab_device_id' =>'required|integer',
                'total'    => 'required|integer'
            ]);
        $data = $request->only(['total']);
        $update = $ladDevice->where('id','=',urlencode(e(Input::get('lab_device_id'))))->update($data);
        if($update){
            return response()->json(
                $this->success_data([],1,'添加成功')
            );
        }else{
            return response()->json(
                $this->success_data([],2,'编辑失败')
            );
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
        $id = urlencode(e(Input::get('id')));
        if(!empty($id)){
            $data = LadDevice::find($id);
            $del = $data->delete();
            if($del){
                return response()->json(
                    $this->success_data([],1,'删除成功')
                );
            }else{
                return response()->json(
                    $this->success_data([],2,'删除失败')
                );
            }
        }else{
            return response()->json(
                $this->success_data([],3,'没有传入LadDevices表ID')
            );
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
            $where['status'] = 1;
            $where['location_id'] = $local_id;
            foreach($floor as $k=>$v){
                $where['floor'] = $v;
                $labArr[$k]['floor'] = $v;
                $data = Laboratory::where($where)->get();
                $labArr[$k]['lab'] = $data->toArray();
            }
            return $labArr;
        });
        //$str = json_encode($cacheData);
        return $cacheData;
        //$this->success_data($cacheData,1,'success');
//        return response()->json(
//            $this->success_data(['result' => true, 'cacheData' => $cacheData])
//        );
    }





}