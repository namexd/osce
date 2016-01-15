<?php
/**
 * Created by PhpStorm.
 * User: fengyell <Luohaihua@misrobot.com>
 * Date: 2015/12/31
 * Time: 14:59
 */

namespace Modules\Osce\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Modules\Osce\Entities\Staff;
use Modules\Osce\Http\Controllers\CommonController;
use Modules\Osce\Repositories\Common;
use App\Entities\User;

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
            $user   =   $common     ->  createAdminUser($data);
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
            return redirect()->back()->withErrors($ex);
        }
    }

    public function getDelUser(Request $request){
        $this->validate($request,[
            'id'  =>  'required'
        ]);
        $id =   intval($request    ->  get('id'));
        $user   =   User::find($id);
        if($user->delete())
        {
            return redirect()->route('osce.admin.user.getStaffList');
        }
        else
        {
            return redirect()->back()->withErrors(new \Exception('删除失败'));
        }
    }
}