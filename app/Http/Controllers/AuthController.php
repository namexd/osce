<?php
/**
 * Created by PhpStorm.
 * User: fengyell <Luohaihua@misrobot.com>
 * Date: 2015/11/19
 * Time: 16:02
 */

namespace App\Http\Controllers;
use App\Entities\SysRoles;
use App\Http\Controllers\Controller as BaseController;
use Illuminate\Support\Facades\Input;
use Illuminate\Http\Request;
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
     * @author tangjun <tangjun@misrobot.com>
     * @date 2015-12-15 10:35
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function AuthManage(){

        $roleList = $this->SysRoles->getRolesList();
        return view('role.role',['roleList'=>$roleList]);
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
            ]);
        $data = [
            'name' => Input::get('name'),
            'slug' => Input::get('slug'),
            'description'=>Input::get('description')
        ];
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
     * @author whg <weihuiguo@misrobot.com>
     * @date 2015年12月15日13:59:39
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */ 

    public function SetPermissions(){
        return  view('usermanage.rolemanage_detail');
    }

    /**
     * 删除角色
     * @method GET /auth/role-manage
     * @author tangjun <tangjun@misrobot.com>
     * @date 2015-12-15 14:20
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */

    public function deleteRole(){
        $id = Input::get('id');
        if($id){
            $deleteRole = DB::connection('sys_mis')->table('sys_roles')->where(['id'=>$id])->delete();
            if($deleteRole){
                return redirect()->intended('/auth/auth-manage');
            }else{
                return  redirect()->back()->withErrors(['系统繁忙']);
            }
        }else{
            return  redirect()->back()->withErrors(['系统繁忙']);
        }
    }


    public function aa(){
         return  redirect()->back()->withErrors(['系统繁忙']);
    }
}   