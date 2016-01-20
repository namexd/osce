<?php

namespace  App\Repositories\Message;

use App\Repositories\Message\Contracts\Message;

class EmailSender implements Message{

    protected $config;

    public function __construct($config)
    {
        $this->config = $config;
    }

    public function send($accept,$content,$title=null,$module=null,$sender=0,$pid=0){



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