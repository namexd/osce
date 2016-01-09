<?php

namespace App\Repositories\Message;

use Illuminate\Support\Facades\Facade;

class MessageFacade extends Facade
{
    /**
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return "messages";
    }

    static public function __callStatic($name, $args)
    {
        return self::resolveFacadeInstance("messages.{$name}");
    }
}