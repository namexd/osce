<?php namespace Modules\Osce\Http\Controllers;

use App\Entities\SysMenus;
use Pingpong\Modules\Routing\Controller;

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
			$connection	=	\DB::connection('sys_mis');
			$userRole	=	$connection	->	table('sys_user_role')	->	where('user_id','=',$user->id)->first();

			if(is_null($userRole))
			{
				throw new \Exception('非法用户，请按照要求注册');
			}

			$MenusList = $SysMenus	->getRoleMenus($userRole->role_id);

			$MenusList = $this		->node_merge($MenusList);

			$MenusList	=	collect($MenusList);
			return view('osce::admin.layouts.admin',['list'=>$MenusList,'role_id'=>$userRole->role_id]);
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