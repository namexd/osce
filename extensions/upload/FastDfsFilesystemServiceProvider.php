<?php

namespace Extensions\UpLoad;

use League\Flysystem\Filesystem;
use Illuminate\Support\ServiceProvider;
use Storage;
use Extensions\UpLoad\FastDfsAdapter;

class FastDfsFilesystemServiceProvider extends ServiceProvider
{

    public function boot()
    {
        Storage::extend('fastdfs', function($app, $config)
        {
            return new FastDfsAdapter();
        });

    }

    public function register()
    {}
}