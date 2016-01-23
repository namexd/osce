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
use Modules\Osce\Entities\ExamResult;
use Modules\Osce\Entities\ExamStation;
use Modules\Osce\Entities\Student;
use Modules\Osce\Entities\Exam;
use Modules\Osce\Http\Controllers\CommonController;
use Auth;

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
        try{
            $user= Auth::user();
            if(empty($user)){
                throw new \Exception('当前用户未登陆');
            }
            //根据用户获得考试id
            $ExamIdList= Student::where('user_id','=',$user->id)->select('exam_id')->get();
            $list=[];
            foreach($ExamIdList as $key=>$data){
                $list[$key]=[
                      'exam_id'=>$data->exam_id,
                ];
            }
            $examIds = array_column($list, 'exam_id');
            $ExamModel = new Exam();
            $ExamList= $ExamModel->Examname($examIds);
            //根据考试id获取所有考试
            return view('osce::wechat.resultquery.examination_list',['ExamList'=>$ExamList]);
        }catch (\Exception $ex) {
            throw $ex;
        }
    }

//      ajax  /osce/wechat/student-exam-query/every-exam-list
    public function getEveryExamList(Request $request){

        $this->validate($request,[
            'exam_id'=>'required|integer'
        ]);
        $examId =Input::get('exam_id');
        //根据考试id查询出所有考站
        $stationId= ExamStation::where('exam_id','=',$examId)->select('station_id')->get();
        $list=[];
        foreach($stationId as $data){
            $list[]=[
                'id'=>$data->station_id,
            ];
        }
        $stationIds = array_column($list, 'id');
        //根据考站id查询出考站的相关信息
        $ExamResultModel= new ExamResult();
        $stationList =$ExamResultModel->stationInfo($stationIds);
        dd($stationList);
        if($stationList){
            return response()->json(
                $this->success_data('',0,'查询失败')
            );

        }

    }



    //考生成绩查询详情页根据考站id查询



}