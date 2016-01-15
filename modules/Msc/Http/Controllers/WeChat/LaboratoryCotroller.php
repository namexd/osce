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
use Modules\Msc\Entities\LabApply;
use Modules\Msc\Entities\LabPlan;
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
     * @url /msc/wechat/laboratory/laboratory-list
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
        //$user->user_type == 1 代表老师$val
        }elseif($user->user_type == 1){
            return view('msc::wechat.booking.booking_teacher',['FloorData'=>$FloorData]);
        }
    }

    /**
     * @method  GET
     * @url /msc/wechat/laboratory/laboratory-teacher-list
     * @access public
     * @return \Illuminate\View\View
     * @author tangjun <tangjun@misrobot.com>
     * @date    2016年1月15日10:29:38
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function LaboratoryTeacherList(){
        $Floor = new Floor;
        $FloorData = $Floor->GetFloorData();
        $type = Input::get('type');
        if(!empty($type)){
            return view('msc::wechat.booking.booking_teacher',['FloorData'=>$FloorData,'type'=>$type]);
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
        $LaboratoryInfo = $this->Laboratory->GetLaboratoryInfo($id,$DateTime,1);
        $data = [
            'ApplyTime'=>$DateTime,
            'LaboratoryInfo'=>$LaboratoryInfo,
            'LadDeviceList'=>$LadDevice->GetLadDeviceAll($id)
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
        $user = Auth::user();
        $DateTime = Input::get('DateTime');
        $id = Input::get('id');
        $LadDevice = new LadDevice;
        //TODO GetLaboratoryInfo方法会查询出（实验室相关的楼栋信息、实验室相关的日历安排、实验室相关的日历安排、以及不同日历安排的预约情况和计划情况）
        $LaboratoryInfo = $this->Laboratory->GetLaboratoryOpenInfo($id,$DateTime,2);
        if(!empty($LaboratoryInfo['OpenPlan'])){
            foreach($LaboratoryInfo['OpenPlan'] as $key => $val){
                //$LaboratoryInfo['OpenPlan'][$key]['Apply_num'] = count($val['PlanApply']);
                if(count($val['PlanApply'])>0){
                    foreach($val['PlanApply'] as $k => $v){
                        if(!empty($v['LabApply'])){
                            //TODO 自己已经预约
                            if($v['LabApply']['apply_user_id'] == $user->id){
                                $LaboratoryInfo['OpenPlan'][$key]['Apply_status'] = 1;
                            }elseif($v['LabApply']['user_type'] == 2){//TODO 老师预约
                                $LaboratoryInfo['OpenPlan'][$key]['Apply_status'] = 2;
                            }
                        }
                    }
                }
            }
        }
        $data = [
            'ApplyTime'=>$DateTime,
            'LaboratoryInfo'=>$LaboratoryInfo,
            'LadDeviceList'=>$LadDevice->GetLadDeviceAll($id)
        ];
        //dd($data);
        return  view('msc::wechat.booking.booking_student_detail',['data'=>$data]);
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
            'LadDeviceList'=>$LadDevice->GetLadDeviceAll($LabId)
        ];
        //dd($data);
        return  view('msc::wechat.booking.booking_student_form',['data'=>$data]);
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
            'description'=>'required|max:512',
            'date_time' => 'required|date'
        ],[
            'lab_id.required'=>'实验室id必填',
            'lab_id.integer'=>'实验室id必须为数字',
            'open_plan_id.required'=>'日历id必填',
            'description.required'=>'预约日期必填',
            'description.max'=>'预约原因最长不能超过512个字节',
            'date_time.required'=>'预约日期必填',
            'date_time.date'=>'预约日期必须为标准的日期格式'
        ]);
        $req = $Request->all();
        $user = Auth::user();
        $open_plan_id = $req['open_plan_id'];

        $data = [
            'lab_id'=>$req['lab_id'],
            'type'=>2,
            'description'=>$req['description'],
            'apply_user_id'=>$user->id,
            'apply_time'=>$req['date_time'],
            'user_type'=>($user->user_type == 1)?2:1,
        ];

        //TODO 新建数据库对象 准备事物操作
        $MscMis = DB::connection('msc_mis');
        $MscMis->beginTransaction();
        $LabApply = new LabApply;
        $rew = $LabApply->create($data);
        if (!empty($rew->id)) {

            $begin_endtime = '';
            $OpenPlan = new OpenPlan;
            //TODO 根据日历id 数组 查询日历表数据 (为插入计划表做准备)
            $OpenPlanInfo = $OpenPlan->whereIn('id',$open_plan_id)->get();
            if(!empty($OpenPlanInfo->toArray())){
                foreach($OpenPlanInfo as $v){
                    if(empty($begin_endtime)){
                        $begin_endtime .= $v['begintime'].'-'.$v['endtime'];
                    }else{
                        $begin_endtime .= (','.$v['begintime'].'-'.$v['endtime']);
                    }
                }
            }
            //TODO 构建计划表数据
            $LabPlanData = [
                'begin_endtime' => $begin_endtime,
                'user_id' => $user->id,
                'type' => 2,
                'lab_id' => $rew->lab_id,
                'plan_time' => $rew->apply_time,
                'lab_apply_id' => $rew->id
            ];
            $LabPlan = new LabPlan;
            $LabPlanInfo = $LabPlan->create($LabPlanData);

            //TODO 计划插入成功 之后生成预约和日历的中间表数据
            if (!empty($LabPlanInfo->id)) {
                $PlanApplyData = [];
                foreach($open_plan_id as $v){
                    $PlanApplyData [] = [
                        'apply_id'=>$rew->id,
                        'open_plan_id'=>$v,
                        'created_at'=>date('Y-m-d H:i:s',time()),
                        'updated_at'=>date('Y-m-d H:i:s',time())
                    ];
                }
                if($MscMis->table('plan_apply')->insert($PlanApplyData)){

                    if($MscMis->table('open_plan')->whereIn('id',$open_plan_id)->increment('apply_num',1)){
                        $MscMis->commit();
                        return  view('msc::wechat.booking.booking_success');
                    }else{
                        $MscMis->rollBack();
                        dd('添加失败');
                    }
                }else{
                    $MscMis->rollBack();
                    dd('添加失败');
                }
            }else{
                $MscMis->rollBack();
                dd('添加失败');
            }
        }

    }
}