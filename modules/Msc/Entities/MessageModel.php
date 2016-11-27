<?php
/**
 * Created by PhpStorm.
 * @author tangjun <tangjun@163.com>
 * @date 2015-12-30 11:28
 * @copyright 2013-2015 MIS 163.com Inc. All Rights Reserved
 */
namespace Modules\Msc\Entities;

use Illuminate\Database\Eloquent\Model;
use App\Entities\SysRoles;
/**
 * Class MessageModel
 * @package Modules\Msc\Entities
 */
class MessageModel extends  Model
{
    /**
     * 像微信用户发送普通文本消息
     * @param $msg
     * @param $openid
     * @return bool
     * @throws \Exception
     * @throws \Overtrue\Wechat\Exception
     * @author tangjun <tangjun@163.com>
     * @date    2015年12月30日11:32:13
     * @copyright 2013-2015 MIS 163.com Inc. All Rights Reserved
     */
    public function SendWeChatMsg($msg,$openid){
        $userService = new \Overtrue\Wechat\Staff(config('wechat.app_id'), config('wechat.secret'));
        return $userService->send($msg)->to($openid);
    }


    /**
     * 根据不同的角色获取相关的代办事项
     * @param $role
     * @param $uid
     * @return array
     * @author tangjun <tangjun@163.com>
     * @date    2015年12月30日11:43:30
     * @copyright 2013-2015 MIS 163.com Inc. All Rights Reserved
     */
    public function AdjPending($role,$uid){
        $data = [];
        return  $data;
    }

}