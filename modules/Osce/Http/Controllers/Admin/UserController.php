<?php
/**
 * Created by PhpStorm.
 * User: fengyell <Luohaihua@misrobot.com>
 * Date: 2015/12/31
 * Time: 14:59
 */

namespace Modules\Osce\Http\Controllers\Admin;

use App\Entities\SysRoles;
use App\Entities\SysUserRole;
use Illuminate\Http\Request;
use Modules\Osce\Entities\Staff;
use Modules\Osce\Http\Controllers\CommonController;
use Modules\Osce\Repositories\Common;
use App\Entities\User;
use Auth;

class UserController extends CommonController
{

    /**
     * 用户列表
     * @url /osce/admin/user/staff-list
     * @access public
     *
     * * @param Request $request
     * <b>get 请求字段：</b>
     * * string        参数英文名        参数中文名(必须的)
     * * string        参数英文名        参数中文名(必须的)
     * * string        参数英文名        参数中文名(必须的)
     * * string        参数英文名        参数中文名(必须的)
     *
     * @return view
     *
     * @version 1.0
     * @author Luohaihua <Luohaihua@misrobot.com>
     * @date ${DATE}${TIME}
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     *
     */
    public function getStaffList(Request $request,Common $common){
        $list   =   $common->getUserList();
        return view('osce::admin.sysmanage.usermanage',['list'=>$list]);
    }

    public function getEditStaff(Request $request){
        $this   ->  validate($request,[
            'id'    =>  'required',
        ]);

        $id =   $request    ->get('id');

        $staff  =   User::find($id);
        return view('osce::admin.sysmanage.usermanage_edit',['item'=>$staff]);
    }

    /**
     *
     * @url GET /osce/admin/user/add-user
     * @access public
     *
     * @return \Illuminate\View\View
     *
     * @version 1.0
     * @author Luohaihua <Luohaihua@misrobot.com>
     * @date 2015-12-29 17:09
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     *
     */
    public function getAddUser(){
        return view('osce::admin.sysmanage.usermanage_add');
    }

    /**
     * 注册管理员
     * @url GET /osce/admin/user/add-user
     * @access public
     *
     * @param Request $request
     * @param Common $common
     * <b>get请求字段：</b>
     * * string        参数英文名        参数中文名(必须的)
     * * string        参数英文名        参数中文名(必须的)
     * * string        参数英文名        参数中文名(必须的)
     * * string        参数英文名        参数中文名(必须的)
     *
     * @return $this|\Illuminate\Http\RedirectResponse
     *
     * @version 1.0
     * @author Luohaihua <Luohaihua@misrobot.com>
     * @date 2015-12-29 17:09
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     *
     */
    public function postAddUser(Request $request,Common $common){
        $this->validate($request,[
            'name'  =>  'required',
            'gender'=>  'sometimes|in:0,1,2'
        ]);
        $mobile     =   e($request    ->  get('mobile'));
        $name       =   e($request    ->  get('name'));
        $gender     =   intval($request    ->  get('gender'));
        $data       =   [
            'name'      =>  $name,
            'mobile'    =>  $mobile,
            'gender'    =>  $gender,
        ];
        try{
            $user   =   User::where('mobile','=',$mobile)->first();
            if(!is_null($user))
            {
                //throw new \Exception('该手机已经被注册了');

                $common ->  relativeAdminUser($user);
            }
            else
            {
                $user   =   $common     ->  createAdminUser($data);
            }

            return redirect()->route('osce.admin.user.getStaffList');

        } catch(\Exception $ex){
            return redirect()->back()->withErrors($ex->getMessage());
        }
    }

    public function postEditUser(Request $request,Common $common){
        $this->validate($request,[
            'name'  =>  'required',
            'gender'=>  'sometimes|in:0,1,2'
        ]);
        $id         =   intval($request     ->  get('id'));
        $mobile     =   e($request          ->  get('mobile'));
        $name       =   e($request          ->  get('name'));
        $gender     =   intval($request     ->  get('gender'));
        $data       =   [
            'name'      =>  $name,
            'mobile'    =>  $mobile,
            'gender'    =>  $gender,
        ];
        try{
            if(!$common     ->  updateAdminUser($id,$data))
            {
                throw new \Exception('用户修改失败');
            }
            return redirect()->route('osce.admin.user.getStaffList');
        }
        catch(\Exception $ex)
        {
            return redirect()->back()->withErrors($ex->getMessage());
        }
    }

    public function postDelUser(Request $request){
        $this->validate($request,[
            'id'  =>  'required'
        ]);
        $id =   intval($request    ->  get('id'));
        try{
            $user   =   Auth::user();
            if($user->id ==$id){
                throw new \Exception('此为当前登录人的账号，无法删除自己！');
            }
            $user   =   User::find($id);
            if($user->delete()){
                return $this->success_data('删除成功！');
            } else{
                throw new \Exception('删除失败！');
            }
        } catch(\Exception $ex){
            return $this->fail($ex);
        }
    }
    public function getLogout() {

        if(Auth::check())
        {
            try{
                Auth::logout();
            } catch (\Exception $ex){
                return redirect()->route('osce.admin.postIndex')->with('message','你现在已经退出登录了!');
            }
        }
        return redirect()->route('osce.admin.postIndex')->with('message','你现在已经退出登录了!');
    }

    /**
     *用户权限选择
     * @method GET
     * @url user/change-users-role
     * @access public
     *
     * @param Request $request post请求<br><br>
     * <b>post请求字段：</b>
     * * int        id        用户id(必须的)
     *
     * @return ${response}
     *
     * @version 1.0
     * @author zhouchong <zhouchong@misrobot.com>
     * @date ${DATE} ${TIME}
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function getChangeUsersRole(Request $request){
        $this->validate($request,[
            'id'  =>  'required'
        ]);
         $id=$request->get('id');
         $roleId=SysUserRole::where('user_id',$id)->select()->first();
         $roles=SysRoles::select()->get();
         $data=[];
         foreach($roles as $role){
             if($roleId){
                   $user_role_id=$roleId->role_id;
                 if($role->id!=1 && $role->id!=2 && $role->id!=5 && $role->id!=4 && $role->id!=$user_role_id){
                     $data[]=[
                         'role_id'=>$role->id,
                         'role_name'=>$role->name,
                     ];
                 }
             }else{
                 if($role->id!=1 && $role->id!=2 && $role->id!=5 && $role->id!=4 ){
                     $data[]=[
                         'role_id'=>$role->id,
                         'role_name'=>$role->name,
                     ];
                 }
             }

         }

         return view('osce::admin.sysmanage.usermanage_change_role')->with(['role_id'=>$roleId,'data'=>$data,'user_id'=>$id]);
    }

    /**
     *更改修改用户的权限
     * @method POST
     * @url user/edit-users-role
     * @access public
     *
     * @param Request $request post请求<br><br>
     * <b>post请求字段：</b>
     * * int        role_id        角色id(必须的)
     * * int        user_id        用户id(必须的)
     *
     * @return ${response}
     *
     * @version 1.0
     * @author zhouchong <zhouchong@misrobot.com>
     * @date ${DATE} ${TIME}
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function postEditUserRole(Request $request){
              $this->validate($request,[
                  'role_id'  => 'required',
                  'user_id'  => 'required'
              ],[
                  'role_id'  => '请选择角色',
                  'user_id'  => '该用户不存在',
              ]);
               $user_id=$request->input('user_id');
               $role_id=$request->input('role_id');
               $result=SysUserRole::where('user_id','=',$user_id)->update([
                      'role_id' =>  $role_id
                  ]);

               if($result){
                    return redirect()->route('osce.admin.user.getStaffList')->with('message','修改成功!');
               }
                    return redirect()->back()->withErrors('修改权限失败!');

    }
}