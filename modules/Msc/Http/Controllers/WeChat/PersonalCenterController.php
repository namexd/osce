<?php namespace Modules\Msc\Http\Controllers\WeChat;

use Illuminate\Support\Facades\Input;
use Modules\Msc\Entities\ResourcesDeviceApply;
use Modules\Msc\Entities\ResourcesDevicePlan;
use Modules\Msc\Entities\ResourcesOpenLabPlan;
use Modules\Msc\Http\Controllers\MscWeChatController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Modules\Msc\Entities\ResourcesBorrowing;
use Modules\Msc\Entities\Student;
use Modules\Msc\Entities\Teacher;
use App\Entities\User;

class PersonalCenterController extends MscWeChatController {
	public function getTest(){
		return view('msc::wechat.myreservation.current_reser');
	}

	//个人中心
	public function getIndex(){
		$data = [
			'user'=>Auth::user()
		];
		return view('msc::wechat.index.index_personalcenter',$data);
	}

	//修改绑定手机号
	public function getSavePhone(){
		$user = Auth::user();
		$data = [
			'user'=>$user
		];
		return view('msc::wechat.personalcenter.phone_change',$data);
	}
	//修改绑定手机号处理
	public function postSavePhone(Request $request,User $user){

		$this->validate($request,[
			'mobile'=>'required',
			'id'=>'required',
		]);
		$requests = $request->all();
		$rew = $user->where('id','=',$requests['id'])->update(array('mobile'=>$requests['mobile']));
		if($rew){
			return redirect()->intended('/msc/wechat/personal-center/info');
		}else{
			dd('修改失败');
		}


	}

	//查看个人信息
	public function getInfo(Student $student,Teacher $teacher){
		$user = Auth::user();
		$data = [
			'user'=>$user
		];
		$studentInfo = $student->where('id','=',$user->id)->with('className','professionalName')->get()->first();
		$teacherInfo = $teacher->where('id','=',$user->id)->with('dept')->get()->first();
		if(!empty($studentInfo)){
			$data['studentInfo'] = $studentInfo;
		}elseif(!empty($teacherInfo)){
			$data['teacherInfo'] = $teacherInfo;
		}

		return view('msc::wechat.personalcenter.personalinfo',$data);
	}

	//信息管理
	public function getInfoManage(){
		return view('msc::wechat.index.index_infomanage');
	}

	//我的的外借
	public function getPersonalMyBorrow(ResourcesBorrowing $resourcesBorrowing){
		$user = Auth::user();
		$lender = $user->id;
		$nowBorrowingList = $resourcesBorrowing->where('lender','=',$lender)->where('status','=',0)->with('user','resourcesTool','resourcesToolItem')->get();
		$applyBorrowingList = $resourcesBorrowing->where('lender','=',$lender)->where('status','=',1)->with('user','resourcesTool','resourcesToolItem')->get();
		foreach($nowBorrowingList as $k => $v){
			$nowBorrowingList[$k]['begindate'] = date('m.d',strtotime($nowBorrowingList[$k]['begindate']));
			$nowBorrowingList[$k]['enddate'] = date('m.d',strtotime($nowBorrowingList[$k]['enddate']));
			$nowBorrowingList[$k]['real_begindate'] = date('m.d',strtotime($nowBorrowingList[$k]['real_begindate']));
			$nowBorrowingList[$k]['real_enddate'] = date('m.d',strtotime($nowBorrowingList[$k]['real_enddate']));
		}
		foreach($applyBorrowingList as $k => $v){
			$applyBorrowingList[$k]['begindate'] = date('m.d',strtotime($applyBorrowingList[$k]['begindate']));
			$applyBorrowingList[$k]['enddate'] = date('m.d',strtotime($applyBorrowingList[$k]['enddate']));
			$applyBorrowingList[$k]['real_begindate'] = date('m.d',strtotime($applyBorrowingList[$k]['real_begindate']));
			$applyBorrowingList[$k]['real_enddate'] = date('m.d',strtotime($applyBorrowingList[$k]['real_enddate']));
		}
		$data = [
			'nowBorrowingList'=>$nowBorrowingList,
			'applyBorrowingList'=>$applyBorrowingList
		];
		return view('msc::wechat.personalcenter.personalinfo_myborrow',$data);
	}

	//获取外借历史数据
	public function getBorrowHistoryData(ResourcesBorrowing $ResourcesBorrowing){
		$user = Auth::user();
		$lender = empty($user)?5:$user->id;
		$BorrowingBuilder = $ResourcesBorrowing->where('real_enddate','<>',' ')->where('real_enddate','<',date('Y-m.d H:i:s'))->where('lender','=',$lender);
		$historyList = $BorrowingBuilder->with('user','resourcesTool','resourcesToolItem')->orderBy('id')->paginate(7);

		foreach($historyList as $k => $v){
			$historyList[$k]['begindate'] = date('m.d',strtotime($historyList[$k]['begindate']));
			$historyList[$k]['enddate'] = date('m.d',strtotime($historyList[$k]['enddate']));
			$historyList[$k]['real_begindate'] = date('m.d',strtotime($historyList[$k]['real_begindate']));
			$historyList[$k]['real_enddate'] = date('m.d',strtotime($historyList[$k]['real_enddate']));
		}

		return response()->json(
			$this->success_rows(1,'获取成功',$historyList->total(),20,$historyList->currentPage(),array('historyList'=>$historyList->toArray()))
		);
	}

	//续借
	public function getRenew(ResourcesBorrowing $resourcesBorrowing){
		$borrowId = Input::get('id');
		if(!empty($borrowId)){
			$BorrowingInfo = $resourcesBorrowing->where('id','=',$borrowId)->with('user','resourcesTool','resourcesToolItem')->get()->first();
			if(!empty($BorrowingInfo)){
				$BorrowingInfo['begindate'] = date('m.d',strtotime($BorrowingInfo['begindate']));
				$BorrowingInfo['enddate'] = date('m.d',strtotime($BorrowingInfo['enddate']));
				$BorrowingInfo['real_begindate'] = date('m.d',strtotime($BorrowingInfo['real_begindate']));
				$BorrowingInfo['real_enddate'] = date('m.d',strtotime($BorrowingInfo['real_enddate']));
			}
			$data = [
				'BorrowingInfo'=>$BorrowingInfo
			];
			return view('msc::wechat.personalcenter.personalinfo_myborrow_renew',$data);
		}else{
			return view('msc::wechat.index.index_error',array('error_msg'=>'没有该设备信息'));
		}

	}
	//取消外借申请
	public function postCancelBorrowing(Request $request,ResourcesBorrowing $resourcesBorrowing){
		$requests = $request->all();
		$user = Auth::user();
		$lender = empty($user)?5:$user->id;
		if(!empty($requests['id'])){
			$rew = $resourcesBorrowing->where('id','=',$requests['id'])->where('lender','=',$lender)->update(array('status'=>-2));
			if($rew){
				$openid = \Illuminate\Support\Facades\Session::get('openid','');
				$this->sendMsg('取消预订成功',$openid);

				return response()->json(
					$this->success_rows(1,'取消成功')
				);

			}else{
				return response()->json(
					$this->success_rows(2,'用户信息不匹配')
				);
			}
		}else{
			return response()->json(
				$this->success_rows(3,'没有该条数据')
			);

		}


	}

	/**
	 * 我的开放设备预约   (WX-Stu-001-我的预约_当前预约信息)
	 * @api GET /msc/wechat/personal-center/my-apply
	 * @access public
	 *
	 * @param Request $request get请求<br><br>
	 *
	 * @return view
	 *
	 * @version 0.7
	 * @author Luohaihua <Luohaihua@misrobot.com>
	 * @date 2015-12-07
	 * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
	 *
	 */
	public function getMyApply(Request $request){
		$user 					=	Auth::user();
		$resourcesDeviceApply	=	new ResourcesDeviceApply();
		$list				=	$resourcesDeviceApply	->	getMyApply($user->id);
		// 无翻页
		return view('msc::wechat.personalcenter.myreservation',['list'=>$list]);
	}

	/**
	 *	取消现有申请	(WX-Stu-002-我的预约_当前预约信息(取消预约))
	 * @api GET /msc/wechat/personal-center/cancel-open-device-apply
	 * @access public
	 *
	 * @param Request $request get请求<br><br>
	 * <b>get请求字段：</b>
	 * * int        id        申请ID(必须的)
	 *
	 * @return redirect
	 *
	 * @version 1.0
	 * @author Luohaihua <Luohaihua@misrobot.com>
	 * @date ${DATE} ${TIME}
	 * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
	 *
	 */
	public function getCancelOpenDeviceApply(Request $request){
		$id	=	$request		->	get('id');
		$resourcesDeviceApply	=	new	ResourcesDeviceApply();
		try
		{
			$resourcesDeviceApply	->	cancelOpenDeviceApply($id);
			return redirect()->route('msc.personalCenter.myApply');
		}
		catch(\Exception $ex)
		{
			return redirect()->route('msc.personalCenter.myApply')->withErrors($ex);
		}
	}

	/**
	 *	我的开放设备使用历史  着陆页
	 * @api GET /msc/wechat/personal-center/open-device-histroy
	 * @access public
	 *
	 *
	 * @return view
	 *
	 * @version 1.0
	 * @author Luohaihua <Luohaihua@misrobot.com>
	 * @date ${DATE} ${TIME}
	 * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
	 *
	 */
	public function getOpenDeviceHistroy(){
//		return view('');
	}

	/**
	 *	我的开放设备使用历史 ajax 数据获取
	 * @api GET /msc/wechat/personal-center/user-open-device-histroy-data
	 * @access public
	 *
	 * @param Request $request get请求<br><br>
	 * <b>get请求字段：</b>
	 * * int        page        翻页页码(必须的)
	 *
	 * @return json
	 *
	 * @version 1.0
	 * @author Luohaihua <Luohaihua@misrobot.com>
	 * @date 2015-12-08 11:12
	 * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
	 *
	 */
	public function getUserOpenDeviceHistroyData(){
		$user 					=	Auth::user();
		$resourcesDevicePlan	=	new ResourcesDevicePlan();
		$pagination				=	$resourcesDevicePlan	->	getUserOpenDeviceHistroy($user->id);
		$list					=	[];

		foreach($pagination as $item)
		{
			$list[]	=	[
				'code'			=>	$item	->	device	->	code,
				'name'			=>	$item	->	device	->	name,
				'id'			=>	$item	->	id,
				'time_start'	=>	$item	->	currentdate,
				'time_end'		=>	date('H:i',strtotime($item	->	begintime)).'-'.date('H:i',strtotime($item	->	endtime)),
			];
		}

		return response()->json(
			$this->success_rows(1,'获取成功',$pagination->total(),config('msc.page_size'),$pagination->currentPage(),$list)
		);
	}

}