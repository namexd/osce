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
use Modules\Msc\Entities\Groups;
use Modules\Msc\Entities\StudentClass;
use Modules\Msc\Entities\Resources;
use DB;
class CourseOrderController extends MscWeChatController {

	//查看预约课程列表
	// /msc/wechat/course-order/course-list
	public function getCourseList(ResourcesClassroom $resourcesClassroom){
		//获取教室资源列表
		$resourcesClassroomList = $resourcesClassroom->getClassroomList();
		//dd($resourcesClassroomList);
		$data = [
			'resourcesClassroomList'=>$resourcesClassroomList,
		];
		return view('msc::wechat.courseorder.course_search',$data);
	}

	//获取课程列表数据
	// /msc/wechat/course-order/course-list-data
	public function getCourseListData(ResourcesClassroomCourses $resourcesClassroomCourses){
		$dateTime = Input::get('dateTime');
		$resources_lab_id = Input::get('resources_lab_id');
		
		$resources_lab_plan_builder = DB::connection('msc_mis')->table('resources_lab_plan');
		if(!empty($dateTime)){
			$resources_lab_plan_builder = $resources_lab_plan_builder->where('currentdate','=',$dateTime);
		}
		if(!empty($resources_lab_id)){
			$ClassroomCoursesList = $resourcesClassroomCourses->where('resources_lab_id','=',$resources_lab_id)->get();
			$ClassroomCoursesIdArr = [];
			foreach($ClassroomCoursesList as $val){
				$ClassroomCoursesIdArr[] = $val['id'];
			}
			$resources_lab_plan_builder = $resources_lab_plan_builder->whereIn('resources_lab_plan.resources_lab_course_id',$ClassroomCoursesIdArr);
		}

		$resources_lab_plan_builder = $resources_lab_plan_builder
			->join('resources_lab_courses', 'resources_lab_plan.resources_lab_course_id', '=', 'resources_lab_courses.id')
			->join('resources_lab', 'resources_lab_courses.resources_lab_id', '=', 'resources_lab.id')
			->select('resources_lab_plan.id','resources_lab_plan.currentdate','resources_lab_plan.begintime', 'resources_lab_plan.endtime','resources_lab.status','resources_lab.name');



		$ClassroomPlanList = $resources_lab_plan_builder->orderBy('resources_lab_plan.id')->paginate(7);

		return response()->json(
			$this->success_rows(1,'获取成功',$ClassroomPlanList->total(),20,$ClassroomPlanList->currentPage(),array('ClassroomApplyList'=>$ClassroomPlanList->toArray()))
		);

	}

	//紧急课程预约
	// /msc/wechat/course-order/course-apply
	public function getCourseApply(ResourcesClassroomPlan $resourcesClassroomPlan,StudentClass $studentClass,Groups $groups){
		$id = Input::get('id');
		//$user = Auth::user();
		//dd($user);
		$studentClass = $studentClass->get();
		 $groups = $groups->get();

		$resources_lab_plan_builder = DB::connection('msc_mis')->table('resources_lab_plan as r_l_p');
		//resources_lab_plan.resources_lab_course_id
		 $ClassroomPlanInfo = $resources_lab_plan_builder->join('resources_lab_courses as r_l_c', 'r_l_p.resources_lab_course_id', '=', 'r_l_c.id')
		 ->join('resources_lab as r_l', 'r_l_c.resources_lab_id', '=', 'r_l.id')
		 ->where('r_l_p.id','=',$id)
		 ->select('r_l_p.currentdate','r_l_p.begintime', 'r_l_p.endtime', 'r_l.name','r_l.id')
		 ->first();
		//dd($ClassroomPlanInfo);
		$data = [
			'studentClass' => $studentClass,
			'groups' => $groups,
			'ClassroomPlanInfo' => $ClassroomPlanInfo,
		];

		return view('msc::wechat.courseorder.course_search_apply',$data);
	}

	//确实执行预约的课程
	// /msc/wechat/course-order/course-confirm
	public function getCourseConfirm(ResourcesClassroomPlan $resourcesClassroomPlan,StudentClass $studentClass,Groups $groups){
		$id = Input::get('id');
		if($id){
		$resources_lab_plan_builder = DB::connection('msc_mis')->table('resources_lab_plan as r_c_p');
		 $ClassroomPlanInfo = $resources_lab_plan_builder->join('resources_lab_courses as r_c_c', 'r_c_p.resources_lab_course_id', '=', 'r_c_c.id')
		 ->join('resources_lab as r_c', 'r_c_c.resources_lab_id', '=', 'r_c.id')
		 ->join('courses as c','r_c_p.course_id','=','c.id')
		 ->where('r_c_p.id','=',$id)
		 ->select('r_c_p.currentdate','r_c_p.begintime', 'r_c_p.endtime', 'r_c.name','r_c_p.id','c.name as cname')
		 ->first();
		$studentClass = $studentClass->get();
	 	//$groups = $groups->get();
		if(!empty($ClassroomPlanInfo)){
			$ClassroomPlanInfo->begintime = date('H:i',strtotime($ClassroomPlanInfo->begintime));
	 		$ClassroomPlanInfo->endtime = date('H:i',strtotime($ClassroomPlanInfo->endtime));
		}
	 	
	 	
		$data = [
			'ClassroomPlanInfo'=>$ClassroomPlanInfo,
			'studentClass' => $studentClass,
			'groups' => $groups,
		];
			return view('msc::wechat.courseorder.course_confirm',$data);
		}else{
			abort(404);
		}

	}

	//保存课程预约信息（）
	///msc/wechat/course-order/postAddClassRoomApply
	public function postAddClassRoomApply(Request $request) {   
		$this->validate($request, [      
			'course_name' => 'required',      
			'detail' => 'required',       
			]); 
		$timestamp = explode('~',Input::get('timestamp'));   
		$user = Auth::user(); 
		$data = [
			'resources_lab_id' => Input::get('c_id'),
			'end_datetime' => $timestamp[1],
			'begin_datetime' => $timestamp[0],
			'detail' =>  Input::get('detail'),
			'apply_uid' => $user->id,
		];
		DB::connection('msc_mis')->beginTransaction();
		$resources=ResourcesClassroomApply::create($data);
		//dd($resources->id);
		if(!$resources){
			DB::connection('msc_mis')->rollback();
			return redirect()->intended('/msc/wechat/course-order/course-apply?id='. Input::get('c_id'));
		}
		$arr['resources_lab_apply_id'] = $resources->id;
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
		$PlanGroup = DB::connection('msc_mis')->table('resources_lab_apply_group')->insert($data);
		
		if(!$PlanGroup){
			DB::connection('msc_mis')->rollback();
			return redirect()->intended('/msc/wechat/course-order/course-apply?id='. Input::get('c_id'));
		}

		DB::connection('msc_mis')->commit();
		return redirect()->intended('/msc/wechat/course-order/course-list?type=success');
	} 


	//确定使用
	//courseApply
	public function postAddCourseToAplan(ResourcesClassroomPlan $plan,ResourcesClassroomCourses $rcc){
		DB::connection('msc_mis')->beginTransaction();
		$CourseToAplan = DB::connection('msc_mis')->table('resources_lab_plan')->where('id', Input::get('rcp'))->update(['status' => 0]);
		if(!$CourseToAplan){
			DB::connection('msc_mis')->rollback();
			return redirect()->intended('/msc/wechat/course-order/course-confirm?id='. Input::get('rcp'));
		}

		$r_c_c_id = $plan->where('id','=', Input::get('rcp'))->first();
		$r_c_id = $rcc->where('id','=',$r_c_c_id->resources_lab_course_id)->first();
		$room_update = DB::connection('msc_mis')->table('resources_lab')->where('id', $r_c_id->resources_lab_id)->update(['status' => 2]);
		if(!$room_update){
			DB::connection('msc_mis')->rollback();
			return redirect()->intended('/msc/wechat/course-order/course-confirm?id='. Input::get('rcp'));
		}
		$arr['resources_lab_plan_id'] = Input::get('rcp');
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
		$PlanGroup = DB::connection('msc_mis')->table('resources_lab_plan_group')->insert($data);
		if(!$PlanGroup){
			DB::connection('msc_mis')->rollback();
			return redirect()->intended('/msc/wechat/course-order/course-confirm?id='. Input::get('rcp'));
		}

		DB::connection('msc_mis')->commit();
		return redirect()->intended('/msc/wechat/course-order/course-list?type=success');
	}
}