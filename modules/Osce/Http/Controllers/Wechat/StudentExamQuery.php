<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/1/26 0026
 * Time: 10:45
 */

namespace Modules\Osce\Http\Controllers\Wechat;
use Illuminate\Http\Request;
use Modules\Osce\Http\Controllers\CommonController;

class StudentExamQuery extends  CommonController
{



    //url  /osce/wechat/student-exam-query/results-query-index
    public  function getResultsQueryIndex(Request $request){

        
        return view('osce::wechat.exammanage.exam_notice');

    }

}