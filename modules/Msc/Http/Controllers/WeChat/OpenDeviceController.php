<?php
/**
 * 开放设备控制器
 * Created by PhpStorm.
 * User: wangjiang
 * Date: 2015/12/7 0007
 * Time: 14:01
 */

namespace Modules\Msc\Http\Controllers\WeChat;

use App\Entities\User;
use Illuminate\Support\Facades\Input;
use Modules\Msc\Entities\ResourcesClassroom;
use Modules\Msc\Entities\ResourcesDevice;
use Modules\Msc\Entities\ResourcesDeviceApply;
use Modules\Msc\Entities\ResourcesLabHistory;
use Modules\Msc\Entities\ResourcesOpenLabPlan;
use Modules\Msc\Entities\Teacher;
use Modules\Msc\Http\Controllers\MscWeChatController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Modules\Msc\Entities\ResourcesDeviceCate;
use Modules\Msc\Entities\ResourcesDevicePlan;
use Modules\Msc\Entities\ResourcesDeviceHistory;
use DB;

class OpenDeviceController extends MscWeChatController
{
    /**
     * 开放设备预约首页
     * @method GET /msc/wechat/open-device/open-tools-order-index
     * @access public
     *
     * @param Request $request get请求<br><br>
     * <b>get请求字段：</b>
     * * string        参数英文名        参数中文名(必须的)
     *
     * @return view
     *
     * @version 0.7
     * @author wangjiang <wangjiang@misrobot.com>
     * @date 2015-12-7 14:23
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function getOpenToolsOrderIndex (Request $request)
    {
        $data=[
            'js'=> $this->GenerationJsSdk()
        ];
        return view('msc::wechat.opendevice.opendevice_homepage',$data);
    }
    /**
     * 开放设备预约查询页面-页面
     * @method GET /msc/wechat/open-device/open-tools-order-search
     * @access public
     *
     * @param Request $request get请求<br><br>
     * <b>get请求字段：</b>
     * * string        参数英文名        参数中文名(必须的)
     *
     * @return view
     *
     * @version 0.7
     * @author wangjiang <wangjiang@misrobot.com>
     * @date 2015-12-7 15:06
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function getOpenToolsOrderSearch (Request $request)
    {
        $openDeviceCates = new ResourcesDeviceCate();
        $cateList   =   $openDeviceCates->get();
        return view('msc::wechat.opendevice.opendevice_bespoken',['cateList'=>$cateList]);
    }

    /**
     * 开放设备预约查询页面-数据
     * @method GET /msc/wechat/open-device/open-tools-order-search
     * @access public
     *
     * @param Request $request post请求<br><br>
     * <b>post请求字段：</b>
     * * int        $cate_id        开放设备类型id
     * * string     $date           查询时间(eg:2015/11/11)
     *
     * @return json
     *
     * @version 0.7
     * @author wangjiang <wangjiang@misrobot.com>
     * @date 2015-12-7 15:08
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function postOpenToolsOrderSearch (Request $request)
    {
        $this->validate($request, [
            'cate_id' => 'required|integer',
            'date'    => 'required|date_format:Y-m-d',
        ]);

        $cateId = $request->input('cate_id');
        $date   = $request->input('date');
        empty($cateId) ? 1 : $cateId;
        $resourcesDevice = new ResourcesDevice();
        $list = $resourcesDevice->getAvailableList($cateId, $date);
//        return response()->json($list);
        if ($list)
        {
            return response()->json(
                $this->success_rows(1,'获取成功',$list->lastPage(),config('msc.page_size',10),$list->currentPage(),['list'=>$list->toArray()])
            );
        }
        else
        {
            return response()->json(false); // 获取数据失败
        }
    }

    /**
     * 根据开放设备id和日期获得其可预约的时间段及状态
     * @method GET /msc/wechat/open-device/open-tools-time-sec/{id}/{date}
     * @access public
     *
     * @param Request $request post请求<br><br>
     * <b>post请求字段：</b>
     * * string        $id        开放设备编号
     * * string        $date      日期
     *
     * @return view   {'status'=>1(2) 1->已预约  2->可预约 , 'time'=>'8:00-9:00', 'id'=>1(设备id)}
     *
     * @version 0.7
     * @author wangjiang <wangjiang@misrobot.com>
     * @date 2015-12-7 17:37
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function getOpenToolsTimeSec ($id, $date)
    {    	
        $id = intval($id);		

        $resourceDevice = new ResourcesDevice();
        $data = $resourceDevice->getTimeList($id, $date);


        return view('msc::wechat.opendevice.opendevice_bespokentime',['data'=>$data]);       
    }

    /**
     * 根据开放设备id和时间段发起一条申请-表单
     * @method GET /msc/wechat/open-device/open-tools-apply/{id}/{date}/{timeSec}
     * @access public
     *
     * @param Request $request get请求<br><br>
     * <b>get请求字段：</b>
     * * int           $id             设备id
     * * string        $date           预约日期
     * * string        $timeSec        预约时间段(eg:8:00-9:00)
     *
     * @return view
     *
     * @version 0.7
     * @author wangjiang <wangjiang@misrobot.com>
     * @date 2015-12-7 19:07
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function getOpenToolsApply ($id, $date, $timeSec)
    {
        $id = intval($id);

        $resourcesDevice = ResourcesDevice::findOrFail($id);
        $data = [
            'id'       => $id, // 设备id
            'name'     => $resourcesDevice->name, // 设备名字
            'code'     => $resourcesDevice->code, // 设备编码
            'timeSec'  => $timeSec, // 时间段
            'date'     => $date, // 日期
            //'userId'   => Auth::user()->id, // 用户id
            'userName' => Auth::user()->name, // 用户名字
        ];

        return view('msc::wechat.opendevice.opendevice_apply',['data'=>$data]);
    }

    /**
     * 根据开放设备id和时间段发起一条申请-处理
     * @method GET /msc/wechat/open-device/open-tools-apply
     * @access public
     *
     * @param Request $request get请求<br><br>
     * <b>get请求字段：</b>
     * * string        $date            预约日期
     * * string        $timeSec         预约时间段(eg:8:00-9:00)
     * * string        $detail          申请说明
     * * deviceId      $deviceId        开放设备id
     * @return view
     *
     * @version 0.7
     * @author wangjiang <wangjiang@misrobot.com>
     * @date 2015-12-7 19:21
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function postOpenToolsApply (Request $request)
    {    	
        $this->validate($request, [
            //'uid'      => 'required|integer',
            'date'     => 'required|date_format:Y-m-d', // TODO 注意格式
            'timeSec'  => 'required|string',
            'detail'   => 'required|string|max:255', // TODO 是否必须
            'deviceId' => 'required|integer',
        ]);

        //用户信息
        $user = Auth::user();
        $uid  = $user->id;

        // 判断是不是老师
        $teacher = Teacher::find($uid);

        // 时间处理
        $date      = $request->input('date');
        $time      = $request->input('timeSec');
        $timeArray = explode('-', $time);

        // 申请理由
        $detail = $request->input('detail');

        // 设备id
        $deviceId = $request->input('deviceId');

        $data = [
            'apply_user_type'         => $teacher ? 1 : 0,
            'apply_type'              => 0, // 正常申请
            'apply_uid'               => $uid,
            'original_begin_datetime' => date('Y-m-d H:i:s', (strtotime($date .' '.$timeArray['0']))),
            'original_end_datetime'   => date('Y-m-d H:i:s', (strtotime($date .' '.$timeArray['1']))),
            'detail'                  => $detail,
            'status'                  => 0, // 待审核
            'resources_device_id'     => $deviceId,
        ];

        $result = ResourcesDeviceApply::create($data);

        if ($result)
		{
            return redirect()->intended('/msc/wechat/open-device/open-tools-order-index');
		}
    }

    /**
     * 开放设备使用完毕后扫描信息 - 1.“使用时间”从计划表读   2.“使用时长”从历史记录表读(当前时间-开始时间)
     * @method GET /msc/wechat/open-device/open-tools-used-info
     * @access public
     *
     * @param Request $request get请求<br><br>
     * <b>get请求字段：</b>
     * * int        $code     设备code
     *
     * @return view
     *
     * @version 0.7
     * @author wangjiang <wangjiang@misrobot.com>
     * @date 2015-12-8 11:18
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function getOpenToolsUsedInfo ($code)
    {
        // 使用者信息
        $user = Auth::user();

        $resourcesDevice     = new ResourcesDevice();
        $resourcesDevicePlan = new ResourcesDevicePlan();

        if(!empty($code)){
            $DeviceInfo = $resourcesDevice->where('code','=',$code)->get()->first();

            if(!empty($DeviceInfo->id)){

                $data['resources_device_id'] = $DeviceInfo->id;
                $data['uid']                 = $user->id;				

                $DevicePlanInfo = $resourcesDevicePlan->getDeviceInfo($data, 1);									              
            }else{
                return view('msc::wechat.index.index_error',array('error_msg'=>'没有相关设备'));
            }
        }else{
            return view('msc::wechat.index.index_error',array('error_msg'=>'没有传入code'));
        }
		
        // 从历史记录表读取开始使用时间        
        $timeLength = time()-strtotime($DevicePlanInfo->ResourcesDeviceHistory->begin_datetime);

        $data = [
            'userId'            => $user->id, // 使用者id
            'userName'          => $user->name, // 使用者姓名
            'deviceId'          => $DevicePlanInfo->device->id, // 设备id
            'planId'            => $DevicePlanInfo->id, // 计划id
            'deviceName'        => $DevicePlanInfo->device->name, // 设备名字
            'timeSec'           => $DevicePlanInfo->currentdate.' '.date('H:i', strtotime($DevicePlanInfo->begintime)).'-'.date('H:i', strtotime($DevicePlanInfo->endtime)), // 使用时间段
            'timeLengthHour'    => date('H', $timeLength), // 使用时长(时)
            'timeLengthMinute'  => date('i', $timeLength), // 使用时长(分)
            'timeLengthSecond'  => date('s', $timeLength), // 使用时长(秒)
            'result_init'       => 1, // 默认良好(1-良好 2-损坏 3-严重损坏)
            'result_poweroff'   => 1, // 默认关机
        ];
		
        return view('msc::wechat.opendevice.end_confirm',['data'=>$data]);
    }

    /**
     * 使用完开放设备后确定离开 - 1.计划表状态改为“已使用”  2.历史表写入结束时间
     * @method GET /msc/wechat/open-device/add-open-tools-history
     * @access public
     *
     * @param Request $request post请求<br><br>
     * <b>post请求字段：</b>
     * * int        $deviceId   设备编号
     *
     * @return response
     *
     * @version 0.7
     * @author wangjiang <wangjiang@misrobot.com>
     * @date 2015-12-8 14:07
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function postAddOpenToolsHistory (Request $request)
    {
        $this->validate($request, [
            'deviceId'   => 'required|integer',
        ]);

        $deviceId = $request->input('deviceId');

        // 预约人信息
        $user = Auth::user();

        // 获得设备相关信息
        $data['resources_device_id'] = $deviceId;
        $data['uid']                 = $user->id;
        $resourcesDevicePlan = new ResourcesDevicePlan();
        $DevicePlanInfo      = $resourcesDevicePlan->getDeviceInfo($data, 1);		

        // 更新计划表状态  历史表写入结束时间
        $planId    = $DevicePlanInfo->id;
        $historyId = $DevicePlanInfo->ResourcesDeviceHistory ->id;
        $resourcesDevicePlan->afterUseDevice($planId, $historyId);

        // 实验室信息
        $lab = ResourcesClassroom::findOrFail($DevicePlanInfo->device->resources_lab_id);

        $msg = '你的设备使用情况'
                .'预约人信息：'.$DevicePlanInfo->user->name
                .'设备名称：'.$DevicePlanInfo->device->name
                .'设备编号：'.$DevicePlanInfo->device->code
                .'使用的时间段：'.$DevicePlanInfo->currentdate.' '.date('H:i', strtotime($DevicePlanInfo->begintime)).'-'.date('H:i', strtotime($DevicePlanInfo->endtime))
                .'地址：'.$lab->location
                .'设备状态：良好' // 默认关机
                .'是否复位设备：是'; // 默认良好
        try
        {
            $this->sendMsg($user->openid, $msg);
        }
        catch (\Exception $e)
        {
            throw new \Exception('发送微信通知失败');
        }

        return redirect()->route('wechat.lab-tools.getOpenToolsOrderIndex');
    }

    /**o
     * 开放设备延时使用申请-表单
     * @method GET /msc/wechat/open-device/open-tools-delay
     * @access public
     *
     * @param Request $request get请求<br><br>
     * <b>get请求字段：</b>
     * * int        $deviceId        开放设备编号
     * * string     $planId          开放设备使用计划编号
     *
     * @return view
     *
     * @version 0.7
     * @author wangjiang <wangjiang@misrobot.com>
     * @date 2015-12-8 14:14
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function getOpenToolsDelay ($deviceId, $planId)
    {
        $deviceId = intval($deviceId);
        $planId   = intval($planId);

        $plan   = ResourcesDevicePlan::findOrFail($planId);
        $device = ResourcesDevice::findOrFail($deviceId);

        $data = [
            'id'        => $deviceId, // 设备id
            'name'      => $device->name, // 设备名称
            'usedTime'  => date('H:i', strtotime($plan->begintime)).'-'.date('H:i', strtotime($plan->endtime)), // 已使用的时间段
            'delayTime' => date('H:i', strtotime($plan->endtime)).'-'.date('H:i', (strtotime($plan->endtime)+($device->max_use_time) * 60)), // 申请的时间段-默认下一个时间段
            'date'      => $plan->currentdate, // 日期
        ];
		
        return view('msc::wechat.opendevice.opendevice_renew',['data'=>$data]);
    }

    /**
     * 开放设备延时使用申请-处理
     * @method POST /msc/wechat/open-device/open-tools-delay
     * @access public
     *
     * @param Request $request post请求<br><br>
     * <b>post请求字段：</b>
     * * int        $deviceId         开放设备编号
     * * string     $timeSec          申请开放设备使用时间段(eg:10:00-11:00)
     * * string     $date             使用日期
     *
     * @return view
     *
     * @version 0.7
     * @author wangjiang <wangjiang@misrobot.com>
     * @date 2015-12-8 14:14
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function postOpenToolsDelay (Request $request)
    {
        $this->validate($request, [
            'deviceId'   => 'required|integer',
            'timeSec'    => 'required|string',
            'date'       => 'required|date_format:Y-m-d',
        ]);

        $deviceId = $request->input('deviceId');
        $date     = $request->input('date');
        $timeSec  = $request->input('timeSec');

        $timeArray = explode('-', $timeSec);

        $resourcesDevicePlan = new ResourcesDevicePlan();
        $result = $resourcesDevicePlan->checkConflicts($deviceId, $date, $timeArray['0'], $timeArray['1']);

        $msg = '';
        if ($result)
        {
            // 不通过审核
            $msg = '抱歉，你的开放设备预约延时使用申请审核未通过。请你改天再次进行开放设备预约申请。';
        }
        else
        {
            // 通过
            $data = [
                'resources_device_id' => $deviceId,
                'currentdate'         => $date,
                'begintime'           => $timeArray['0'],
                'endtime'             => $timeArray['1'],
                'status'              => 0,
            ];
            $result = ResourcesDevicePlan::create($data);
            if ($result)
            {
                // 设备信息
                $device = ResourcesDevice::findOrFail($deviceId);
                // 实验室信息
                $lab = ResourcesClassroom::findOrFail($device->resources_lab_id);

                $msg = '恭喜你，开放设备预约延时申请审核通过！'
                    .'设备名称：'.$device->name
                    .'设备编号：'.$device->code
                    .'预约时间段：'.$timeSec
                    .'地址：'.$lab->location;
            }
            else
            {
                $msg = '抱歉，你的开放设备预约延时使用申请审核未通过。请你改天再次进行开放设备预约申请。';
            }
        }

        // 用户信息
        $user = Auth::user();
        try
        {
            $this->sendMsg($user->openid, $msg);
        }
        catch (\Exception $e)
        {
            throw new \Exception('发送微信通知失败');
        }

        return redirect()->route('wechat.lab-tools.getOpenToolsOrderIndex');
    }

    /**
     * 开放实验室历史记录列表
     * @method GET /msc/wechat/open-device/history-list
     * @access public
     *
     * @param Request $request get请求<br><br>
     * <b>get请求字段：</b>
     * @return view
     *
     * @version 0.7
     * @author tangjun <tangjun@misrobot.com>
     * @date 2015-12-8 11:02
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function getHistoryList(){
        return view('msc::wechat.resource.opendevice_history');
    }

    /**
     * 开放实验室历史记录列表数据
     * @method GET /msc/wechat/open-device/history-list-data
     * @access public
     *
     * @param Request $request get请求<br><br>
     * <b>get请求字段：</b>
     * @return view
     *
     * varchar   dateTime           使用时间
     * @version 0.7
     * @author tangjun <tangjun@misrobot.com>
     * @date 2015-12-8 11:04
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function getHistoryListData(ResourcesDeviceHistory $ResourcesDeviceHistory){
        $dateTime = Input::get('dateTime');
        $data = [];
        if(!empty($dateTime)){
            $data['dateTime'] = $dateTime;
        }
        $HistoryList = $ResourcesDeviceHistory->getHistoryList($data);

        return response()->json(
            $this->success_rows(1,'获取成功',$HistoryList->total(),20,$HistoryList->currentPage(),array('ClassroomApplyList'=>$HistoryList->toArray()))
        );
    }

    /**
     * 开放实验室历史记录列表
     * @method GET /msc/wechat/open-device/history-detail?id=?
     * @access public
     *
     * @param Request $request get请求<br><br>
     * <b>get请求字段：</b>
     * @return view
     *
     * int   id           计划表id
     * @version 0.7
     * @author tangjun <tangjun@misrobot.com>
     * @date 2015-12-8 11:04
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function getHistoryDetail(ResourcesDeviceHistory $ResourcesDeviceHistory){
        $id = Input::get('id');
        if(!empty($id)){
            $HistoryDetail = $ResourcesDeviceHistory->getHistoryDetail(array('id'=>$id));

            //dd($HistoryDetail);//->toArray()

            return view('msc::wechat.resource.opendevice_history_detail',['HistoryDetail'=>$HistoryDetail]);
        }else{
            return view('msc::wechat.index.index_error',array('error_msg'=>'没有传入历史Id'));
        }
    }
    /**
     * 设备使用信息确认(扫一扫着陆页)(学生)/帮学生开启设备(扫一扫着陆页)(老师)
     * @method GET /msc/wechat/open-device/equipment-confirm
     * @access public
     *
     * @param Request $request get请求<br><br>
     * <b>get请求字段：</b>
     * @return view
     *
     * varchar   code           设备id
     * @version 0.7
     * @author tangjun <tangjun@misrobot.com>
     * @date 2015-12-8 16:20
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function getEquipmentConfirm(ResourcesDevicePlan $ResourcesDevicePlan,ResourcesDevice $ResourcesDevice){
        $code = Input::get('code');
        $user = Auth::user();

        if(!empty($code)){

            $DeviceInfo = $ResourcesDevice->where('code','=',$code)->get()->first();

            if(!empty($DeviceInfo->id)){
                $data['resources_device_id'] = $DeviceInfo->id;
                if(!empty($user->user_type) && $user->user_type == 2){
                    $data['uid'] = $user->id;
                    $DevicePlanInfo = $ResourcesDevicePlan->getDeviceInfo($data);
                }elseif(!empty($user->user_type) && $user->user_type == 1){
                    $DevicePlanInfo = $ResourcesDevicePlan->getDeviceInfo($data);
                }

                if(!empty($DevicePlanInfo)){
                    if(!empty($user->user_type) && $user->user_type == 1){
                        return view('msc::wechat.resource.opendevice_student_start',array('DevicePlanInfo'=>$DevicePlanInfo));
                    }else{
                        return view('msc::wechat.opendevice.begin_confirm',array('DevicePlanInfo'=>$DevicePlanInfo));
                    }

                }else{
                    return view('msc::wechat.index.index_error',array('error_msg'=>'没有相关设备信息'));
                }
            }else{
                return view('msc::wechat.index.index_error',array('error_msg'=>'没有相关设备'));
            }
        }else{
            return view('msc::wechat.index.index_error',array('error_msg'=>'没有传入code'));
        }
    }

    /**
     * 确认使用设备(学生)/老师
     * @method GET /msc/wechat/open-device/equipment-confirm
     * @access public
     *
     * @param Request $request post请求<br><br>
     * <b>get请求字段：</b>
     * @return view
     *
     * int resources_device_id  设备id
     * int opertion_uid 使用者id
     * int resources_lab_id 实验室id
     * int resources_device_plan_id 计划表id
     * @version 0.7
     * @author tangjun <tangjun@misrobot.com>
     * @date 2015-12-8 16:20
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function postEquipmentConfirm(Request $request,ResourcesDeviceHistory $ResourcesDeviceHistory,ResourcesDevicePlan $ResourcesDevicePlan){
        $this->validate($request,[
            'resources_device_id'   => 'required|integer',
            'opertion_uid'    => 'required|integer',
            'resources_lab_id'    => 'required|integer',
            'resources_device_plan_id'       => 'required|integer',
        ]);
        $data = $request->all();
        $data['begin_datetime'] = date('Y-m-d H:i:s',time());
        DB::connection('msc_mis')->beginTransaction();
        $result = $ResourcesDeviceHistory::create($data);
        if($result){
            $rew = $ResourcesDevicePlan->where('id','=',$data['resources_device_plan_id'])->update(['status'=>'1']);
            if($rew){
                DB::connection('msc_mis')->commit();
                return view('msc::wechat.index.index_success',array('error_msg'=>'确认使用成功'));
            }else{
                DB::connection('msc_mis')->rollback();
                return view('msc::wechat.index.index_error',array('error_msg'=>'确认使用失败'));
            }
        }else{
            return view('msc::wechat.index.index_error',array('error_msg'=>'确认使用失败'));
        }
    }

    /**
     * 老师帮忙关闭设备
     * @method GET /msc/wechat/open-device/close-device
     * @access public
     *
     * @param Request $request get请求<br><br>
     * <b>get请求字段：</b>
     * @return view
     *
     * varchar   code           设备code
     * @version 0.7
     * @author tangjun <tangjun@misrobot.com>
     * @date 2015-12-8 16:20
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function getCloseDevice(ResourcesDevicePlan $ResourcesDevicePlan,ResourcesDevice $ResourcesDevice){
        $code = Input::get('code');
        $user = Auth::user();
        if(!empty($code)){
            $DeviceInfo = $ResourcesDevice->where('code','=',$code)->get()->first();
            if(!empty($DeviceInfo->id)){
                $data['resources_device_id'] = $DeviceInfo->id;
                $DevicePlanInfo = $ResourcesDevicePlan->getDeviceInfo($data,1);
                
                if(!empty($DevicePlanInfo['ResourcesDeviceHistory']['begin_datetime'])){
                    $begin_time = strtotime($DevicePlanInfo['ResourcesDeviceHistory']['begin_datetime']);
                    $now_time = time();
                    $second = $now_time - $begin_time;
                    $str = $this->TimeToString($second);
                    $DevicePlanInfo['total_time'] = $str;
                }

                if(!empty($DevicePlanInfo)){
                    return view('msc::wechat.resource.opendevice_student_end',array('DevicePlanInfo'=>$DevicePlanInfo));
                }else{
                    return view('msc::wechat.index.index_error',array('error_msg'=>'没有相关信息'));
                }
            }else{
                return view('msc::wechat.index.index_error',array('error_msg'=>'没有相关设备'));
            }
        }else{
            return view('msc::wechat.index.index_error',array('error_msg'=>'没有传入code'));
        }
    }

    /**
     * 老师帮忙关闭设备(数据操作)
     * @method post /msc/wechat/open-device/close-device
     * @access public
     *
     * @param Request $request post请求<br><br>
     * <b>get请求字段：</b>
     * @return view
     *
     * int   id           计划表id
     * @version 0.7
     * @author tangjun <tangjun@misrobot.com>
     * @date 2015-12-8 16:20
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function postCloseDevice(Request $request,ResourcesDevicePlan $ResourcesDevicePlan,ResourcesDeviceHistory $ResourcesDeviceHistory){
        $this->validate($request,[
            'id'       => 'required|integer',
            'uid'       => 'required|integer',
        ]);
        $data = $request->all();
        $rew = $ResourcesDevicePlan->where('id','=',$data['id'])->where('status','=',1)->get()->first();
        if(!empty($rew)){
            DB::connection('msc_mis')->beginTransaction();
            $result = $ResourcesDeviceHistory->where('resources_device_plan_id','=',$data['id'])->where('opertion_uid','=',$data['uid'])->update(array('end_datetime'=>date('Y-m-d H:i:s',time())));
            if($result){
                $r = $ResourcesDevicePlan->where('id','=',$data['id'])->update(array('status'=>2));
                if($r){
                    DB::connection('msc_mis')->commit();
                    return view('msc::wechat.index.index_success',array('error_msg'=>'确认使用成功'));
                }else{
                    DB::connection('msc_mis')->rollBack();
                    return view('msc::wechat.index.index_error',array('error_msg'=>'关闭设备失败'));
                }
            }else{
                return view('msc::wechat.index.index_error',array('error_msg'=>'关闭设备失败'));
            }
        }else{
            return view('msc::wechat.index.index_error',array('error_msg'=>'没有相关设备信息'));
        }

    }
 /**
     * 自定义函数：TimeToString($second) 输入秒数换算成多少天/多少小时/多少分/多少秒的字符串
     * int   $second           时间戳
     * @version 0.7
     * @author tangjun <tangjun@misrobot.com>
     * @date 2015-12-8 16:20
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function TimeToString($second){
        $second = $second%(3600*24);//除去整天之后剩余的时间
        $hour = floor($second/3600);
        $second = $second%3600;//除去整小时之后剩余的时间
        $minute = floor($second/60);
        $second = $second%60;//除去整分钟之后剩余的时间
        //返回字符串
        if($hour>0){
            return $hour.'小时'.$minute.'分'.$second.'秒';
        }elseif($minute>0){
            return $minute.'分'.$second.'秒';
        }elseif($second>0){
            return $second.'秒';
        }

    }
    /**
     * 开放设备管理
     * @method get /msc/wechat/open-device/open-device-manage
     * @access public
     *
     * @param Request $request post请求<br><br>
     * <b>get请求字段：</b>
     * @return view
     *
     * @version 0.7
     * @author tangjun <tangjun@misrobot.com>
     * @date 2015-12-8 16:20
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function getOpenDeviceManage(){
        $data=[
            'js'=> $this->GenerationJsSdk()
        ];
        return view('msc::wechat.resource.opendevice_manage',$data);

    }

}