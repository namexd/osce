<?php

namespace  App\Repositories\Message;

use Illuminate\Support\ServiceProvider;

class MessageServiceProvider extends ServiceProvider
{
    /**
     * 延迟加载
     *
     * @var boolean
     */
    //protected $defer = true;

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
        $this->registerManager();

        $this->app->singleton('messages.sms', function () {
            return $this->app['messages']->message('sms');
        });

        $this->app->singleton('messages.pm', function () {
            return $this->app['messages']->message('pm');
        });

        $this->app->singleton('messages.wechat', function () {
            return $this->app['messages']->message('wechat');
        });

        $this->app->singleton('messages.email', function () {
            return $this->app['messages']->message('email');
        });
    }

    protected function registerManager()
    {
        $this->app->singleton('messages', function () {
            return new MessageManager($this->app);
        });
    }

    public function provides()
    {
        return array([
            'messages',
            'messages.sms',
            'messages.pm',
            'messages.wechat',
            'messages.email',
        ]);
    }
}
