<?php

namespace App\Repositories\Message;

use App\Repositories\Message\Contracts\Factory as FactoryContract;
use App\Repositories\Message\Contracts\Message as Msg;

class MessageManager implements FactoryContract {

    protected $app;

    protected $messages = [];

    protected $customCreators = [];

    public function __construct($app)
    {
        $this->app = $app;
    }

    public function message($name = null){

        $name = $name ?: $this->getDefaultDriver();

        return $this->messages[$name] = $this->get($name);

    }

    protected function get($name)
    {
        return isset($this->messages[$name]) ? $this->messages[$name] : $this->resolve($name);
    }

    public function driver($driver = null)
    {
        return $this->message($driver);
    }

    public function getDefaultDriver()
    {
        return $this->app['config']['message.default'];
    }

    protected function resolve($name)
    {
        $config = $this->getConfig($name);

        if (isset($this->customCreators[$config['driver']])) {
            throw new \Exception('未定义消息驱动类型');
        }

        return $this->{'create'.ucfirst($config['driver']).'Driver'}($config);
    }

    public function createSmsDriver(array $config)
    {
        return new SmsSender($config);
    }

    public function createWechatDriver(array $config)
    {
        return new WechatSender($config);
    }

    public function createPmDriver(array $config)
    {
        return new PmSender($config);
    }

    public function createEmailDriver(array $config)
    {
        return new EmailSender($config);
    }


    protected function getConfig($name)
    {
        return $this->app['config']["message.messages.{$name}"];
    }



}