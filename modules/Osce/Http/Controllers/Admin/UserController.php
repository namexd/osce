<?php
/**
 * Created by PhpStorm.
 * User: fengyell <Luohaihua@misrobot.com>
 * Date: 2015/12/31
 * Time: 14:59
 */

namespace Modules\Osce\Http\Controllers\Admin;


use App\Http\Requests\Request;
use Modules\Osce\Entities\Staff;
use Modules\Osce\Http\Controllers\CommonController;

class UserController extends CommonController
{
    public function getStaffList(Request $request){
        $list   =   new Staff();
        //return view();
    }
}