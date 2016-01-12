<?php
/**
 * Created by PhpStorm.
 * @author tangjun <tangjun@misrobot.com>
 * @date 2016-01-04 11:19
 * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
 */

namespace Modules\Msc\Http\Controllers\WeChat;
use Illuminate\Support\Facades\Input;
use Modules\Msc\Entities\Student;
use Modules\Msc\Entities\Teacher;
use Modules\Msc\Http\Controllers\MscWeChatController;
use Illuminate\Http\Request;
use Modules\Msc\Entities\Laboratory;
use Modules\Msc\Entities\OpenPlan;
use Modules\Msc\Entities\LadDevice;
use Modules\Msc\Entities\Floor;
use Illuminate\Support\Facades\Auth;
use Modules\Msc\Entities\OpenLabApply;
use DB;
class LaboratoryCotroller extends MscWeChatController
{

    /**
     * @param Student $student
     * @param Teacher $teacher
     * @author tangjun <tangjun@misrobot.com>
     * @date    2016年1月4日12:05:39
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function  __construct(Student $student, Teacher $teacher)
    {
        parent::__construct($student, $teacher); // TODO: Change the autogenerated stub
        $this->Laboratory = new Laboratory;
    }


    /**
     * 待预约列表
     * @method  GET
     * @url /msc/wechat/Laboratory/laboratory-list
     * @access public
     * @param Request $Request
     * @return \Illuminate\View\View
     * @author tangjun <tangjun@misrobot.com>
     * @date    2016年1月4日12:05:39
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function LaboratoryList(Request $Request){
        $Floor = new Floor;
        $FloorData = $Floor->GetFloorData();
        $user = Auth::user();
        //$user->user_type == 2 代表学生
        if($user->user_type == 2){
            return view('msc::wechat.booking.booking_student',['FloorData'=>$FloorData]);
        //$user->user_type == 1 代表老师
        }elseif($user->user_type == 1){

        }
    }

    /**
     * 获取普通实验室列表
     * @method GET
     * @url /msc/wechat/laboratory/laboratory-list-data
     * @access public
     * @DateTime 时间（筛选预约的时间）
     * @floor_id 地址id
     * @floor_num 几楼
     * @return \Illuminate\Http\JsonResponse
     * @author tangjun <tangjun@misrobot.com>
     * @date    2016年1月4日15:46:29
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function LaboratoryListData(){
        $DateTime = Input::get('DateTime');
        $FloorId = Input::get('floor_id');//TODO 楼栋ID
        $FloorNum = Input::get('floor_num');//TODO 层数
        $OpenPlan = new OpenPlan;
        //TODO 根据日历表获取有 日历安排的教室id
        if(!empty($DateTime)){
            $IdRrr = $OpenPlan->GetOpenPlanId($DateTime);
        }else{
            return response()->json(
                $this->success_rows(1,'没有传入预约日期')
            );
        }
        //TODO 获取普通实验室列表（没有日历安排的）
        $LaboratoryListData = $this->Laboratory->GetLaboratoryListData($IdRrr,['FloorId'=>$FloorId,'FloorNum'=>$FloorNum]);

        return response()->json(
            $this->success_rows(1,'获取成功',$LaboratoryListData->total(),config('msc.page_size',10),$LaboratoryListData->currentPage(),array('ClassroomApplyList'=>$LaboratoryListData->toArray()))
        );
    }
    /**
     * 获取开放实验室列表
     * @method GET
     * @url /msc/wechat/laboratory/open-laboratory-list-data
     * @access public
     * @DateTime 时间（筛选预约的时间）
     * @floor_id 地址id
     * @floor_num 几楼
     * @return \Illuminate\Http\JsonResponse
     * @author tangjun <tangjun@misrobot.com>
     * @date    2016年1月4日15:46:29
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function OpenLaboratoryListData(){
        $DateTime = Input::get('DateTime');
        $FloorId = Input::get('floor_id');//TODO 楼栋ID
        $FloorNum = Input::get('floor_num');//TODO 层数
        $OpenPlan = new OpenPlan;
        //TODO 根据日历表获取有 日历安排的教室id
        if(!empty($DateTime)){
            $IdRrr = $OpenPlan->GetOpenPlanId($DateTime);
        }else{
            return response()->json(
                $this->success_rows(1,'没有传入预约日期')
            );
        }

        //TODO 获取开放实验室列表
        $LaboratoryListData = $this->Laboratory->GetLaboratoryListData($IdRrr,['FloorId'=>$FloorId,'FloorNum'=>$FloorNum],2);

        return response()->json(
            $this->success_rows(1,'获取成功',$LaboratoryListData->total(),config('msc.page_size',10),$LaboratoryListData->currentPage(),array('ClassroomApplyList'=>$LaboratoryListData->toArray()))
        );
    }

    /**
     * 根据id 和 日期 预约普通实验室 填写表单页面
     * @method  GET
     * @url /msc/wechat/laboratory/apply-laboratory
     * @access public
     * @DateTime 时间（筛选预约的时间）
     * @id 实验室id
     * @author tangjun <tangjun@misrobot.com>
     * @date    2016年1月5日11:07:56
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function ApplyLaboratory(){
        $DateTime = Input::get('DateTime');
        $id = Input::get('id');
        $LadDevice = new LadDevice;
        //TODO GetLaboratoryInfo方法会查询出（实验室相关的楼栋信息、实验室相关的日历安排、实验室相关的日历安排、以及不同日历安排的预约情况和计划情况）
        $LaboratoryInfo = $this->Laboratory->GetLaboratoryInfo($id,$DateTime);
        $data = [
            'ApplyTime'=>$DateTime,
            'LaboratoryInfo'=>$LaboratoryInfo,
            'LadDeviceList'=>$LadDevice->GetLadDevice($id)
        ];
        dd($data);
    }
    /**
     * 根据id 和 日期 开放实验室日历列表页面
     * @method  GET
     * @url /msc/wechat/laboratory/apply-open-laboratory
     * @access public
     * @DateTime 时间（筛选预约的时间）
     * @id 实验室id
     * @author tangjun <tangjun@misrobot.com>
     * @date    2016年1月6日09:48:41
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function ApplyOpenLaboratory(){
        $DateTime = Input::get('DateTime');
        $id = Input::get('id');
        $LadDevice = new LadDevice;
        //TODO GetLaboratoryInfo方法会查询出（实验室相关的楼栋信息、实验室相关的日历安排、实验室相关的日历安排、以及不同日历安排的预约情况和计划情况）
        $LaboratoryInfo = $this->Laboratory->GetLaboratoryInfo($id,$DateTime);
        $data = [
            'ApplyTime'=>$DateTime,
            'LaboratoryInfo'=>$LaboratoryInfo,
            'LadDeviceList'=>$LadDevice->GetLadDevice($id)
        ];
        dd($data);
    }

    /**
     * 开放实验室申请表单填写页面
     * @method  POST
     * @url /msc/wechat/laboratory/open-laboratory-form
     * @access public
     * @param Request $Request
     * @return \Illuminate\View\View
     * @author tangjun <tangjun@misrobot.com>
     * @date    2016年1月6日11:06:00
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function OpenLaboratoryForm(Request $Request){
        $this->validate($Request,[
            'lab_id'   => 'required|integer',
            'open_plan_id'   => 'required',
            'date_time' => 'required|date'
        ],[
            'lab_id.required'=>'实验室id必填',
            'lab_id.integer'=>'实验室id必须为数字',
            'open_plan_id.required'=>'日历id必填',
            'date_time.required'=>'预约日期必填',
            'date_time.date'=>'预约日期格式不正确'
        ]);
        $requests = $Request->all();
        $LabId = $requests['lab_id'];
        $OpenPlanIdRrr = $requests['open_plan_id'];
        $DateTime = $requests['date_time'];
        $LadDevice = new LadDevice;
        $LaboratoryOpenPlanData = $this->Laboratory->GetLaboratoryOpenPlan($LabId,$OpenPlanIdRrr);
        $data = [
            'ApplyTime'=>$DateTime,
            'LaboratoryOpenPlanData'=>$LaboratoryOpenPlanData,
            'LadDeviceList'=>$LadDevice->GetLadDevice($LabId)
        ];
        dd($data);
        //return  view();
    }

    /**
     * @method  POST
     * @url /msc/wechat/laboratory/open-laboratory-form-op
     * @access public
     * @param Request $Request
     * @author tangjun <tangjun@misrobot.com>
     * @date    2016年1月7日10:31:29
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function OpenLaboratoryFormOp(Request $Request){
        $this->validate($Request,[
            'lab_id'   => 'required|integer',
            'open_plan_id'   => 'required',
            'description'=>'required|max:512'
        ],[
            'lab_id.required'=>'实验室id必填',
            'lab_id.integer'=>'实验室id必须为数字',
            'open_plan_id.required'=>'日历id必填',
            'description.required'=>'预约日期必填',
            'description.max'=>'预约原因最长不能超过512个字节'
        ]);
        $req = $Request->all();
        $user = Auth::user();
        $insertArr = [];
        $data = [
            'lab_id'=>$req['lab_id'],
            'type'=>($user->user_type == 1)?2:1,
            'description'=>$req['description'],
            'apply_user_id'=>$user->id,
            'created_at'=>date('Y-m-d H:i:s',time()),
            'updated_at'=>date('Y-m-d H:i:s',time()),
        ];
        //TODO 拼凑插入数组
        if(is_array($req['open_plan_id'])){
            foreach($req['open_plan_id'] as $v){
                $data['lab_plan_id'] = $v;
                $insertArr [] = $data;
            }
        }

        $return = DB::connection('msc_mis')->table('open_lab_apply')->insert($insertArr);

        if($return){
            dd('申请成功');
        }else{
            dd('申请失败');
        }

    }
}