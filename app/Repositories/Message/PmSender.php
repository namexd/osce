<?php

namespace  App\Repositories\Message;

use App\Repositories\Message\Contracts\Message;

class PmSender implements Message{

    protected $config;

    public function __construct($config)
    {
        $this->config = $config;
    }

    public function send($accept,$content,$title=null){


    }

}