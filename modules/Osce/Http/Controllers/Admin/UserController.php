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
use Modules\Osce\Entities\Teacher;
use Modules\Osce\Http\Controllers\CommonController;
use Modules\Osce\Repositories\Common;
use App\Entities\User;
use Auth;
use DB;

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
    public function getStaffList(Request $request, Common $common)
    {
        $list = $common->getUserList();
        
        return view('osce::admin.systemManage.user_manage', ['list' => $list]);
    }

    public function getEditStaff(Request $request)
    {
        $this->validate($request, [
            'id' => 'required',
        ]);

        $id = $request->get('id');

        $staff = User::find($id);
        return view('osce::admin.systemManage.user_manage_edit', ['item' => $staff]);
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
    public function getAddUser()
    {
        return view('osce::admin.systemManage.user_manage_add');
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
    public function postAddUser(Request $request, Common $common)
    {
        $this->validate($request, [
            'name' => 'required',
            'gender' => 'sometimes|in:0,1,2'
        ]);
        $mobile = e($request->get('mobile'));
        $name = e($request->get('name'));
        $gender = intval($request->get('gender'));
        $data = [
            'name' => $name,
            'mobile' => $mobile,
            'gender' => $gender,
        ];
        try {
            $user = User::where('mobile', '=', $mobile)->first();
            if (!is_null($user)) {
                //throw new \Exception('该手机已经被注册了');

                $common->relativeAdminUser($user);
            } else {
                $user = $common->createAdminUser($data);
            }

            return redirect()->route('osce.admin.user.getStaffList');

        } catch (\Exception $ex) {
            return redirect()->back()->withErrors($ex->getMessage());
        }
    }

    public function postEditUser(Request $request, Common $common)
    {
        $this->validate($request, [
            'name' => 'required',
            'gender' => 'sometimes|in:0,1,2'
        ]);
        $id = intval($request->get('id'));
        $mobile = e($request->get('mobile'));
        $name = e($request->get('name'));
        $gender = intval($request->get('gender'));
        $data = [
            'name' => $name,
            'mobile' => $mobile,
            'gender' => $gender,
        ];
        try {
            if (!$common->updateAdminUser($id, $data)) {
                throw new \Exception('用户修改失败');
            }
            return redirect()->route('osce.admin.user.getStaffList');
        } catch (\Exception $ex) {
            return redirect()->back()->withErrors($ex->getMessage());
        }
    }

    public function postDelUser(Request $request)
    {
        $this->validate($request, [
            'id' => 'required'
        ]);
        $id = intval($request->get('id'));
        $role_id = config('osce.adminRoleId');
        try {
            $user = Auth::user();
            if ($user->id == $id) {
                throw new \Exception('此为当前登录人的账号，无法删除自己！');
            }
            //删除对应的系统管理员角色 TODO: Zhoufuxiang 216-04-13
            $sysUserRoles = SysUserRole::where('user_id','=',$id)->where('role_id','=',$role_id)->get();
            foreach ($sysUserRoles as $sysUserRole) {
                if(!$sysUserRole->delete()){
                    throw new \Exception('删除该用户管理员角色失败！');
                }
            }

//            $user = User::find($id);
//            if (!$user->delete()) {
//                throw new \Exception('删除失败！');
//            }

            return $this->success_data('删除成功！');

        } catch (\Exception $ex) {
            return $this->fail($ex);
        }
    }

    public function getLogout()
    {

        if (Auth::check()) {
            $nowTime = time();
            try {
                Auth::logout();
                //修改用户最后登录时间
                $user = Auth::user();
                if ($user) {
                    throw new \Exception('未找到当前用户信息');
                } else {
                    $connection = \DB::connection('sys_mis');
                    $connection->table('users')->where('id', $user->id)->update(['lastlogindate' => $nowTime]);
                }
            } catch (\Exception $ex) {
                return redirect()->route('osce.admin.postIndex')->with('message', '你现在已经退出登录了!');
            }
        }
        return redirect()->route('osce.admin.postIndex')->with('message', '你现在已经退出登录了!');
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
    public function getChangeUsersRole(Request $request)
    {
        $this->validate($request, [
            'id' => 'required'
        ]);
        $id = $request->get('id');
        try {
            $roleId= SysUserRole::where('user_id', $id)->get();
            $roles = SysRoles::whereNotIn('name', ['监考老师', '巡考老师', '超级管理员', '考生'])->get();

            $data = [];

            foreach ($roles as $role) {
                if ($roleId) {
                    $user_role_id = $roleId->pluck('role_id')->toArray();
                    if ($role->id != config('config.teacherRoleId') && $role->id != config('config.patrolRoleId') && $role->id != config('config.spRoleId') && $role->id != config('config.teacherRoleId') && !in_array($role->id, $user_role_id)) {
                        $data[] = [
                            'role_id' => $role->id,
                            'role_name' => $role->name,
                        ];
                    }
                } else {
                    if ($role->id != config('config.teacherRoleId') && $role->id != config('config.patrolRoleId') && $role->id != config('config.spRoleId') && $role->id != config('config.teacherRoleId')) {
                        $data[] = [
                            'role_id' => $role->id,
                            'role_name' => $role->name,
                        ];
                    }
                }

            }

            return view('osce::admin.systemManage.user_manage_change_role')->with([
                'role_ids' => $roleId,
                'data' => $data,
                'user_id' => $id
            ]);

        } catch (\Exception $ex) {

            return view('osce::admin.systemManage.user_manage_change_role')->with([
                'role_ids' => $roleId,
                'data' => $data,
                'user_id' => $id
            ])->withErrors($ex->getMessage());
        }
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
    public function postEditUserRole(Request $request)
    {
        $connection = DB::connection('sys_mis');
        $connection ->beginTransaction();

        try{
            $this->validate($request, [
                'role_id' => 'required',
                'user_id' => 'required'
            ], [
                'role_id' => '请选择角色',
                'user_id' => '该用户不存在',
            ]);
            $user_id = $request->input('user_id');
            $roleIds = $request->input('role_id');

            //老师角色 集合
            $teacherRoles = [
                config('osce.invigilatorRoleId'),
                config('osce.patrolRoleId'),
                config('osce.spRoleId')
            ];

            //获取用户原有的所有角色
            $original = SysUserRole::where('user_id','=',$user_id)->get()->pluck('role_id')->toArray();
            $delRoles = array_diff($original, $roleIds);     //原来有，现在不具有（需删除）
            $addRoles = array_diff($roleIds, $original);     //现在有，原来不具有（需添加）

            //处理用户 角色增删，归档问题
            $this->handleUserRoles($user_id, $addRoles, $delRoles, $teacherRoles);

            //TODO: 学生角色归档、还原归档问题   Zhoufuxiang 2016-04-13

            $connection->commit();

            return redirect()->route('osce.admin.user.getStaffList')->with('message', '修改成功!');

        } catch (\Exception $ex){

            $connection->rollBack();
            return redirect()->back()->withErrors($ex->getMessage());
        }
    }

    /**
     * 处理用户 角色增删，归档问题
     * @param $user_id
     * @param $addRoles
     * @param $delRoles
     * @param $roles
     *
     * @author Zhoufuxiang 2016-04-13
     * @return bool
     * @throws \Exception
     */
    public function handleUserRoles($user_id, $addRoles, $delRoles, $teacherRoles)
    {
        //将其他不同的角色，加入数据库中
        if(count($addRoles)>0){
            foreach ($addRoles as $addRole)
            {
                $data = ['role_id' => $addRole, 'user_id' => $user_id];
                if(!SysUserRole::create($data)){

                    throw new \Exception('修改权限失败!');
                }
                //如果该用户曾经 有老师的角色，则还原归档
                if(in_array($addRole, $teacherRoles)){
                    //其他 字段参数
                    $params = ['type' => Common::getTeacherTypeByRoleId($addRole)];
                    //老师 还原归档
                    Common::resetArchived(new Teacher(), $user_id, '老师角色归档失败!', $params);
                }
            }
        }

        //删除 已经不需要的 用户对应 角色关系
        if(count($delRoles)>0){
            foreach ($delRoles as $delRole) {
                //如果删除的角色中，有老师角色，则将老师归档
                if(in_array($delRole, $teacherRoles)){

                    //老师归档
                    Common::archived(new Teacher(), $user_id, '老师角色归档失败!');
                }

                //删除用户对应 角色关系
                $sysUserRole = SysUserRole::where('user_id','=',$user_id)->where('role_id','=',$delRole)->first();
                if (!is_null($sysUserRole)){

                    if(!$sysUserRole->delete()){
                        throw new \Exception('修改权限失败!');
                    }
                }
            }
        }

        return true;
    }

    /**
     * 异步判断，删除用户对应的角色中是否有老师角色
     * @param Request $request
     * @author Zhoufuxiang 2016-3-30
     * @return string
     */
    public function getJudgeUserRole(Request $request){
        try{
            $this->validate($request,[
                'user_id'   => 'required',
                'roleIds'   => 'required',
            ],[
                'roleIds.required' => '角色必选！'
            ]);

            $user_id = $request->get('user_id');
            $roleIds = $request->get('roleIds');
            $roles   = [
                config('osce.invigilatorRoleId'),
                config('osce.patrolRoleId'),
                config('osce.spRoleId')
            ];

            $original= SysUserRole::where('user_id','=',$user_id)->get()->pluck('role_id')->toArray();
            $delRoles = array_diff($original, $roleIds);     //原来有，现在不具有（需删除）
            if(!empty($delRoles)){
                foreach ($delRoles as $delRole) {
                    if(in_array($delRole, $roles)){
                        return $this->success_data('',2,'警告：该用户原有的老师角色 确定要删除！');
                    }
                }
            }

            return $this->success_data('',1,'success');

        } catch (\Exception $ex){

            return $this->success_data('',-1,'请选择角色！');
        }

    }
}