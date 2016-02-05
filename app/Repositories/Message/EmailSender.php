<?php

namespace  App\Repositories\Message;

use App\Repositories\Message\Contracts\Message;
use Mail;
use Modules\Osce\Entities\Exam;

class EmailSender implements Message{

    protected $config;

    public function __construct($config)
    {
        $this->config = $config;
    }

    public function send($accept,$content,$title=null,$module=null,$sender=0,$pid=0) {
        try {
            if (is_array($accept)) {
                foreach ($accept as $item) {
                    if (!empty($item)) {
                        $flag = Mail::raw($content,function($message) use($item,$title){
                            $message->from(config('mail.from.address'), config('mail.from.name'));
                            $message->to($item)->subject($title);
                        });
                    }
                }
            } else {
                $flag = Mail::raw($content,function($message) use($accept,$title){
                    $message->from(config('mail.from.address'), config('mail.from.name'));
                    $message->to($accept)->subject($title);
                });
            }

            if(!$flag){
                throw new \Exception('邮件发送失败，请重试');
            }
        } catch (\Exception $ex) {
            if ($ex->getCode() == 554) {
                throw new \Exception('邮件发送过于频繁');
            }
            throw $ex;
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
