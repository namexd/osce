<?php namespace Modules\Osce\Http\Controllers;

use App\Entities\SysMenus;
use Pingpong\Modules\Routing\Controller;
use App\Entities\SysUserRole;

class OsceController extends Controller {


	public function index()
	{
		try{
			$SysMenus	=	new SysMenus();

			$user	=	\Auth::user();
			if(!$user){
				throw new \Exception('没有找到用户，请登录');
			}
			//sys_user_role
//			$connection	=	\DB::connection('sys_mis');

//			$userRoles	=	$connection	->	table('sys_user_role')	->	where('user_id','=',$user->id)->get();
			//TODO：Zhoufuxiang 2016-04-22
			$userRoles = SysUserRole::where('user_id','=',$user->id)->orderBy('role_id')->get();

			if($userRoles->isEmpty())
			{
				throw new \Exception('非法用户，请按照要求注册');
			}
			$superRole = config('osce.superRoleId');		//获取超级管理员角色值
			$adminRole = config('osce.adminRoleId');		//获取系统管理员角色值
			//获取用户最高权限角色
			$role_id   = $userRoles->first();				//默认去第一个
			foreach ($userRoles as $userRole)
			{
				if ($userRole->role_id == $superRole){
					$role_id = $superRole;					//获取超级管理员角色ID
					break;
				}
				if ($userRole->role_id == $adminRole){
					$role_id = $adminRole;					//获取系统管理员角色ID
					continue;
				}
			}

			$MenusList = $SysMenus	->getRoleMenus($role_id);

			$MenusList = $this		->node_merge($MenusList);

			$MenusList =	collect($MenusList);
			return view('osce::admin.layouts.admin',['list'=>$MenusList, 'role_id'=>$role_id]);
		}
		catch(\Exception $ex)
		{
			if($ex->getCode()==0){
				return redirect()->route('osce.admin.getIndex');
			}
			return redirect()->route('osce.admin.getIndex')->withErrors($ex->getMessage());
		}

	}
	//递归通过pid 将其压入到一个多维数组!
	/*
     * $node 存放所有节点的节点数组
     * $access 判断有误权限
     * $pid 父id
     * return 多维数组;
     * */
	protected  function node_merge($node,$pid=0){
		$arr = array();
		foreach($node as $v){
			if(empty($v))
			{
				continue;
			}
			if($v['pid'] == $pid){
				$v["child"] = $this->node_merge($node,$v["id"]);
				$arr[] = $v;
			}
		}
		$arr	=	collect($arr);

		return  $arr	->	sortBy('order');
	}
}