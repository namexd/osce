<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\Jobs\SendReminderSms;
use App\Entities\Sys\User;
use App\Repositories\Common;

class ExampleTest extends TestCase
{

    public function testSendSms(){

        echo 'test......';

        Common::sendSms('13980757127','注册验证码:'.time());
    }

}
