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


class VcrTest  extends TestCase
{
   public function testAddVcr(){
       $cate_id =   1;
       $data    =   [
           'cate_id'        =>  $cate_id,
           'name'           =>  '测试'.rand(1000,9999),
           'code'           =>  rand(1000,9999),
           'ip'             =>  '192.168.1.200',
           'username'       =>  'admin',
           'password'       =>  'gogomisrobot',
           'port'           =>  '9090',
           'channel'        =>  '30',
           'description'    =>  '测试'.rand(1000,9999),
       ];
       $respone =   $this->action('post','\Modules\Osce\Http\Controllers\Admin\MachineController@postAddMachine','',$data);
       $this    ->  assertRedirectedToRoute('osce.admin.machine.getMachineList',['cate_id'=>$cate_id]);
   }
}