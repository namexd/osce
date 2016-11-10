<?php
/**
 * Created by PhpStorm.
 * User: zhouchong
 * Date: 2016/1/20 0020
 * Time: 20:29
 */
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\App;
class PadTest extends TestCase{

//    public function testOne(){
//        $response =   $this->action('get','\Modules\Osce\Http\Controllers\Api\Pad\PadController@getWaitRoom','',['exam_id'=>67]);
//        $view=$response->getContent();
//        dd($view);
//vcr_id=1&begin_dt=2016-1-26 9:00:00&end_dt=2016-1-26 10:00:00&room=1&exam_id=1
//    }

      public function testTwo(){
       $response =   $this->action('get','\Modules\Osce\Http\Controllers\Api\IndexController@getExamList','',['exam_id'=>101]);
        $view=$response->getContent();
//        $response = $this->action('get','\Modules\Osce\Http\Controllers\Api\Pad\PadController@getStudentVcr','', ['exam_id'=>88,'room_id'=>11]);
//        $view=$response->getContent();
        dd($view);
      }
}