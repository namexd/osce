<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/4/27 0027
 * Time: 14:23
 */

namespace Modules\Osce\Http\Controllers\Admin;


use Modules\Osce\Entities\Message;
use Modules\Osce\Http\Controllers\CommonController;
use Illuminate\Http\Request;

class MessageController extends CommonController
{

    /**
     * 发送信息（短信、邮件等）
     * @url  GET  /osce/admin/message/send-message
     * @param   Request $request
     * 
     * @author  Zhoufuxiang <zhoufuxiang@misrobot.com>
     * @date    2016-04-27  15:00:00
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function getSendMessage()
    {
        $messageModel = new Message();
        $result = $messageModel->sendMessage();
        if($result){
            echo date('Y-m-d H:i:s').' ： '.$result;
        }
    }
}