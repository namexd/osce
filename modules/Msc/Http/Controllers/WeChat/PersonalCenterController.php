<?php namespace Modules\Msc\Http\Controllers\WeChat;

use Illuminate\Support\Facades\Input;
use Modules\Msc\Http\Controllers\MscWeChatController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Modules\Msc\Entities\PlanApply;
use Modules\Msc\Entities\Student;
use Modules\Msc\Entities\Teacher;
use App\Entities\User;
use Modules\Msc\Entities\LabApply;
use Modules\Msc\Entities\OpenPlan;
use DB;
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
	/**
	 * 我的预约（实验室）
	 * @method	GET
	 * @url /msc/wechat/personal-center/my-laboratory-apply
	 * @access public
	 * @author tangjun <tangjun@sulida.com>
	 * @date	2016年1月11日13:58:12
	 * @copyright  2013-2017 sulida.com  Inc. All Rights Reserved
     */
	public function MyLaboratoryApply(){
		$LabApply = new LabApply;
		$user = Auth::user();
		$MyApplyList = [];
		$MyPlanList = [];
		if($user->user_type == 1){//TODO 老师
			$MyApplyList = $LabApply->MyApplyList(1,$user->id,2);
			$MyPlanList = $LabApply->MyApplyList(2,$user->id,2);
		}elseif($user->user_type == 2){//TODO 学生
			$MyApplyList = $LabApply->MyApplyList(1,$user->id,1);
			$MyPlanList = $LabApply->MyApplyList(2,$user->id,1);

		}
		$data = [
			'MyApplyList'=>$MyApplyList,
			'MyPlanList'=>$MyPlanList
		];
		return view('msc::wechat.booking.mybooking',$data);

	}
	/**
	 * 我已经完成的实验室预约信息；
	 * @method	GET
	 * @url /msc/wechat/personal-center/history-laboratory-apply-list
	 * @access public
	 * @author tangjun <tangjun@sulida.com>
	 * @date	2016年1月11日13:58:12
	 * @copyright  2013-2017 sulida.com  Inc. All Rights Reserved
	 */
	public function HistoryLaboratoryApplyList(){
		$LabApply = new LabApply;
		$user = Auth::user();
		$HistoryLaboratoryApplyList = $LabApply->HistoryLaboratoryApplyList($user->id);
		return response()->json(
			$this->success_rows(1,'获取成功',$HistoryLaboratoryApplyList->total(),config('msc.page_size',10),$HistoryLaboratoryApplyList->currentPage(),array('HistoryLaboratoryApplyList'=>$HistoryLaboratoryApplyList->toArray()))
		);
	}


	/**
	 * 预约详情
	 * @method	GET
	 * @url /msc/wechat/personal-center/get-apply-details
	 * @access public
	 * @author tangjun <tangjun@sulida.com>
	 * @date	2016年1月18日11:41:25
	 * @copyright  2013-2017 sulida.com  Inc. All Rights Reserved
     */
	public function GetApplyDetails(){
		$apply_id = Input::get('apply_id');
		$LabApply = new LabApply;
		$ApplyDetails = $LabApply->GetApplyDetails($apply_id);
		return response()->json(
			$this->success_rows(1,'获取成功',1,config('msc.page_size',10),1,array('HistoryLaboratoryApplyList'=>$ApplyDetails->toArray()))
		);
	}

	/**
	 * @method	GET
	 * @url /msc/wechat/personal-center/cancel-apply
	 * @access public
	 * @author tangjun <tangjun@sulida.com>
	 * @date	2016年1月18日16:27:08
	 * @copyright  2013-2017 sulida.com  Inc. All Rights Reserved
     */
	public function CancelApply(){
		$LabApply = new LabApply;
		$user = Auth::user();
		$apply_id = Input::get('apply_id');
		$MscMis = DB::connection('msc_mis');
		$MscMis->beginTransaction();
		$LabApplyBuilder = $LabApply->where('apply_user_id','=',$user->id)->where('id','=',$apply_id);
		$updateRew = $LabApplyBuilder->update(['status'=>4]);
		if($updateRew){
			$LabPlanInfo = $MscMis->table('lab_plan')->where('lab_apply_id','=',$apply_id)->first();
			$delRew = $MscMis->table('lab_plan')->where('lab_apply_id','=',$apply_id)->delete();
			if((!empty($LabPlanInfo->id) && $delRew) || empty($LabPlanInfo->id)){
				$LabApplyInfo = $LabApplyBuilder->first();
				switch($LabApplyInfo['type']){
					case 1://TODO 取消普通实验室的预约
						$MscMis->commit();
						return	$this->success_rows(1,'取消成功');
						break;
					case 2://TODO 取消开放实验室的预约
						$PlanApply = new PlanApply;
						$PlanIdArr = $PlanApply->ApplyIdGetOpenPlanIdArr($apply_id);
						$OpenPlan = new OpenPlan;
						//TODO 找到对应的开放日历减去预约次数
						if(!empty($PlanIdArr)){
							$decrementRew = false;
							if($LabApplyInfo['user_type'] == 2){
								$decrementRew = $OpenPlan->whereIn('id',$PlanIdArr)->update(['is_teacher_apply'=>0]);
							}else{
								$decrementRew = $OpenPlan->whereIn('id',$PlanIdArr)->decrement('apply_num',1);
							}
							if($decrementRew){
								$MscMis->commit();
								return	$this->success_rows(1,'取消成功');
							}else{
								$MscMis->rollBack();
								return	$this->success_rows(2,'取消失败');
							}
						}else{//TODO 如果没有找到开放日历（默认开放日历被取消）
							$MscMis->commit();
							return	$this->success_rows(1,'取消成功');
						}
						break;
					default:
						return	$this->success_rows(2,'取消失败');
				}
			}else{
				$MscMis->rollBack();
				return	$this->success_rows(2,'取消失败');
			}

		}else{
			$MscMis->rollBack();
			return	$this->success_rows(2,'取消失败');
		}
	}






}