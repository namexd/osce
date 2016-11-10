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


class ResourceTest  extends TestCase
{
    public function testAddResoucre(){
        $data=[
            'repeat_max'=>0,
            'name'=>'测试'.rand(0,9).rand(0,9).rand(0,9).rand(0,9),
            'cate_id'=>2,
            'manager_id'=>45,
            'manager_name'=>'测试教师6941',
            'manager_mobile'=>'13699450370',
            'location'=>'测试地址新八角',
            'detail'=>'测试地址描述',
            'code'=>[
                '123456789',
                '123456790',
                '123456791',
                '123456792',
            ],
            'resources_type'=>'TOOLS',
            'images_path'=>[
                '123.jpg'
            ]
        ];
        $response=$this->action('post','\Modules\Msc\Http\Controllers\WeChat\ResourcesManagerController@postAddResources','',$data);
        $this->assertRedirectedToAction('\Modules\Msc\Http\Controllers\WeChat\ResourceController@getResourceAdd');
    }
}