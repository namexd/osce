<?php namespace Modules\Msc\Http\Controllers\WeChat;

use Illuminate\Support\Facades\Input;
use Modules\Msc\Http\Controllers\MscWeChatController;
use Illuminate\Http\Request;
use Modules\Msc\Entities\ResourcesClassroom;
use Illuminate\Support\Facades\Auth;
use Modules\Msc\Entities\ResourcesClassroomApply;
use Modules\Msc\Entities\ResourcesClassroomPlan;
use Modules\Msc\Entities\ResourcesClassroomCourses;
use Modules\Msc\Entities\ResourcesClassroomPlanGroup;
use Modules\Msc\Entities\ResourcesClassroomApplyGroup;
use Modules\Msc\Entities\Groups;
use Modules\Msc\Entities\Courses;
use Modules\Msc\Entities\StudentClass;
use Modules\Msc\Entities\Resources;
use Modules\Msc\Entities\Student;
use Modules\Msc\Entities\ResourcesLabCalendar;
use Modules\Msc\Entities\ResourcesOpenLabCalendar;
use Modules\Msc\Entities\ResourcesOpenLabApply;
use Modules\Msc\Entities\ResourcesOpenLabPlan;
use Modules\Msc\Entities\ResourcesOpenLabAppGroup;
use Modules\Msc\Entities\ResourcesDeviceApply;
use App\Entities\User;
use DB;
class OpenLaboratoryController extends MscWeChatController {
	//突发事件预约（老师紧急预约）详情页
	public function getEmergencyEventDetail(ResourcesOpenLabApply $ResourcesOpenLabApply,ResourcesOpenLabAppGroup $ResourcesOpenLabAppGroup,ResourcesClassroom $ResourcesClassroom,ResourcesOpenLabCalendar $ResourcesOpenLabCalendar,ResourcesOpenLabPlan $ResourcesOpenLabPlan,Groups $Groups,StudentClass $StudentClass,User $User,Courses $Courses){
		$aid = Input::get('id');
		$group = array();
		$class = array();
		$type = null;
         $OpenLabApply = $ResourcesOpenLabApply->where('id','=',$aid)->first();
         $OpenLabAppGroup = $ResourcesOpenLabAppGroup->where('resources_openlab_apply_id','=',$OpenLabApply->id)->get();
         foreach ($OpenLabAppGroup as $k => $v) {
         	if($v->student_class_id == 0){
         		$group[] = $v->student_group_id;
         	}else{
         		$class[] = $v->student_class_id;
         	}
         }
         //dd($OpenLabApply->id);
         if($group){
         	$type = "学生组";
         	$groups = $Groups->whereIn('id',$group)->get();
         }else{
         	$type = "班级";
         	$StudentClass = $StudentClass->whereIn('id',$class)->get();
         }

         $studentType = !empty($groups)?$groups:$StudentClass;
         $str = null;
         foreach ($studentType as $key => $value) {
         	$str .= $value->name;
         }

         $Classroom = $ResourcesClassroom->where('id','=',$OpenLabApply->resources_lab_id)->first();
         $OpenLabCalendar = $ResourcesOpenLabCalendar->where('id','=',$OpenLabApply->resources_lab_calendar_id)->first();
         $OpenLabPlan = $ResourcesOpenLabPlan->where('resources_openlab_calendar_id','=',$OpenLabCalendar->id)->first();
         $OpenLabPlan->begintime = date('H:i',strtotime($OpenLabPlan->begintime));
         $OpenLabPlan->endtime = date('H:i',strtotime($OpenLabPlan->endtime));
         //课程内容
         $Courses = $Courses->where('id','=',$OpenLabApply->course_id)->first();
         //dd($Courses);
         $username = $User->where('id','=',$OpenLabApply->apply_uid)->select('name')->first();
         $data = [
        	'OpenLabApply' => $OpenLabApply,
        	'Classroom' => $Classroom,
        	'studentType' => $str,
        	'OpenLabAppGroup' => $OpenLabAppGroup,
        	'OpenLabPlan' => $OpenLabPlan,
        	'type' => $type,
        	'Courses' => $Courses,
        	'username' => $username
        ];
        $user = Auth::user();
        return view('msc::wechat.resource.emergency_detail',$data);
    }
	//获取实验室数据列表
	public function getLaboratoryData(ResourcesOpenLabCalendar $resourcesOpenLabCalendar){
		$dateTime = Input::get('dateTime');
		$data = [];
		if(!empty($dateTime)){
			$data['dateTime'] = $dateTime;
			$data['week'] = date('N',strtotime($dateTime));
		}
		$LaboratoryList = $resourcesOpenLabCalendar->getLaboratoryClassroomList($data);
		$user = Auth::user();

		foreach($LaboratoryList as $k => $v){

			if(empty($v['resourcesClassroom'])){
				unset($LaboratoryList[$k]);
				continue;
			}

			$LaboratoryList[$k]['is_appointment'] = 0;
			$LaboratoryList[$k]['status']= 0;
			$LaboratoryList[$k]['num'] = count($v['ResourcesOpenLabApply']);
			$LaboratoryList[$k]['plan_num'] = count($v['get_plan']);
			if(!empty($v['ResourcesOpenLabApply']) && count($v['ResourcesOpenLabApply'])>0){
				foreach($v['ResourcesOpenLabApply'] as $val){
					if($val['apply_uid'] == $user->id){
						$LaboratoryList[$k]['is_appointment'] = 1;
						break;
					}
				}
			}
			//TODO:罗海华 修改bug 用于 学生申请开放实验室 审核通过后，学生在此提交申请
			//TODO:在开放实验室被预约时段不满员的情况下依然可以申请
			//TODO:2015-12-22 19:54
			if(!empty($v['get_plan']) && count($v['get_plan'])>0){
				if($v->resourcesClassroom->person_total<=$v['get_plan']->first()->resorces_lab_person_total)
				{
					$LaboratoryList[$k]['status']= 1;
				}
			}
		}

		return response()->json(
			$this->success_rows(1,'获取成功',$LaboratoryList->total(),20,$LaboratoryList->currentPage(),array('ClassroomApplyList'=>$LaboratoryList->toArray()))
		);
	}

	//实验室计划列表
	public function getLaboratory(){
		return view('msc::wechat.openlab.openlab_student_search');
	}

	//开放实验室管理
	public function getOpenLaboratoryManage(){
		return view('msc::wechat.resource.openlab_manage');
	}

	/**
	 * 实验室预约页面
	 */
	public function getOrderLab(ResourcesOpenLabCalendar $ResourcesOpenLabCalendar,StudentClass $studentClass,Groups $groups,ResourcesOpenLabPlan $ResourcesOpenLabPlan,ResourcesClassroomCourses $ResourcesClassroomCourses,Courses $Courses){
		$pid = Input::get('id');//clelend.id
		$ClassroomPlan_detai = $ResourcesOpenLabCalendar->order_detail($pid);
		$user = Auth::user();
		$username = $user->name;
		$user_type = $user->user_type;
		$studentClass = $studentClass->get();
		$groups = $groups->where('name','!=','null')->get();
		$ClassroomPlan_detai->day_begintime = date('H:i',strtotime($ClassroomPlan_detai->day_begintime));
		$ClassroomPlan_detai->day_endtime = date('H:i',strtotime($ClassroomPlan_detai->day_endtime));
		/**
		 * 查找课程
		 */
		//$openlab_id = $ResourcesOpenLabPlan->where('resources_openlab_calendar_id','=',$pid)->first();
		$Course = null;
		if($ClassroomPlan_detai->resources_lab_id){
			$resources_lab_courses = $ResourcesClassroomCourses->where('resources_lab_id','=',$ClassroomPlan_detai->resources_lab_id)->get();
			$arr = array();
			foreach ($resources_lab_courses as $key => $value) {
				$arr[] = $value['course_id']; 
			}
			$Course = $Courses->whereIn('id', $arr)->get();
		}

		$data = [
			'ClassroomPlanInfo' => $ClassroomPlan_detai,
			'username' => $username,
			'studentClass' => $studentClass,
			'groups' => $groups,
			'user_type' => $user_type,
			'pid'     => $pid,
			'Courses' => $Course,
			'apply_type' => Input::get('apply_type'),
			'apply_date' => Input::get('apply_date'),
		];
		return view('msc::wechat.openlab.openlab_search_apply',$data);
	}


	//预约实验室用户类型列表
	public function getTypeList(ResourcesClassroom $resourcesClassroom){
			$i= !empty($i)?$i:0;
			if($i > 0){
				return redirect()->intended('/msc/wechat/open-laboratory/order-lab?id='. Input::get('c_id'));
			}else{
				$user = Auth::user();
				$user_type = $user->user_type;
				switch ($user_type) {
					case 1:
						$view = 'openlab_teacher_search';
						break;
					case 2:
						$view = 'openlab_student_search';
						break;
					default:
						return view('msc::wechat.index.index_error',array('error_msg'=>'你是管理员？'));
						break;
				}
				//获取教室资源列表
				$resourcesClassroomList = $resourcesClassroom->getClassroomList();
				$data = [
					'resourcesClassroomList'=>$resourcesClassroomList,
				];
				//dd($data);
				return view('msc::wechat.openlab.'.$view,$data);
			}
	}


	//添加实验室预约信息
	public function postAddLab(Request $request,Student $student,ResourcesOpenLabPlan $ResourcesOpenLabPlan,ResourcesOpenLabApply $ResourcesOpenLabApply){
		$timestamp = explode('~',Input::get('timestamp'));
		$user = Auth::user();
		$url = "/msc/wechat/open-laboratory/order-lab?id=".Input::get('c_id');
		if(Input::get('user_type') == 1){
			//dd(Input::get());
			$this->validate($request, [
				'detail' => 'required',
				//'course_name' => 'required',
			]);

			$data = [
				'resources_lab_calendar_id' => Input::get('p_id'),
				'resources_lab_id' => Input::get('c_id'),
				'apply_date' => Input::get('apply_date'),
				'apply_type' => Input::get('apply_type'),
				'course_id' => Input::get('course_name'),
				'detail' =>  Input::get('detail'),
				'apply_uid' => $user->id,
			];
			//dd($data);
			DB::connection('msc_mis')->beginTransaction();
			$resources=ResourcesOpenLabApply::create($data);
			if(!$resources){
				DB::connection('msc_mis')->rollback();
				return redirect()->intended('/msc/wechat/open-laboratory/order-lab?id='. Input::get('c_id'));
			}
			$arr['resources_openlab_apply_id'] = $resources->id;
			$data = array();
			if(Input::get('class_id')){
				$class_id = explode(',', Input::get('class_id'));
				foreach ($class_id as $value) {
					$arr['student_class_id'] = $value;
					$data[] = $arr;
				}
			}

			if(Input::get('group_id')){
				$group_id = explode(',', Input::get('group_id'));
				foreach ($group_id as $value) {
					$arr['student_group_id'] = $value;
					$data[] = $arr;
				}
			}
			$PlanGroup = DB::connection('msc_mis')->table('resources_openlab_apply_group')->insert($data);

			$this->rollback_func($PlanGroup,$url);
			if(!$PlanGroup){
				DB::connection('msc_mis')->rollback();
				return redirect()->intended('/msc/wechat/open-laboratory/order-lab?id='. Input::get('c_id'));
			}

			DB::connection('msc_mis')->commit();
			return redirect()->intended('/msc/wechat/open-laboratory/type-list');
		}elseif(Input::get('user_type') == 2){
			$data = [
				'resources_lab_id' => Input::get('c_id'),
				// 'end_datetime' => date('Y-m-d',time()).' '.$timestamp[1],
				// 'begin_datetime' => date('Y-m-d',time()).' '.$timestamp[0],
				'apply_date' => Input::get('apply_date'),
				'apply_uid' => $user->id,
				'apply_type' => 0,
				'resources_lab_calendar_id' => Input::get('p_id'),
			];
		//dd($data);
			DB::connection('msc_mis')->beginTransaction();
			$resources=ResourcesOpenLabApply::create($data);
			if($resources){
				DB::connection('msc_mis')->commit();
				return redirect()->intended('/msc/wechat/open-laboratory/type-list');
			}else{
				return redirect()->intended('/msc/wechat/open-laboratory/order-lab?id='. Input::get('c_id'));
			}
		


	}
}
	
	//不超过-回滚
	public function rollback_func($data,$url){
		if(!$data){
			DB::connection('msc_mis')->rollback();
			return redirect()->intended($url);
		}
	}


	

 
    /**
     * 开放设备列表
     */
    public function getOpenLabList(){
    	return view('msc::wechat.resource.opendevice_applylist');
    }

    /**
     * ajax获取开放设备申请列表数据
     */
    public function getAjaxData(ResourcesDeviceApply $ResourcesDeviceApply){
    	$dateTime = Input::get('dateTime');
		//dd($data);
		$DeviceApply = $ResourcesDeviceApply->getAjaxAppData($dateTime);
     	return response()->json(
		 	$this->success_rows(1,'获取成功',$DeviceApply->total(),20,$DeviceApply->currentPage(),array('DeviceApplyList'=>$DeviceApply->toArray()))
		 );
    }

    /**
     * 开放设备申请详情页
     * @method GET /msc/wechat/open-laboratory/open-device-page
     * @access public
     *
     * @param Request $request get请求<br><br>
     * <b>get请求字段：</b>
     * @return view
     *
     * id   
     * @version 0.7
     * @author weihuiguo <weihuiguo@misrobot.com>
     * @date 2015-12-8 11:04
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function getOpenDevicePage(ResourcesDeviceApply $ResourcesDeviceApply){
    	$aid = Input::get('id');
    	if($aid){
	    	$DeviceApply = $ResourcesDeviceApply->getAppData($aid);
	    	$DeviceApply->original_begin_datetime = date('H:i',strtotime($DeviceApply->original_begin_datetime));
	    	$DeviceApply->original_end_datetime = date('H:i',strtotime($DeviceApply->original_end_datetime));
	    	$data = [
	    		'DeviceApply' => $DeviceApply
	    	];
	    	
	    	return view('msc::wechat.resource.opendevice_applylist_detail',$data);
	    }else{
	    	return view('msc::wechat.index.index_error',array('error_msg'=>'操作失败'));
	    }
    }
    /**
     * 开放设备申请不通过页面
     * @method GET /msc/wechat/open-laboratory/open-lab-miss
     * @access public
     *
     * @param Request $request get请求<br><br>
     * <b>get请求字段：</b>
     * @return view
     *
     * id   
     * @version 0.7
     * @author weihuiguo <weihuiguo@misrobot.com>
     * @date 2015-12-8 11:04
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function getOpenLabMiss(){
    	$id = Input::get('id');
    	return view('msc::wechat.resource.opendevice_applylist_refuse',['id'=>$id]);
    }

    /**
     * 开放设备申请不通过数据处理
     * @method POST /msc/wechat/open-laboratory/open-lab-miss-do
     * @access public
     *
     * @param Request $request post请求<br><br>
     * <b>post请求字段：</b>
     * @return view
     *
     * reject id   
     * @version 0.7
     * @author weihuiguo <weihuiguo@misrobot.com>
     * @date 2015-12-8 11:04
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function postOpenLabMissDo(Request $request){
    	$this->validate($request, [
				'reject' => 'required',
			]);
    	$user = Auth::user();
    	$data = [
    		'reject' 		  => Input::get('reject'),
    		'status' 		  => 2,
    		'opeation_uid'    => $user->id,
    	];


    	$resourt = DB::connection('msc_mis')->table('resources_device_apply')->where('id','=',Input::get('id'))->update($data);
    	if($resourt){
    		return redirect()->intended('/msc/wechat/open-laboratory/open-lab-list'); 
    	}else{
    		return view('msc::wechat.index.index_error',array('error_msg'=>'操作失败'));
    	}
    }

    /**
     * 开放设备申请通过处理
     * @method POST 
     * @access public
     *
     * @param Request $request post请求<br><br>
     * <b>post请求字段：</b>
     * @return view
     *
     * @version 0.7
     * @author weihuiguo <weihuiguo@misrobot.com>
     * @date 2015-12-8 11:04
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function getOpenLabOfferDo(Request $Request){
    	$apply = DB::connection('msc_mis')->table('resources_device_apply')->where('id','=',Input::get('id'))->first();
    	//dd($apply);
    	$user = Auth::user();
    	$data = [
    		'status' 		  => 1,
    		'opeation_uid'    => $user->id,
    	];

    	$resourt = DB::connection('msc_mis')->table('resources_device_apply')->where('id','=',Input::get('id'))->update($data);

    	//dd($resourt);
    	if($resourt){
    		return redirect()->intended('/msc/wechat/open-laboratory/open-lab-list'); 
    	}else{
    		return view('msc::wechat.index.index_error',array('error_msg'=>'操作失败'));
    	}
    }
	//突发事件管理
	public function getEmergencyManage(){
		return view('msc::wechat.resource.emergency _search');
	}
	//突发事件管理页面数据
	public function getEmergencyManageData(ResourcesOpenLabApply $ResourcesOpenLabApply){
		$dateTime = Input::get('dateTime');
		$data = [];
		if(!empty($dateTime)){
			$data['dateTime'] = $dateTime;
		}

		$OpenLabApplyList = $ResourcesOpenLabApply->getOpenLabApplyList($data);
		return response()->json(
			$this->success_rows(1,'获取成功',$OpenLabApplyList->total(),20,$OpenLabApplyList->currentPage(),array('ClassroomApplyList'=>$OpenLabApplyList->toArray()))
		);

	}

	/**
	 * 开放实验室申请详情
	 * @api GET /msc/wechat/open-laboratory/open-lab-apply
	 * @access public
	 *
	 * @param Request $request get请求<br><br>
	 * <b>get请求字段：</b>
	 * * string        id        申请ID(必须的)
	 *
	 * @return view {设备名称：$apply->name,申请人：$user->name,申请开始时间：$apply->calendar->begintime ,申请结束时间：$apply->calendar->endtime,申请结束时间：$apply->calendar->endtime,申请理由：$apply->detail}
	 *
	 * @version 1.0
	 * @author Luohaihua <Luohaihua@misrobot.com>
	 * @date 205-12-21 16:27
	 * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
	 *
	 */
	public function getOpenLabApply(Request $request){
		$this   ->  validate($request,[
				'id' => 'required|integer',
		]);
		$id     =   $request    ->get('id');

		$apply  =   ResourcesOpenLabApply::find($id);
		if(is_null($apply))
		{
			return redirect()->back()->withErrors(new \Exception('没有找到该申请'));
		}
		else
		{
			return view('msc::wechat.resource.openlab_apply_detail',['lab'=>$apply->lab,'user'=>$apply->applyUser,'apply'=>$apply]);
		}
	}
}