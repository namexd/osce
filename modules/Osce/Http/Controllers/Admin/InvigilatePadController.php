<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/1/13 0013
 * Time: 11:42
 */

namespace Modules\Osce\Http\Controllers\Admin;


use Illuminate\Http\Request;
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


    public function getExamGrade(Request $request){
      $this->validate($request,[
            'station_id' =>'required|integer',
            //'exam_id'  => 'required|integer'
      ]);
        $stationId =$request->get('station_id');
        $examId = $request->get('exam_id');
        //根据考站id查询出下面所有的考试项目
        $station    =   Station::find($stationId);
        //$stationModel = new Station();
        //$items = $stationModel->ItmeList($stationId);
        $StandardModel  =   new Standard();

        $standardList   =   $StandardModel->ItmeList($station->subject_id);
        dd($standardList);

    }


    

}