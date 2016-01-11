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
/*        $sender=App::make('messages.sms');
        dd($sender->send('13980757127','验证码：'.time().'【敏行医学】'));*/

        //userid
        $sender=App::make('messages.pm');
        //查询消息列表，数量
        /*
         *
         *    /**
     * 获取消息列表
     * @param $accept  接收人
     * @param null $sender 发送人
     * @param null $module 模块
     * @param int $status 状态
     * @param int $pageSize 分页条数
     * @param int $pageIndex 页码
     * @return mixed
     *
        public function messages($accept,$sender=null,$module=null,$status=1,$pageSize=10,$pageIndex=0);
         *
         *
         */
        dd($sender->messages(1));

        dd($sender->send(1,'验证码：'.time().'【敏行医学】'));

        //email
        $sender=App::make('messages.email');
        dd($sender->messages(1));
        dd($sender->send('111@13.com','验证码：'.time().'【敏行医学】'),'注册信息');

        //openid
        $sender=App::make('messages.wechat');
        dd($sender->send('fewq3ffw','验证码：'.time().'【敏行医学】'));


    }

}
