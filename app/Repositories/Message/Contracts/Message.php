<?php

namespace App\Repositories\Message\Contracts;

interface Message {

    public function send($accept,$content,$title=null);

}