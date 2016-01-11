<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\Jobs\SendReminderSms;
use App\Repositories\Common;


class ExampleTest extends TestCase
{
    public function testSendSms(){
        //mobile
        $sender=App::make('messages.sms');
        dd($sender->send('13699459159','验证码：'.time().'【敏行医学】'));

        //userid
        $sender=App::make('messages.pm');
        dd($sender->send('1111','验证码：'.time().'【敏行医学】'));

        //email
        $sender=App::make('messages.email');
        dd($sender->send('111@13.com','验证码：'.time().'【敏行医学】'),'注册信息');

        //openid
        $sender=App::make('messages.wechat');
        dd($sender->send('fewq3ffw','验证码：'.time().'【敏行医学】'));


    }

}
