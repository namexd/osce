<?php

namespace Modules\Msc\Http\Controllers\Admin;

use Illuminate\Support\Facades\Input;
use Modules\Msc\Entities\Student;
use Modules\Msc\Entities\Teacher;
use App\Entities\User;
use Illuminate\Http\Request;
use DB;

class VerifyController extends BaseController {

	protected $studentRepo,$teacherRepo;


	public function __construct(Student $student,Teacher $teacher){
		$this->studentRepo=$student;
		$this->teacherRepo=$teacher;
	}

	public function getStudent($status=0){

		if($status==0){
			$data=$this->studentRepo->where('validated','=','0')->paginate(config('msc.page_size',10));
		}
		elseif($status==1){
			$data=$this->studentRepo->where('validated','=','1')->paginate(config('msc.page_size',10));
		}
		else{
			$data=$this->studentRepo->paginate(config('msc.page_size',10));
		}

		return view('msc::admin.verify.student',[
			'list'=>$data
		]);
	}

	public function getTeacher($status=0){

		if($status==0){
			$data=$this->teacherRepo->where('validated','=','0')->paginate(config('msc.page_size',10));
		}
		elseif($status==1){
			$data=$this->teacherRepo->where('validated','=','1')->paginate(config('msc.page_size',10));
		}
		else{
			$data=$this->teacherRepo->paginate(config('msc.page_size',10));
		}

		return view('msc::admin.verify.teacher',[
				'list'=>$data
		]);
	}


	//批量删除用户
	public function postDelMany(Request $request){
		$ids=e($request->get('ids'));
		$idsArray=explode(',',$ids);
		$userModel=new User();
		$studentModel=new Student();
		$teacherModel=new Teacher();

		DB::beginTransaction();
		try{
			if(empty($idsArray))
			{
				throw new\Exception('请选择被删除的用户');
			}
			$result=$userModel->whereIn('id',$idsArray)->delete();
			if(!$result)
			{
				throw new\Exception('删除用户失败');
			}
			$student = $studentModel->whereIn('id',$idsArray)->get();
			if(count($student)>0){
				$result=$studentModel->whereIn('id',$idsArray)->delete();
				if(!$result)
				{
					throw new\Exception('删除学生失败');
				}
			}
			$teacher = $teacherModel->whereIn('id',$idsArray)->get();
			if(count($teacher)>0) {
				$result = $teacherModel->whereIn('id', $idsArray)->delete();
				if (!$result) {
					throw new\Exception('删除教师失败');
				}
			}
			DB::commit();
			return redirect()->intended('/msc/admin/examine/examine-list');
		}
		catch (\Exception $ex)
		{
			DB::rollback();
			return response()->json(
				$this->fail($ex)
			);
		}
	}


	/**
	 *
	 * @api GET /api/1.0/private/admin/user/change-user-status
	 * @access public
	 *
	 * @param Request $request get请求<br><br>
	 * <b>get请求字段：</b>
	 * * string        id        要修改的用户ID(必须的)
	 * * string        status    要修改的状态(必须的)  0为 未审核   1为通过  2为未通过
	 *
	 * @return json
	 *
	 * @version 1.0
	 * @author Zouyuchao <Zouyuchao@sulida.com>
	 * @date ${DATE} ${TIME}
	 * @copyright  2013-2017 sulida.com  Inc. All Rights Reserved
	 *
	 */
/*	public function postChangeUserStatus(Request $request){
		$id=(int)$request->get('id');
		$status=(int)$request->get('status');
		$userModel=new User();
		$data=$userModel->getUserProfileByIds($id);
		$userProfile=$data[$id];
		if(!is_null($userProfile))
		{
			$userProfile->validated=$status;
			if($userProfile->save())
			{
				return redirect()->intended('/msc/admin/examine/examine-list');

			}
			else
			{
				dd('修改失败');
			}
		}
		else
		{
			dd('没有找到相关用户');
		}

	}*/

/*http://www.mis.hx/msc/admin/verify/change-users-status
http://www.mis.hx/msc/admin/verify/student*/
	/**
	 * 批量修改用户状态
	 * @api GET /api/1.0/private/admin/user/change-users-status
	 * @access public
	 *
	 * @param Request $request get请求<br><br>
	 * <b>get请求字段：</b>
	 * * string        ids        用户ID 序列(必须的) e.g:1,22,23,31
	 * * string        status     要变更的状态(必须的)
	 *
	 * @return json data":{"total":4,"pagesize":20,"page":1,"rows":[{uid:用户ID，result:执行结果},{uid:用户ID，result:执行结果}]}
	 *
	 *
	 * @version 1.0
	 * @author Zouyuchao <Zouyuchao@sulida.com>
	 * @date 2015-11-11 18:01
	 * @copyright  2013-2017 sulida.com  Inc. All Rights Reserved
	 *
	 */
	public function postChangeUsersStatus(Request $request){
		if(Input::get('id')){
			if(Input::get('type') == 'student'){
				$update = DB::connection('msc_mis')->table('student')->where('id','=',Input::get('id'))->update(['validated' =>Input::get('status')]);
				if($update){
					return redirect()->intended('/msc/admin/verify/student');
				}else{
					return view('msc::wechat.index.index_error',array('error_msg'=>'系统异常'));
				}
			}else{
				$update = DB::connection('msc_mis')->table('teacher')->where('id','=',Input::get('id'))->update(['validated' =>Input::get('status')]);
				if($update){
					return redirect()->intended('/msc/admin/verify/teacher');
				}else{
					return view('msc::wechat.index.index_error',array('error_msg'=>'系统异常'));
				}
			}

		}else{
			$ids=e($request->get('ids'));
			$status=e($request->get('status'));
			$idsArray=explode(',',$ids);
			$idData=[];

			foreach($idsArray as $id)
			{
				$idData[]=intval($id);
			}
			if(Input::get('type') == 'student'){
				$update = DB::connection('msc_mis')->table('student')->whereIn('id',$idData)->update(['validated' =>Input::get('status')]);
				if($update){
					return redirect()->intended('/msc/admin/verify/student');
				}else{
					return view('msc::wechat.index.index_error',array('error_msg'=>'系统异常'));
				}
			}else {

				$update = DB::connection('msc_mis')->table('teacher')->whereIn('id',$idData)->update(['validated' => Input::get('status')]);
				//dd(Input::get());
				if($update){
					return redirect()->intended('/msc/admin/verify/teacher');
				}else{
					return view('msc::wechat.index.index_error',array('error_msg'=>'系统异常'));
				}
			}
		}


	}


}