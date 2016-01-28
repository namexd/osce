<?php
/**
 * Created by PhpStorm.
 * User: j5110
 * Date: 2016/1/20
 * Time: 15:28
 */


use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\App;

class DrawlotsTest extends TestCase
{
    private $user_id = 51;

//    public function testOne()
//    {
//        $userOb =   \App\Entities\User::find(1);
//        $response   =   $this->actingAs($userOb)
//            ->action('get','\Modules\Osce\Http\Controllers\Api\Pad\DrawlotsController@getExaminee');
//        $view = $response->getContent();
//        $data = json_decode($view);
////        $this->assertTrue($data);
//        dd($view);
//    }

    public function testThree()
    {
        $userOb =   \App\Entities\User::find(1);
        $response   =   $this->actingAs($userOb)
            ->action('get','\Modules\Osce\Http\Controllers\Api\Pad\DrawlotsController@getNextExaminee');
        $view = $response->getContent();
        $data = json_decode($view);
//        $this->assertTrue($data.code == 1);
        dd($view);
    }

//    public function testTwo()
//    {
//        $response   =   $this
//            ->action('get','\Modules\Osce\Http\Controllers\Api\Pad\DrawlotsController@getStation','',['uid'=>'88','room_id'=>1]);
//        $view = $response -> getContent();
//        dd($view);
//    }
}