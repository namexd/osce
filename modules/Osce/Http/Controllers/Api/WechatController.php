<?php
/**
 * Created by PhpStorm.
 * User: fengyell <Zouyuchao@sulida.com>
 * Date: 2016/1/23
 * Time: 11:24
 */

namespace Modules\Osce\Http\Controllers\Api;

use Modules\Osce\Http\Controllers\CommonController;
use Overtrue\Wechat\Server;

class WechatController extends CommonController
{
    public function service(Server $server){
        $server ->  on('message',function(){
            return "获取成功";
        });
        return $server->serve();
    }
}