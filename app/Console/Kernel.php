<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Modules\Osce\Http\Controllers\Admin\MessageController;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        \App\Console\Commands\Inspire::class,
        \App\Console\Commands\MigrateMysqlSchemaCommand::class,
        \App\Console\Commands\RedisSubscribe::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $moduleSchedulesList    =   config('config.ModuleSchedulesList',[]);
        foreach($moduleSchedulesList as $moduleSchedule)
        {
            $defaultCommand =   new $moduleSchedule($schedule);
            $defaultCommand ->osceScheduling();
        }
//        $schedule->command('inspire')
//                 ->hourly();

        //临时代码，用于执行发送信息
        $messageContro = \App::make('Modules\Osce\Http\Controllers\Admin\MessageController');
        $messageContro ->getSendMessage();
    }
}
