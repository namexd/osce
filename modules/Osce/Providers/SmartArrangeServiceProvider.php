<?php

namespace Modules\Osce\Providers;

use Illuminate\Support\ServiceProvider;
use Modules\Osce\Entities\SmartArrange\SmartArrange;
use Modules\Osce\Entities\SmartArrange\SmartArrangeForHuaxiRepository;
use Modules\Osce\Entities\SmartArrange\Student\StudentFromDB;

class SmartArrangeServiceProvider extends ServiceProvider
{
    /**
     * 服务提供者加是否延迟加载.
     *
     * @var bool
     */
    protected $defer = true;

    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        //binding接口以便于依赖注入
        \App::bind('student', function () {
            return new StudentFromDB();
        });

        \App::bind('SmartArrange', function () {
            return new SmartArrange(\App::make('student'));
        });

        //使用singleton绑定单例
        $this->app->bind('Modules\Osce\Entities\SmartArrange\SmartArrangeForHuaxiRepository', function () {
            return new SmartArrangeForHuaxiRepository(\App::make('SmartArrange'));
        });
    }

    public function provides()
    {
        return ['Modules\Osce\Entities\SmartArrange\SmartArrangeForHuaxiRepository'];
    }
}
