<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/1/22 0022
 * Time: 15:37
 * @author zhouqiang
 */
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\App;
class InvigilatePadTest extends TestCase
{
  public function  testInvigilatePad(){
      $standard_id =   1;
      $subject_id =2;
      $data    =   [
          'standard_id'           =>$standard_id,
          'subject_id'        =>  $subject_id,
          'score'           =>  rand(1000,9999) ,
          'evaluate'             =>  '测试评分内容'.rand(1000,9999),
      ];
      $respone =   $this->action('post','\Modules\Osce\Http\Controllers\api\InvigilatePadController@postSaveExamEvaluate','',$data);
      $this    ->  assertRedirectedToRoute('osce.api.invigilatepad.postSaveExamEvaluate');
  }
    public function testSaveExamResult(){

        $student_id = 1;
        $station_id =5;
        $exam_screening_id=65;
        $teacher_id    =  51;
        $Nowtime =date('Y-m-d H:i:s',time());

        $data    =   [
            'evaluate'=>   '评价内容',//评价内容
            'operation'=>   rand(1,5),//操作的连贯性
            'skilled'=>     rand(1,5),//工作的娴熟度
            'patient'=>     rand(1,5),//病人关怀情况
            'affinity'=>    rand(1,5),//沟通亲和能力
            'student_id'=>$student_id,
            'station_id'=>$station_id,
            'exam_screening_id'=>$exam_screening_id,
            'begin_dt'=>    $Nowtime,
            'end_dt'=>    $Nowtime,
            'time'    =>        rand(1000,9999),
            'score'=>             rand(1,150),
            'score_dt'=>      $Nowtime,
            'teacher_id'=>    $teacher_id,
        ];

       $this->action('post','\Modules\Osce\Http\Controllers\Api\InvigilatePadController@postSaveExamResult','',$data);

        $this    ->  assertRedirectedToRoute('osce.api.invigilatepad.postSaveExamResult');

    }



}
