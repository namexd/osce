<?php
/**
 * Created by PhpStorm.
 * User: fengyell <Luohaihua@misrobot.com>
 * Date: 2016/1/18
 * Time: 15:21
 */

namespace Modules\Osce\Http\Controllers\Admin;

use Modules\Osce\Http\Controllers\CommonController;

class IndexController extends CommonController
{
    public function dashboard(){
        return view('osce::admin.index.dashboard');
    }
}