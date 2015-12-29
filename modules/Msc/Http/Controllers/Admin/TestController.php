<?php namespace Modules\Msc\Http\Controllers\Admin;

use Pingpong\Modules\Routing\Controller;
use URL;
class TestController extends Controller {

    /**
     * 测试控制器
     * @method GET
     * @url /msc/admin/test/index
     * @access public
     *
     * @return json
     *
     * @author tangjun <tangjun@misrobot.com>
     * @date 2015-12-28 15:55
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function Index(){
        return view('msc::admin.labmanage.lab_maintain');
    }


}