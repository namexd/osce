<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/1/26 0026
 * Time: 10:45
 */

namespace Modules\Osce\Http\Controllers\Wechat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Modules\Osce\Entities\Exam;
use Modules\Osce\Http\Controllers\CommonController;

class StudentExamQuery extends  CommonController
{
    /**
     * 获取考试 ，还回数据个页面
     * @method GET
     * @url /osce/wechat/student-exam-query/results-query-index
     * @access public
     * @param Request $request get请求<br><br>
     * <b>get请求字段：</b>
     * @return view
     * @version 1.0
     * @author zhouqiang <zhouqiang@misrobot.com>
     * @date
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */

    public  function getResultsQueryIndex(Request $request){
        $ExamModel = new Exam();
        $ExamList= $ExamModel->select()->get();
        //dd($ExamList);
        return view('osce::wechat.resultquery.examination_detail');

    }

//      ajax
    public function getEveryExamList(Request $request){

        $this->validate($request,[
            'exam_id'=>'required|integer'
        ]);
        $examId =Input::get('exam_id');
        $ExamModel = new Exam();
        $ExamStationList= $ExamModel->ExamStations($examId);
        if($ExamStationList){
            return response()->json(
                $this->success_data('',0,'查询失败')
            );

        }

    }


}