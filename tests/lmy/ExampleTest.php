<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\Jobs\SendReminderSms;
use App\Repositories\Common;


class ExampleTest extends TestCase
{
    /*   public function testSendSms(){
           //mobi
          $sender=App::make('messages.sms');
           dd($sender->send('13980757127','验证码：'.time().'【敏行医学】'));

           //userid
           //$sender=App::make('messages.pm');
           //查询消息列表，数量
           /*
            *
            *
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
          dd($sender->messages(1));

           dd($sender->send(1,'验证码：'.time().'【敏行医学】'));

           //email
           $sender=App::make('messages.email');
           dd($sender->messages(1));
           dd($sender->send('111@13.com','验证码：'.time().'【敏行医学】'),'注册信息');

           //openid
           $sender=App::make('messages.wechat');
           dd($sender->send('fewq3ffw','验证码：'.time().'【敏行医学】'));


    }*/

    public function testApi(){
        $response = $this->call('POST',
            'http://192.168.1.205/api/1.0/public/oauth/access_token',
        [
            'username'=>'13699456588',
            'password'=>'123456',
            'grant_type'=>'password',
            'client_id'=>'ios',
            'client_secret'=>'111'
        ],
            [],
            [],
            [],
            null);

        $result=json_decode($response->content());
        dd($result);
        $token=$result->access_token;
        //dd($token);


/*        $response = $this->call('GET',
            'http://192.168.1.205/api/1.0/private/osce/winapp/test',
            [
                'access_token'=>$token,
            ],
            [],
            [],
            [],
            null);

        dd($response);*/


        /**
         * code,user_id 必须
         *
         * 'code'          =>  $request->get('code'),
        'name'          =>  $request->get('name',''),
        'status'        =>  $request->get('status',1),
        'description'   =>  $request->get('description',''),
        'factory'       =>  $request->get('factory',''),
        'sp'            =>  $request->get('sp',''),
        'created_user_id'=> $request->get('user_id'),
         */
        $url='http://192.168.1.205/api/1.0/private/osce/winapp/add-watch';

        /**
         * 三个字段必须的
         *             'id'            =>  'required|integer',
        'status'        =>  'required|integer',
        'user_id'       =>  'required|integer'
         */
        $url='http://192.168.1.205/api/1.0/private/osce/winapp/update-watch';

        /**
         * 2个字段必须的
         *      * id int 设备id
         * user_id int 操作用户编号
         */
        $url='http://192.168.1.205/api/1.0/private/osce/winapp/delete-watch';

        $url='http://192.168.1.205/api/1.0/private/osce/winapp/watch-status';
        $url='http://192.168.1.205/api/1.0/private/osce/winapp/bound-watch';
        $url='http://192.168.1.205/api/1.0/private/osce/winapp/unwrap-watch';
        $url='http://192.168.1.205/api/1.0/private/osce/winapp/student-details';



        $result=$this->ajax($url,[
            'access_token'=>$token,
            'user_id'=>1,
            'code'=>'999'
        ]);
        dd($result);

    }

    protected function ajax($url,$parm){

        $kernel = $this->app->make('Illuminate\Contracts\Http\Kernel');

        $request = Request::create(
            $url, 'GET', $parm,
            [], [], [], null
        );

        $request->headers->add([
            'X-Requested-With'=>'XMLHttpRequest'
        ]);


        return $kernel->handle($request);

    }

}
