<?php

namespace  App\Repositories\Message;

use App\Repositories\Message\Contracts\Message;
use Log;

class SmsSender implements Message{

    protected $config;

    public function __construct($config)
    {
        $this->config = $config;
    }

    public function send($accept,$content,$title=null,$module=null,$sender=0,$pid=0){

        if(is_array($accept)){
            foreach($accept as $mobile){
                $this->sendSms($mobile,$content);
            }
        }
        else{
            $this->sendSms($accept,$content);
        }
    }

    public function sendSms($mobile,$content){

        $values=[
            'CorpID'=>$this->config['username'],
            'Pwd' =>$this->config['password'],
            'Content' => iconv('UTF-8', 'GBK', $content),
            'Mobile'=> $mobile,];

        $data = http_build_query($values);
        $result = file_get_contents($this->config['url'].$data);
        //Log::debug($result);
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
                    'mobile'        =>  $mobile,
                    'body'          =>  $content,
                    'result'        =>  $result,
                    'error'         =>  $error[intval($result)]
                ]);

            return false;
        }

        return true;

    }


    public function get($id){
        throw new \Exception('未实现的方法');
    }

    public function messages($accept,$sender=null,$module=null,$status=1,$pageSize=10,$pageIndex=0){
        throw new \Exception('未实现的方法');
    }


    public function delete($id){
        throw new \Exception('未实现的方法');
    }
}