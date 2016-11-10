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
       $data    =   [
            'name'=>'测试老师'.rand(1000,9999),
            'is_sp'=>rand(1,2),
       ];

       $respone =   $this->action('post','\Modules\Osce\Http\Controllers\Admin\InvigilatorController@postAddInvigilator','',$data);
       $this->assertRedirectedToRoute('osce.admin.invigilator.getInvigilatorList');
   }

}