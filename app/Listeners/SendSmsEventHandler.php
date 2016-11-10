<?php

namespace App\Listeners;


use App\Events\SendSmsEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Log;

class SendSmsEventHandler implements ShouldQueue {

	use InteractsWithQueue;

	/**
	 * Create the event handler.
	 *
	 * @return void
	 */
	public function __construct()
	{
		//
	}

	/**
	 * Handle the event.
	 *
	 * @param  SendEmail  $event
	 * @return void
	 */
	public function handle(SendSmsEvent $event)
	{
		 $this->send_sms($event->mobile,
                $event->content);
	}

  protected  function send_sms($mobile,$body) {

        $api_url='http://admin.sms9.net/houtai/sms.php';
        $api_pwd='695845';
        $timestamp=time();

        $values=[
            'cpid'=>10410,
            'password' =>md5($api_pwd.'_'.$timestamp.'_topsky'),
            'timestamp' => $timestamp ,
            'channelid' => '1462',
            'msg' => iconv('UTF-8', 'GBK', $body),
            'tele'=> $mobile,];

        $data = http_build_query($values);

        $opts = array(
            'http'=>array(
            'method'=>"POST",
                'header' => 'Content-type:application/x-www-form-urlencoded',
                'content' => $data,
                'timeout'=>60,
        ));

        $context = stream_context_create($opts);
        $result = file_get_contents($api_url, false, $context);

        if(!stripos($result,'error'))
        {
            $error=[
                '-1'=>'传递参数错误',
                '-2'=>'用户id或密码错误',
                '-3'=>'通道id错误',
                '-4'=>'手机号码错误',
                '-5'=>'短信内容错误',
                '-6'=>'绑定ip错误',
                '-7'=>'绑定ip错误',
                '-8'=>'未带签名',
                '-9'=>'签名字数不对',
                '-10'=>'通道暂停',
                '-11'=>'该时间禁止发送',
                '-12'=>'时间戳错误',
                '-13'=>'编码异常',
                '-14'=>'发送被限制',
            ];

            Log::error('短信发送失败',
                [
                    'mobile'        =>  $mobile,
                    'body'          =>  $body,
                    'result'        =>  $result
                ]);
        }

        //dd($result);

      /**
      错误代码描述:
      传递参数错误=-1
      用户id或密码错误=-2
      通道id错误=-3
      手机号码错误=-4
      短信内容错误=-5
      余额不足错误=-6
      绑定ip错误=-7
      未带签名=-8
      签名字数不对=-9
      通道暂停=-10
      该时间禁止发送=-11
      时间戳错误=-12
      编码异常=-13
      发送被限制=-14
       **/


}
}
