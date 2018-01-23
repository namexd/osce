<?php

namespace App\Providers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\ServiceProvider;
use Modules\Osce\Entities\ExamScreeningStudent;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(Request $request)
    {
        //
        \Log::info('xxx路由地址', ['url' => $request->url()]);
        \Log::info('xxx路由参数', $request->all());
        \Log::info('xxx访问ip为', ['ip' => $request->ip()]);
//        app('db')->listen(function($query, $bindings = null, $time = null, $connectionName = null) {
//            \Log::info('sql command', ['sql' => $query]);
//        });
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
