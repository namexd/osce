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
use Modules\Osce\Entities\TestResult;
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
        return response()->json(
            $this->success_data($list,1,'验证完成')
        );
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
        //考试标准时间
        $mins = $station->mins;
        $exam =Exam::find($examId);
        $StandardModel  =   new Standard();
        $standardList   =   $StandardModel->ItmeList($station->subject_id);
        $temp=array();

        $data=array();

        //首先找pid为0的

        foreach($standardList as $v){

            if($v["pid"]==0){

                $temp[]=$v;

            }

        }

        while($temp){
            $now = array_pop($temp);
                //设置非顶级元素的level=父类的level+1
                foreach($data as $v){

                    if($v["id"]==$now["pid"]){

                        $now["level"]=$v["level"]+1;
                    }

                }
            //找直接子类

            foreach($standardList as $v){

                if($v["pid"]==$now["id"]){

                    $temp[]=$v;

                }

            }

            //移动到最终结果数组

            array_push($data,$now);

        }

        echo json_encode($data);
        return $data;

    }
    /**
     * 提交成绩详情
     * @method GET
     * @url /osce/admin/invigilatepad/save-exam-Result
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
            'subject_id' =>'required|integer',
            'standard_id' =>'required|integer',
            'score' =>'required',
        ],[
            'subject_id.required'=>'请检查考试项目',
            'standard_id.required'=>'请检查评分标准',
            'score.required'=>'请检查评分标准分值',
        ]);
        $data =[
            'subject_id'=>Input::get('subject_id'),
            'standard_id'=>Input::get('standard_id'),
            'score'=>Input::get('score'),
        ];
        $ResultModel = new TestResult();    //获取考试结果id
//        $data['exam_result_id'] = $ResultModel-> //获取考试结果方法
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
     * 提交评价
     * @method GET
     * @url /osce/admin/invigilatepad/see-exam-evaluate
     * @access public
     * @param Request $request get请求<br><br>
     * <b>get请求字段：</b>
     * * string     station_id    考站id   (必须的)
     *
     * @return view
     *
     * @version 1.0
     * @author zhouqiang <zhouqiang@misrobot.com>
     * @date
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */

      public  function getSeeExamEvaluate(Request $request){
           $this->validate($request,[
               'station_id'=>'required|integer',
               'student_id'=>'required|integer',
           ]);
          $stationId= Input::get('station_id');
          $studenId = Input::get('student_id');
//          $ExamScore =



      }


    /**
     *  查看现场视屏
     * @method GET
     * @url /osce/admin/invigilatepad/see-exam-evaluate
     * @access public
     * @param Request $request get请求<br><br>
     * <b>get请求字段：</b>
     * * string     station_id    考站id   (必须的)
     *
     * @return view
     *
     * @version 1.0
     * @author zhouqiang <zhouqiang@misrobot.com>
     * @date
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */


      public  function getLocale(Request $request){
            $this->validate($request,[
               'station_id'=>'required|integer'
            ]);

              




      }





}