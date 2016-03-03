<?php
/**
 * Created by PhpStorm.
 * User: fengyell <Luohaihua@misrobot.com>
 * Date: 2015/11/19
 * Time: 16:02
 */

namespace App\Http\Controllers;
use App\Entities\SysRoles;
use App\Entities\SysUserRole;
use App\Http\Controllers\Controller as BaseController;
use Illuminate\Support\Facades\Input;
use Illuminate\Http\Request;
use App\Entities\SysRolePermission;
use App\Entities\SysPermissionMenu;
use App\Entities\SysPermissionFunction;
use App\Entities\SysPermissions;
use App\Entities\SysMenus;
use App\Entities\SysFunctions;
use DB;
class AuthController extends BaseController
{   

    public function __construct(SysRoles $SysRoles){
        $this->SysRoles=$SysRoles;
    }
    /**
     * 权限管理页面
     * @method GET /auth/auth-manage
     * @access public
     *
     * @param Request $request get请求<br><br>
     * <b>get请求字段：</b>
     * @return view
     *
     * @version 0.8
     * @author whg <whg@misrobot.com>
     * @date 2015年12月15日17:39:08
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function AuthManage(){

        $roleList = $this->SysRoles->getRolesList();
        return view('usermanage.rolemanage',['roleList'=>$roleList]);
    }

    /**
     * 新建角色页面
     * @method GET /auth/role-manage
     * @access public
     *
     * @param Request $request get请求<br><br>
     * <b>get请求字段：</b>
     * @return view
     *
     * @version 0.8
     * @author whg <weihuiguo@misrobot.com>
     * @date 2015年12月15日11:36:27
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function newRolePage(){
        return view('role.role');
    }

    /**
     * 新建角色数据处理
     * @method GET /auth/role-manage
     * @access public
     *
     * @param Request $request get请求<br><br>
     * <b>get请求字段：</b>
     * @return view
     *
     * @version 0.8
     * @author whg <weihuiguo@misrobot.com>
     * @date 2015年12月15日11:39:12
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function postAddNewRole(Request $Request,SysRoles $SysRoles){
        $this->validate($Request,[
            'name' => 'required|min:2|max:10',
        ],[
            'name.required' => '角色名必填',
            'name.min'      => '角色名长度至少为2个',
            'name.max'      => '角色名长度最多为10个'
        ]);
        $data = [
            'name' => Input::get('name'),
            'slug' => rand(1,999999),
            'description'=>Input::get('description')
        ];
        //查看角色名是否已存在

        $RoleName = DB::connection('sys_mis')->table('sys_roles')->where('name','=',$data['name'])->first();
        if($RoleName){
            return  redirect()->back()->withErrors(['该角色名已存在']);
        }

        $addNewRole = DB::connection('sys_mis')->table('sys_roles')->insert($data);

        if($addNewRole){
            return redirect()->intended('/auth/auth-manage');
        }else{
            return  redirect()->back()->withErrors(['系统繁忙']);
        }
    }




    /**
     * 权限设置页面
     * @method GET /auth/set-permissions
     * @access public
     *
     * @param Request $request get请求<br><br>
     * <b>get请求字段：</b>
     * @return view
     *
     * @version 0.8
     * @author tangjun <tangjun@misrobot.com>
     * @date 2015年12月15日13:59:39
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */

    public function SetPermissions($id,SysRolePermission $SysRolePermission,SysMenus $SysMenus,SysFunctions $SysFunctions){

        $data = [];
        if(!empty($id)){
            $data['roleId'] = $id;
        }
		if($id==config('config.superRoleId',1))
		{
            $ex =   new \Exception('超级管理员权限不允许修改');
			return redirect()->back()->withErrors($ex->getMessage());
		}
        $PermissionList = $SysRolePermission->getPermissionList($data);
        $MenusList = $SysMenus->getMenusList();
        $FunctionsList = $SysFunctions->getFunctionsList();

        $MenusList = $this->node_merge($MenusList);
        $FunctionsList = $this->node_merge($FunctionsList);

        $PermissionIdArr = [];
        if(!empty($PermissionList)){
            foreach($PermissionList as $v){
                $PermissionIdArr[] = $v['permission_id'];
            }
        }

        $name=SysRoles::where('id',$id)->select('name')->first()->name;

        $data = [
            'PermissionIdArr'=>$PermissionIdArr,
            'MenusList'=>$MenusList,
            'FunctionsList'=>$FunctionsList,
            'role_id'=>$id,
            'name'=>$name
        ];
        return  view('usermanage.rolemanage_detail',$data);
    }

    /**
     * 权限设置页面
     * @method GET /auth/save-permissions
     * @access public
     *
     * @param Request $request get请求<br><br>
     * <b>get请求字段：</b>
     * @return view
     *
     * @version 0.8
     * @author tangjun <tangjun@misrobot.com>
     * @date 2015年12月15日13:59:39
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */

    public function SavePermissions(Request $Request,SysRolePermission $SysRolePermission){

        $this->validate($Request,[
            'role_id'       => 'required|integer',
        ]);


        $role_id = $Request->get('role_id');
        $permissionIdArr = $Request->get('permission_id');
        $status = $SysRolePermission->where('role_id','=',$role_id)->get();
        //dd($permissionIdArr);
        DB::connection('sys_mis')->beginTransaction();
        $rew = false;
        if(empty($status->toArray())){
            $rew = true;
        }else{
            $rew = $SysRolePermission->DelRolePermission($role_id);
        }
        if($rew){
            $R = $SysRolePermission->AddRolePermission($permissionIdArr,$role_id);
            if($R){
                DB::connection('sys_mis')->commit();
                return redirect()->intended('/auth/auth-manage');
            }else{
                DB::connection('sys_mis')->rollBack();
                dd('權限編輯失敗');
            }
        }else{
            DB::connection('sys_mis')->rollBack();
            dd('權限編輯失敗');
        }



    }
    /**
     * 删除角色
     * @method GET /auth/role-manage
     * @author whg <weihuiguo@misrobot.com>
     * @date 2015-12-15 14:20
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */

    public function deleteRole(){
//        dd(config('config.username'));

        $id = Input::get('id');

        if($id){
            $result = SysUserRole::where('role_id', $id)->first();
            if(!empty($result)){
                return  redirect()->back()->withErrors(['该角色已绑定用户，请先去用户管理中解绑用户！']);
            }

            $deleteRole = DB::connection('sys_mis')->table('sys_roles')
                        ->where(['id'=>$id])
                        ->whereNotIn('name',config('config.username'))
                        ->delete();
//            ->whereNotBetween('name',config('config.username'))



            if($deleteRole){
                return redirect()->intended('/auth/auth-manage');
            }else{
                return  redirect()->back()->withErrors(['系统繁忙']);
            }
        }else{
            return  redirect()->back()->withErrors(['系统繁忙']);
        }
    }

    /**
     * 编辑角色
     * @method GET /auth/role-manage
     * @author whg <weihuiguo@misrobot.com>
     * @date 2015-12-15 14:20
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function editRole(Request $Request){
        //dd(Input::get());
        $this->validate($Request,[
            'name' => 'required|min:2|max:10',
        ],[
            'name.required' => '角色名必填',
            'name.min'      => '角色名长度至少为2个',
            'name.max'      => '角色名长度最多为10个'
        ]);
//        $data = [
//            'name' => Input::get('name'),
//            'description'=>Input::get('description')
//        ];
//        $addNewRole = DB::connection('sys_mis')->table('sys_roles')->where(['id'=>Input::get('id')])->update($data);
//        if($addNewRole){
//            return redirect()->intended('/auth/auth-manage');
//        }else{
//            return  redirect()->back()->withErrors(['修改失败']);
//        }
        //TODO: zhoufuxiang 2016-2-23
        $name =  Input::get('name');
        $des  =  Input::get('description');
        $addNewRole = SysRoles::where(['id'=>Input::get('id')])->first();
        if($addNewRole->name == $name && $addNewRole->description == $des){
            return  redirect()->back()->withErrors(['未做修改']);
        }
        $addNewRole->name        = $name;
        $addNewRole->description = $des;
        if($addNewRole->save()){
            return redirect()->intended('/auth/auth-manage');
        }else{
            return  redirect()->back()->withErrors(['修改失败']);
        }
    }

    //递归通过pid 将其压入到一个多维数组!
    /*
     * $node 存放所有节点的节点数组
     * $access 判断有误权限
     * $pid 父id
     * return 多维数组;
     * */
    public  function node_merge($node,$pid=0){
        $arr = array();
        foreach($node as $v){
            if($v['pid'] == $pid){
                $v["child"] = $this->node_merge($node,$v["id"]);
                $arr[] = $v;
            }
        }
        return  $arr ;
    }

    /**
     * 添加基础权限信息
     * @method GET /auth/sdd-auth
     * @access public
     *
     * @param Request $request get请求<br><br>
     * <b>get请求字段：</b>
     * @return view
     *
     * @author tangjun <tangjun@misrobot.com>
     * @date 2015年12月17日13:59:39
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function AddAuth(SysPermissions $SysPermissions,SysMenus $SysMenus){
        $data = [];
        $osceAuthList   =   config('osce.authList',[]);
        foreach($osceAuthList as $id    =>  $item)
        {
            $data[$id] =   $item;
        }
        $SysMenus->AddMenus($data);
    }
}   