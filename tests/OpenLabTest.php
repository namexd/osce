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


class OpenLabTest  extends TestCase
{
    //获取随机学生
    private function getRandStudent(){
        $list   =   \Modules\Msc\Entities\Student::where('id','>',48)->get();
        return $this->getRandItem($list);
    }
    //获取随机教师
    private function getRandTeacher(){
        $list   =   \Modules\Msc\Entities\Teacher::where('id','>',48)->get();
        return $this->getRandItem($list);
    }

    public function getRandOpenDevice(){
        $list   =   \Modules\Msc\Entities\ResourcesDevice::get();
        return $this->getRandItem($list);
    }
    //测试 学生新增 开放实验室 预约
    public function testStudentAddApply(){
        $user   =   $this->getRandStudent();
        //$openDevice     =   $this->getRandOpenDevice();
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
        $userOb     =   \App\Entities\User::find($user->id);
        $response   =   $this   ->withSession(['openid'=>$userOb->openid])
            ->actingAs($userOb)
            ->action('get','\Modules\Msc\Http\Controllers\WeChat\OpenLaboratoryController@getLaboratoryData','',['dateTime'=>$date]);
        $view   = $response->getContent();
        $json   =   json_decode($view);
		dd($json);
        $timeList=$json    ->  data    ->rows   ->  ClassroomApplyList  ->data;
        $timeGet   =   $this->  getRandItem($timeList);

        $resources_lab_id   =   $timeGet    ->  resources_lab_id;

        $resources_lab_calendae   =   $timeGet->resources_open_lab_apply;
        $timeRand   =   $this-> getRandItem($resources_lab_calendae);

        $resources_lab_calendar_id  =   $timeRand-> resources_lab_calendar_id;
        $course_id  =   $timeRand   ->  course_id;
        $data   =   [
            'p_id' => $resources_lab_calendar_id,
            'c_id'          => $resources_lab_id,
            'apply_date'                => $date,
            'apply_type'                => 1,
            'course_name'                 => $course_id,
            'detail'                    =>  '测试脚本添加'.rand(10000,99999),
            'apply_uid'                 => $user->id,
            'user_type'                 => 1,
        ];

        $userOb     =   \App\Entities\User::find($user->id);
        $response   =   $this   ->withSession(['openid'=>$userOb->openid])
                                ->actingAs($userOb)
                                ->action('post','\Modules\Msc\Http\Controllers\WeChat\OpenLaboratoryController@postAddLab','',$data);
        $this->assertRedirectedTo('/msc/wechat/open-laboratory/type-list');
    }
    public function testTeacherAddApply(){
        $user   =   $this->getRandTeacher();
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
        $userOb     =   \App\Entities\User::find($user->id);
        $response   =   $this   ->withSession(['openid'=>$userOb->openid])
            ->actingAs($userOb)
            ->action('get','\Modules\Msc\Http\Controllers\WeChat\OpenLaboratoryController@getLaboratoryData','',['dateTime'=>$date]);
        $view   = $response->getContent();
        $json   =   json_decode($view);
        $timeList=$json    ->  data    ->rows->ClassroomApplyList  ->data;
        $timeGet   =   $this->getRandItem($timeList);
        $resources_lab_id   =   $timeGet->resources_lab_id;
        $resources_lab_calendae   =   $timeGet->resources_open_lab_apply;
        $timeRand   =   $this->getRandItem($resources_lab_calendae);
        $resources_lab_calendar_id  =   $timeRand-> resources_lab_calendar_id;
        $course_id  =   $timeRand   ->  course_id;
        $data   =   [
            'p_id' => $resources_lab_calendar_id,
            'c_id'          => $resources_lab_id,
            'apply_date'                => $date,
            'apply_type'                => 1,
            'course_name'                 => $course_id,
            'detail'                    =>  '测试脚本添加'.rand(10000,99999),
            'apply_uid'                 => $user->id,
            'user_type'                 => 2,
        ];

        $userOb     =   \App\Entities\User::find($user->id);
        $response   =   $this   ->withSession(['openid'=>$userOb->openid])
            ->actingAs($userOb)
            ->action('post','\Modules\Msc\Http\Controllers\WeChat\OpenLaboratoryController@postAddLab','',$data);
        $this->assertRedirectedTo('/msc/wechat/open-laboratory/type-list');
    }
}