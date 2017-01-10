<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\Jobs\SendReminderSms;
use App\Repositories\Common;


//学生
class S{

    //姓名
    public $name='';

    // 编号（学号）
    public $code='';

    //考生状态 1=考试中  0=空闲  2=考试已安排（准备中）
    protected $status=0;

    //考试进度
    protected $exam_progress=[];

    function __construct($name,$code){
        $this->code=$code;
        $this->name=$name;
    }

    //获取学生状态
    function getStatus(){
        return $this->status;
    }

    //设置学生状态
    function setStatus($status){
        return $this->status=$status;
    }

    //获取学生考试进度
    function getProgress(){
        return $this->exam_progress;
    }

    //设置学生考试初始科目
    function setIniProgress($progress){
        $this->exam_progress=$progress;
    }

    //设置学生考试开始
    function setExamStart($sequence){


        $this->exam_progress[$sequence]['station']='';
        $this->exam_progress[$sequence]['status']='';

        //更新考生考试进度
    }

    //设置学生考试结束
    function setExamEnd($sequence){
        //更新考生考试进度
    }
}

//房间1
class R1{

    //编号
    public $code='';

    //考站组合
    public $ts=[];

    //初始化房间
    function __construct(array $t){

        $t1=new T('A',20);
        $t2=new T('B',20);
        $t3=new T('C',5);
        $t4=new T('D',10);

        //t1，t2为选考
        $ts=[[$t1,$t2],$t3,$t4];
    }
}

//房间2
class R{

    //编号
    public $code='';

    //考站组合
    public $ts=[];

    //添加考站
    function addT(T $t){
        array_add($this->ts,$t->code,$t);
        return $this;
    }


}

//考站
class T{

    //编号
    public $code='';

    //考站序号，必须为大于等于0的整数，所有的考站序号是连续的，且必须从0开始
    public $sequence=0;

    //病例
    public $case='';

    //考试时长
    public $time=5;

    //准备时长
    public $ready=5;

    //正在考试的考生
    public $currentUser         =   null;
    //当前考生开始考试时间
    public $currentBeginTime    =   null;
    //当前考生结束考试时间
    public $currentEndTime      =   null;

    //考站状态  1=考试中  0=空闲  2=考试已安排（准备中）
    protected $status=0;

    //初始化考站
    function __construct($code,$time){
        $this->code=$code;
        $this->time=$time;
    }

    //准备考试，发通知告诉考生
    //进入 准备工作 倒计时
    function prepareTest($user){
        $this->currentUser=$user;
        $this->status=2;
    }

    //开始考试
    //进入 考试 倒计时
    //1个考站一个队列
    function beginTest($user,$sequence){
        $this->currentUser=$user;
        $user->setStatus(1);
        $user->setExamStart($sequence);
        $this->currentBeginTime=time();
        $this->currentEndTime=$this->currentBeginTime+$this->time;
        $this->status=1;

        //触发考试结束事件（结束时间，考试人）
        $this->endTest($user,$sequence);
    }

    //考试结束
    //关闭考试，设置考站为空闲
    function endTest($user,$sequence){

        $user->setStatus(0);
        $user->setExamEnd($sequence);

        $this->currentUser=null;
        $this->currentBeginTime=null;
        $this->currentEndTime=null;
        $this->status=0;
    }

    //获取当前考站状态
    function getStatus(){
        return $this->status;
    }
}



class Exam{

    //考试学生数量
    protected $_S_Count=0;

    //考站数量
    protected $_T_Count=0;

    //考站数组
    protected $_T=[];

    //考生数组
    protected $_S=[];

    //考试顺序规则
    protected $_TS=[];

    //已考过1科目或以上的学生，优先从这个队列获取数据
    protected $_S_ING=[];

    //考站队列
    protected $_Q_STA=[];


    protected $ExamBeginTime='2015-01-01 08:00';

    function __construct(){

        $this->_S_Count=100;
        $this->_T_Count=4;
        //考试最短时长，
        $this->_Min_Time=5;

        //初始化考站信息
        $t1=new T('A',20);  //11020800,11020820,11020840
        $t2=new T('B',20);  //11020800,11020820,11020840
        $t3=new T('C',5);   //11020800,11020805,11020810
        $t4=new T('D',10);  //11020800,11020810,11020820
        $this->_T=[$t1,$t2,$t3,$t4];


        //初始化考站顺序信息
        $this->_TS=[
            1=>[$t1,$t2],
            2=>$t3,
            3=>$t4
        ];

        //初始化待考学生信息
        for($i=0;$i<99;$i++){

            $std=new S('Std_'.$i,str_pad($i,5,0));
            $ini_std_sta=[];

            for($j=0;$j<count($this->_T);$j++){
                $ini_std_sta[$j]=[
                    'station'   =>  0, //考站编号
                    'status'    =>  0   ]; //考试状态 0=未考、1=已考
            }

            $std->setIniProgress($ini_std_sta);  //设置学生的考试科目
            $this->_S[$i]=$std;  //将考生信息放入考生的数组
        }

        //一个考站一个队列，队列初始化
        foreach($this->_T as $t){
            $this->_Q_STA[$t->code]=[];
        }

    }

    //智能排考
    function testQ(){

        //已考>0科目学生（正在考试学生）
        //$_Q_STD_TEST=[];


        //转换为unix时间戳
        $timer=strtotime($this->ExamBeginTime);

        //考试位置不重复 a(考站)
        //考试时间不重复 t（11021000-11021030）

        //初始化考站队列，开始考试
        foreach($this->_Q_STA as $v){

            $s=array_pop($this->_S);

            //加入正在考试队列
            array_push($this->_S_ING,$s->code);

            //加入考站队列
            array_push($this->_Q_STA[$v->code],
                [
                    'stdCode'   =>  $s->code,
                    'beginDt'   =>  $timer,
                    'endDt'     =>  $timer+($v->time*60),
                ]);
        }



        while($this->_S && $this->_S_ING){ // 没有待考试的学生结束

            //1分钟为计算粒度
            $timer=$timer+60;

            //考试时间结束处理
            foreach($this->_Q_STA as $sta){//遍历考站队列

                $t1=strtotime($sta['endDt']);
                $t2=strtotime($timer);

                if(($t1-$t2)>0){//考试时间到,更新队列学生状态
                    $std_code=$sta['stdCode'];
                }
            }
        }
    }
}



class ExampleTest extends TestCase
{

    //单站（多站选考扩展）
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
            1=>$r1,  //s1,s2，2选1考试
            2=>$r2,     //s3
            3=>$r3,     //s4
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






    }




















    /*   public function testSendSms(){
           //mobi
          $sender=App::make('messages.sms');
           dd($sender->send('13980757127','验证码：'.time().'【<!--速立达-->医学】'));

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

           dd($sender->send(1,'验证码：'.time().'【<!--速立达-->医学】'));

           //email
           $sender=App::make('messages.email');
           dd($sender->messages(1));
           dd($sender->send('111@13.com','验证码：'.time().'【<!--速立达-->医学】'),'注册信息');

           //openid
           $sender=App::make('messages.wechat');
           dd($sender->send('fewq3ffw','验证码：'.time().'【<!--速立达-->医学】'));


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
