<?php
/**
 * Created by PhpStorm.
 * User: j5110
 * Date: 2016/2/3
 * Time: 14:03
 */

namespace Modules\Osce\Http\Controllers;

use App\Repositories\Message\EmailSender;
use Modules\Osce\Http\Controllers\Admin\ConfigController;
use Mail;

class MailController extends CommonController
{
    public function send()
    {
        $a = new EmailSender(new ConfigController());
        $a->send('174451864@qq.com', '这是测试内容', '测试内容');
    }
}