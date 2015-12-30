<?php
/**
 * Created by PhpStorm.
 * @author tangjun <tangjun@misrobot.com>
 * @date 2015-12-30 11:28
 * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
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
     * ��΢���û�������ͨ�ı���Ϣ
     * @param $msg
     * @param $openid
     * @return bool
     * @throws \Exception
     * @throws \Overtrue\Wechat\Exception
     * @author tangjun <tangjun@misrobot.com>
     * @date    2015��12��30��11:32:13
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function SendWeChatMsg($msg,$openid){
        $userService = new \Overtrue\Wechat\Staff(config('wechat.app_id'), config('wechat.secret'));
        return $userService->send($msg)->to($openid);
    }


    /**
     * ���ݲ�ͬ�Ľ�ɫ��ȡ��صĴ�������
     * @param $role
     * @param $uid
     * @return array
     * @author tangjun <tangjun@misrobot.com>
     * @date    2015��12��30��11:43:30
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function AdjPending($role,$uid){
        $data = [];
        return  $data;
    }

}