<?php

namespace App\Jobs;

use App\Jobs\Job;
use App\Entities\Sys\User;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Bus\SelfHandling;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendReminderSms extends Job implements SelfHandling, ShouldQueue
{
    use InteractsWithQueue, SerializesModels;

    protected $message,$mobile;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($mobile,$message)
    {
        $this->message=$message;
        $this->mobile=$mobile;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $api_url='http://mb345.com/ws/BatchSend.aspx?';
        $corp_id='LKSDK0004929';
        $api_pwd='jkwqm814@';
        $timestamp=time();

        $values=[
            'CorpID'=>$corp_id,
            'Pwd' =>$api_pwd,
            'Content' => iconv('UTF-8', 'GBK', $this->message),
            'Mobile'=> $this->mobile,];

        $data = http_build_query($values);
        $result = file_get_contents($api_url.$data);

        if(intval($result)<0)
        {
            $error=[
                '-1'=>'帐号未注册',
                '-2'=>'网络访问超时，请重试',
                '-3'=>'密码错误',
                '-5'=>'余额不足',
                '-6'=>'定时发送时间不是有效的时间格式',
                '-7'=>'提交信息末尾未加签名，请添加中文企业签名【 】',
                '-8'=>'发送内容需在1到300个字之间',
                '-9'=>'发送号码为空',

            ];

            Log::error('短信发送失败',
                [
                    'mobile'        =>  $this->mobile,
                    'body'          =>  $this->message,
                    'result'        =>  $result,
                    'error'         =>  $error[intval($result)]
                ]);
        }
    }
}
