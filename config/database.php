<?php

return [

    /*
    |--------------------------------------------------------------------------
    | PDO Fetch Style
    |--------------------------------------------------------------------------
    |
    | By default, database results will be returned as instances of the PHP
    | stdClass object; however, you may desire to retrieve records in an
    | array format for simplicity. Here you can tweak the fetch style.
    |
    */

    'fetch' => PDO::FETCH_CLASS,

    /*
    |--------------------------------------------------------------------------
    | Default Database Connection Name
    |--------------------------------------------------------------------------
    |
    | Here you may specify which of the database connections below you wish
    | to use as your default connection for all database work. Of course
    | you may use many connections at once using the Database library.
    |
    */

    'default' => env('DB_CONNECTION', 'sys_mis'),

    /*
    |--------------------------------------------------------------------------
    | Database Connections
    |--------------------------------------------------------------------------
    |
    | Here are each of the database connections setup for your application.
    | Of course, examples of configuring each database platform that is
    | supported by Laravel is shown below to make development simple.
    |
    |
    | All database work in Laravel is done through the PHP PDO facilities
    | so make sure you have the driver for your particular database of
    | choice installed on your machine before you begin development.
    |
    */

    'connections' => [

        'sqlite' => [
            'driver'   => 'sqlite',
            'database' => database_path('database.sqlite'),
            'prefix'   => '',
        ],

        'msc_mis' => [
            'driver'    => 'mysql',
			'host'      => env('DB_HOST_MSC', 'localhost'),
            'database'  => env('DB_DATABASE_MSC', 'msc_mis'),
            'username'  => env('DB_USERNAME_MSC', 'limingyao'),
            'password'  => env('DB_PASSWORD_MSC', 'limingyao123'),
            'charset'   => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'prefix'    => '',
            'strict'    => false,
        ],
        'sys_mis' => [
            'driver'    => 'mysql',
            'host'      => env('DB_HOST_SYS', '139.196.49.64'),
            'database'  => env('DB_DATABASE_SYS', 'wj_dev_sys_mis'),
			'username'  => env('DB_USERNAME_SYS', 'myy'),
			'password'  => env('DB_PASSWORD_SYS', '123456'),
            'charset'   => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'prefix'    => '',
            'strict'    => false,
        ],
        'osce_mis' => [
            'driver'    => 'mysql',
            'host'      => env('DB_HOST_OSCE', '139.196.49.64'),
            'database'  => env('DB_DATABASE_OSCE', 'wj_dev_osce_mis'),
            'username'  => env('DB_USERNAME_OSCE', 'myy'),
            'password'  => env('DB_PASSWORD_OSCE', '123456'),
            'charset'   => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'prefix'    => '',
            'strict'    => false,
        ],
        'pgsql' => [
            'driver'   => 'pgsql',
            'host'     => env('DB_HOST', 'localhost'),
            'database' => env('DB_DATABASE', 'forge'),
            'username' => env('DB_USERNAME', 'forge'),
            'password' => env('DB_PASSWORD', ''),
            'charset'  => 'utf8',
            'prefix'   => '',
            'schema'   => 'public',
        ],

        'sqlsrv' => [
            'driver'   => 'sqlsrv',
            'host'     => env('DB_HOST', 'localhost'),
            'database' => env('DB_DATABASE', 'forge'),
            'username' => env('DB_USERNAME', 'forge'),
            'password' => env('DB_PASSWORD', ''),
            'charset'  => 'utf8',
            'prefix'   => '',
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Migration Repository Table
    |--------------------------------------------------------------------------
    |
    | This table keeps track of all the migrations that have already run for
    | your application. Using this information, we can determine which of
    | the migrations on disk haven't actually been run in the database.
    |
    */

    'migrations' => 'migrations',

    /*
    |--------------------------------------------------------------------------
    | Redis Databases
    |--------------------------------------------------------------------------
    |
    | Redis is an open source, fast, and advanced key-value store that also
    | provides a richer set of commands than a typical key-value systems
    | such as APC or Memcached. Laravel makes it easy to dig right in.
    |
    */

    'redis' => [

        'cluster' => false,

        //队列
        'queue' => [
			'host'     => env('REDIS_HOST','192.168.0.254'),
            'port'     => 6379,
            'database' => 0,
        ],

        'cache' => [
			'host'     => '192.168.0.254',
            'port'     => 6379,
            'database' => 1,
        ],

        'session' => [
			'host'     => '192.168.0.254',
            'port'     => 6379,
            'database' => 2,
        ],
        'message' => [
            'host' => 'cloud.misrobot.com',
            'port' => 6379,
            'database' => 3,
            'password' => 'gogoMisrobot123'
        ]
    ],

];
