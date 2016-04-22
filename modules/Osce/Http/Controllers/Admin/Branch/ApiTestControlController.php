<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/3/10 0010
 * Time: 14:05
 */

namespace Modules\Osce\Http\Controllers\Admin\Branch;

use Illuminate\Support\Facades\Redis;
use Mockery\CountValidator\Exception;
use Modules\Osce\Entities\AutomaticPlanArrangement\Student;
use Modules\Osce\Entities\ExamScreeningStudent;
use Modules\Osce\Entities\QuestionBankEntities\ExamControl;
use Modules\Osce\Entities\Watch;
use Modules\Osce\Http\Controllers\Admin\InvigilatorController;
use Modules\Osce\Http\Controllers\Api\InvigilatePadController;
use Modules\Osce\Http\Controllers\Api\Pad\PadController;
use Modules\Osce\Http\Controllers\Api\StudentWatchController;
use Modules\Osce\Http\Controllers\CommonController;
use Illuminate\Http\Request;
use Modules\Osce\Http\Controllers\Admin\Branch\ApiController;
use Symfony\Component\HttpFoundation\Response;


/**考试监控
 * Class ExamControlController
 * @package Modules\Osce\Http\Controllers\Admin\Branch
 */

class ApiTestControlController extends CommonController
{
    public function apitest(Request $request)
    {
      /* //测试替考接口
        $request['mode'] = 2;
        $request['exam_id'] = 4;
        $request['student_id'] = 222;
        $request['exam_screening_id'] = 143;
        $apiController = new ApiController();
        $result = $apiController->postAlertExamReplace($request);
        dd($result);*/


/*        //测试开始考试接口
        $request['student_id'] = 7319;
        $request['station_id'] =91 ;
        $InvigilatePadController = new InvigilatePadController();
        $result = $InvigilatePadController->getStartExam($request);
        dd($result);
        */


       /* //获取当前考站评分表接口（考核项考核点）
        $request['station_id'] =91 ;
        $InvigilatePadController = new InvigilatePadController();
        $result = $InvigilatePadController->getExamGrade($request);
        dd($result);*/


/*        //结束考试请求接口

        $PadController = new PadController();
        $request['student_id'] = 7319;
        $request['station_id'] = 91;
        $request['user_id'] = 964;
        $result = $PadController->getChangeStatus($request);
        dd($result);*/

/*
        //保存成绩接口
        $request['score'] =20 ;
        $request['student_id'] = 7319;
        $request['station_id'] =91 ;
        $request['exam_screening_id'] =561 ;
        $request['teacher_id'] =964 ;
        $InvigilatePadController = new InvigilatePadController();
        $result = $InvigilatePadController->postSaveExamResult($request);
        dd($result);*/

 /*       //获取当前考站评分表（考核项考核点）接口
        $request['station_id'] = 20;
        $InvigilatePadController = new InvigilatePadController();
        $result = $InvigilatePadController->getExamGrade($request);
        dd($result);*/



    }

































}