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
        Mail::raw('这是一封测试邮件', function ($message) {
            $to = '174451864@qq.com';
            $message ->to($to)->subject('测试邮件');
        });
    }

}