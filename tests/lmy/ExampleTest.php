<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\Jobs\SendReminderSms;
use App\Repositories\Common;



class Std{

    //姓名
    public $name='';

    //编号（顺序号）
    public $order=0;

}

class Station{

    //病例
    public $case='';

    //考试时长
    public $time=5;

    //准备时长
    public $ready=5;
}

class Room{

    public $name='';

    public $stations=[];
}

class ExamQueue{

    protected $q=[];

    public $name='';

    public function add($order,$std,$station){
        $this->q=array_add($this->q,$order,[
            'std'=>$std,
            'station'=>$station
        ]);
    }

}



class ExampleTest extends TestCase
{

    public function testQueue(){




        $s1=new Station();
        $s1->case='病1';
        $s1->time=3;
        $s1->ready=5;

        $s2=new Station();
        $s2->case='病2';
        $s2->time=5;
        $s2->ready=5;

        $s3=new Station();
        $s3->case='病3';
        $s3->time=10;
        $s3->ready=5;

        $s4=new Station();
        $s4->case='病1';
        $s4->time=3;
        $s4->ready=5;


        $r1=new Room();
        $r1->name='601';
        $r1->stations=[$s1,$s4];

        $r2=new Room();
        $r2->name='602';
        $r2->stations=[$s2];

        $r3=new Room();
        $r3->name='603';
        $r3->stations=[$s3];

        $plan=[
            1=>$r1,
            2=>$r2,
            3=>$r3,
        ];

        $std1=new Std();
        $std1->name='A';
        $std1->order=2;

        $std2=new Std();
        $std2->name='B';
        $std2->order=4;

        $std3=new Std();
        $std3->name='C';
        $std3->order=5;

        $std4=new Std();
        $std4->name='D';
        $std4->order=1;

        $std5=new Std();
        $std5->name='E';
        $std5->order=6;

        //排序后的考生
        $students=[$std4,$std1,$std2,$std3,$std5];



        //按考站排序

        //1、获取所有考站
        $stations=[$s1,$s4,$s2,$s3,];

        //学生总数量 5人
        $students_total=count($students);

        //考站总数量 4个
        $stations_total=count($stations);

        //忽略时长
        //每次参加考试的人数量=考站数量=4
        for($i=0,$j=1;$i<$students_total-1;$i+=4,$j++){

            //当前批次的学生
            $current_std=[];
            for($index=$i,$_index=0;$index<4;$index++,$_index++){
                $current_std[$_index]=$students[$index];
            }

            //dd($current_std);


            //1个考站1个队列
            $result=[];
            for($_sta=0;$_sta<4;$_sta++){ //4个考站

                $q=new ExamQueue();
                for($_std=0;$_std<4;$_std++){
                    $q->add($_std,$current_std[($_sta+$_std)%4],$stations[$_sta]);
                }


                $result[$_sta]=$q;
            }

            Log::info($result);
            dd($result);




        }


        $queue=new ExamQueue();



    }




















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
/*        $response = $this->call('POST',
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
        $token=$result->access_token;*/
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
 /*       $url='http://192.168.1.205/api/1.0/private/osce/winapp/delete-watch';

        $url='http://192.168.1.205/api/1.0/private/osce/winapp/watch-status';
        $url='http://192.168.1.205/api/1.0/private/osce/winapp/bound-watch';
        $url='http://192.168.1.205/api/1.0/private/osce/winapp/unwrap-watch';
        $url='http://192.168.1.205/api/1.0/private/osce/winapp/student-details';*/



//        $result=$this->ajax($url,[
//            'access_token'=>$token,
//            'user_id'=>1,
//            'code'=>'999'
//        ]);
//        dd($result);

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
