<?php
/**
 * Created by PhpStorm.
 * User: fengyell <Luohaihua@misrobot.com>
 * Date: 2016/1/23
 * Time: 11:24
 */

namespace Modules\Osce\Http\Controllers\Api;

use Modules\Osce\Http\Controllers\CommonController;

class WechatController extends CommonController
{
    public function service(){
        echo config('wechat.token');
    }
}