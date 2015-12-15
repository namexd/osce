<?php
/**
 * Created by PhpStorm.
 * User: fengyell <Luohaihua@misrobot.com>
 * Date: 2015/11/19
 * Time: 16:02
 */

namespace App\Http\Controllers;

use App\Http\Controllers\Controller as BaseController;

class AuthController extends BaseController
{
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
        return  view('usermanage.rolemanage');
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
     * @date 2015-12-15 14:20
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function SetPermissions(){
        return  view('usermanage.rolemanage_detail');
    }
}