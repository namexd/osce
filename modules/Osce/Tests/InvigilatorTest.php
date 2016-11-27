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


class InvigilatorTest  extends TestCase
{
   public function testAddInvigilator(){
       $data = [
            'name'          => '测试老师'.rand(1000,9999),
            'gender'        => round(rand(0,1)) ? '男':'女',
            'code'          => rand(10000,99999),
            'idcard'        => '43052218901203'.rand(1000,9999),
            'mobile'        => '158'.rand(10000000, 99999999),
            'email'         => rand(1000,99999).'@163.com',
            'images_path'   => ['/images/head.png'],
       ];

       dump($data);
       $result =   $this->route('post', 'osce.admin.invigilator.postAddInvigilator','',$data);
//       $this->action('post','\Modules\Osce\Http\Controllers\Admin\InvigilatorController@postAddInvigilator','',$data);
       $this->assertRedirectedToRoute('osce.admin.invigilator.getInvigilatorList');
   }

}