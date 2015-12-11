<?php

/**
 * Created by PhpStorm.
 * User: dell
 * Date: 2015/11/4
 * Time: 17:19
 */
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\App;


class OpenDeviceTest  extends TestCase
{
    //获取随机学生
    private function getRandStudent(){
        $list   =   \Modules\Msc\Entities\Student::where('id','>',48)->get();
        return $this->getRandItem($list);
    }
    protected function getRandItem($list){
        $index  =   rand(0,count($list)-1);
        foreach($list as $key=>$item){
            if($index==$key)
            {
                return $item;
            }
        }
    }
    public function getRandOpenDevice(){
        $list   =   \Modules\Msc\Entities\ResourcesDevice::get();
        return $this->getRandItem($list);
    }
    //测试新增 开放设备 预约申请
    public function testAddApply(){
        $user   =   $this->getRandStudent();
        $openDevice     =   $this->getRandOpenDevice();
        $timeConfig     =   [
            '08:00-09:00',
            '10:00-11:00',
            '12:00-13:00',
            '14:00-17:00',
            '17:00-18:00',
        ];
        $time   =   $this->getRandItem($timeConfig);
        $dateConfig   =   [
            date('Y-m-d'),
            date('Y-m-d',time()+3600*24),
            date('Y-m-d',time()+3600*24*2),
        ];
        $date   =   $this->getRandItem($dateConfig);

        $data   =   [
            'uid'      =>   $user->id,
            'date'     =>   $date,
            'timeSec'  =>   $time,
            'detail'   =>   '测试脚本添加申请'.rand(10000,99999),
            'deviceId' =>   $openDevice->id
        ];
        $userOb     =   \App\Entities\User::find($user->id);
        $response   =   $this   ->withSession(['openid'=>$userOb->openid])
                                ->actingAs($userOb)
                                ->action('post','\Modules\Msc\Http\Controllers\WeChat\OpenDeviceController@postOpenToolsApply','',$data);
        $view = $response->getContent();
        $this->assertTrue($view);
    }
}