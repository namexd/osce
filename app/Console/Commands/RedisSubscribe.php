<?php
/**
 * Created by PhpStorm.
 * User: j5110
 * Date: 2016/3/30
 * Time: 21:25
 */

namespace App\Console\Commands;


use Illuminate\Console\Command;
use Redis;

class RedisSubscribe extends Command
{
    /**
     * 控制台命令名称
     *
     * @var string
     */
    protected $signature = 'redis:subscribe';

    /**
     * 控制台命令描述
     *
     * @var string
     */
    protected $description = 'Subscribe to a Redis channel';

    /**
     * 执行控制台命令
     *
     * @return mixed
     */
    public function handle()
    {
        Redis::subscribe(1, function($message) {
            echo $message;
        });
    }

}