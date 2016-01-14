<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/1/13 0013
 * Time: 11:42
 */

namespace Modules\Osce\Http\Controllers\Admin;


use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Input;
use Modules\Osce\Entities\Exam;
use Modules\Osce\Entities\ExamScore;
use Modules\Osce\Entities\Standard;
use Modules\Osce\Entities\Station;
use Modules\Osce\Entities\Student;
use Modules\Osce\Http\Controllers\CommonController;

class InvigilatePadController extends CommonController
{

    /**
     * 身份验证
     * @method GET
     * @url /osce/admin/invigilatepad/authentication
     * @access public
     * @param Request $request get请求<br><br>
     * <b>get请求字段：</b>
     * * string     idcard        学生身份证号   (必须的)
     *
     * @return view
     *
     * @version 1.0
     * @author zhouqiang <zhouqiang@misrobot.com>
     * @date
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */

    public function  getAuthentication(Request $request)
    {
//        dd(222222222);
        $this->validate($request, [
            'watch_id' => 'required|integer'
        ], [
            'watch_id.required' => '请刷腕表'
        ]);
        $watch_id = (int)$request->input('watch_id');
        $studentModel = new  Student();
        $studentData = $studentModel->studentList($watch_id);
        $list = [];
        foreach ($studentData as $itme) {
            $list = [
                'name' => $itme->name,
                'code' => $itme->code,
                'idcard' => $itme->idcard,
                'mobile' => $itme->mobile
            ];
        }
        dd($list);
        return $list;
    }
    /**
     * 根据考站ID和考试ID获取科目信息(考核点、考核项、评分参考)
     * @method GET
     * @url /osce/admin/invigilatepad/exam-grade
     * @access public
     * @param Request $request get请求<br><br>
     * <b>get请求字段：</b>
     * * string     station_id    考站id   (必须的)
     * * string     exam_id       考试id   (必须的)
     *
     * @return view
     *
     * @version 1.0
     * @author zhouqiang <zhouqiang@misrobot.com>
     * @date
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */


    public function getExamGrade(Request $request,Collection $collection){
      $this->validate($request,[
            'station_id' =>'required|integer',
            //'exam_id'  => 'required|integer'
      ],[
         'station_id.required'=>'没有获取到当前考站',
         'exam_id.required'=>'没有获取到当前考试'
      ]);

        $stationId =$request->get('station_id');
        $examId = $request->get('exam_id');
        //根据考站id查询出下面所有的考试项目
        $station    =   Station::find($stationId);
        $exam =Exam::find($examId);
        $StandardModel  =   new Standard();
        $standardList   =   $StandardModel->ItmeList($station->subject_id);


        dd($standardList);

    }
    /**
     * 成绩详情保存
     * @method GET
     * @url /osce/admin/invigilatepad/Save-exam-Result
     * @access public
     * @param Request $request get请求<br><br>
     * <b>get请求字段：</b>
     * * string     station_id    考站id   (必须的)
     * * string     exam_id       考试id   (必须的)
     *
     * @return view
     *
     * @version 1.0
     * @author zhouqiang <zhouqiang@misrobot.com>
     * @date
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function  getSaveExamResult(Request $request){
        $this->validate($request,[
            'exam_result_id' =>'required|integer',
            'subject_id' =>'required|integer',
            'standard_id' =>'required|integer',
            'score' =>'required',
        ],[
            'exam_result_id.required'=>'请检查是否得到考生的信息',
            'subject_id.required'=>'请检查考试项目',
            'standard_id.required'=>'请检查评分标准',
            'score.required'=>'请检查评分标准分值',
        ]);
        $data =[
            'exam_result_id'=>Input::get('exam_result_id'),
            'subject_id'=>Input::get('subject_id'),
            'standard_id'=>Input::get('standard_id'),
            'score'=>Input::get('score'),
        ];
        $Save =ExamScore::create($data);
        if($Save){
            return response()->json(
                $this->success_data(1,'详情保存成功')
            );
        }else{
            return response()->json(
                $this->success_data(0,'详情保存失败')
            );
        }

    }

    /**
     * 成绩详情查看
     * @method GET
     * @url /osce/admin/invigilatepad/Save-exam-Result
     * @access public
     * @param Request $request get请求<br><br>
     * <b>get请求字段：</b>
     * * string     station_id    考站id   (必须的)
     * * string     exam_id       考试id   (必须的)
     *
     * @return view
     *
     * @version 1.0
     * @author zhouqiang <zhouqiang@misrobot.com>
     * @date
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */








}