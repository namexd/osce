<?php
/**
 * Created by PhpStorm.
 * User: fengyell <Luohaihua@misrobot.com>
 * Date: 2016/1/20
 * Time: 17:41
 */

namespace Modules\Osce\Commands;


use Illuminate\Console\Scheduling\Schedule;

class DefaultCommand
{
    protected $schedule;
    public function __construct(Schedule $schedule)
    {
        $this->schedule =   $schedule;
    }
    public function osceScheduling(){
        //TODO:请将 所有的任务在此 调用
    }
}