<?php

namespace  App\Repositories\Message;

use App\Repositories\Message\Contracts\Message;
use Mail;
class EmailSender implements Message{

    protected $config;

    public function __construct($config)
    {
        $this->config = $config;
    }

    public function send($accept,$content,$title=null,$module=null,$sender=0,$pid=0) {
        $flag = Mail::raw($content,function($message) use($accept,$title){
//            $message->from(config('mail.from.address'), config('mail.from.name'));
            $message->to('174451864@qq.com')->subject('邮件');
        });

        if($flag){
            echo '发送邮件成功，请查收！';
        } else{
            echo '发送邮件失败，请重试！';
        }

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