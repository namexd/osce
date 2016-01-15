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
use Modules\Osce\Entities\StationVcr;
use Modules\Osce\Entities\Student;
use Modules\Osce\Entities\TestResult;
use Modules\Osce\Http\Controllers\CommonController;
use DB;

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
            $list[] = [
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
     * * int    station_id    考站id   (必须的)
     * * int     exam_id       考试id   (必须的)
     *
     * @return json
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
     *   * 提交评价
     * @method GET
     * @url /osce/admin/invigilatepad/save-exam-evaluate
     * @access public
     * @param Request $request get请求<br><br>
     * <b>get请求字段：</b>
     * * int     subject_id    考试项目id  (必须的)
     * * int     standard_id  评分标准 id   (必须的)
     * * int     score       根据评分标准所得的分值
     * * string         evaluate     评价内容
     *
     * @return  json
     *
     * @version 1.0
     * @author zhouqiang <zhouqiang@misrobot.com>
     * @date
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function  getSaveExamEvaluate(Request $request){
        $this->validate($request,[
            'subject_id' =>'required|integer',
            'standard_id' =>'required|integer',
            'score' =>'required',
            'evaluate'=>'required'
        ],[
            'subject_id.required'=>'请检查考试项目',
            'standard_id.required'=>'请检查评分标准',
            'score.required'=>'请检查评分标准分值',
            'evaluate.required'=>'评价内容',
        ]);
        $data =[
            'subject_id'=>Input::get('subject_id'),
            'standard_id'=>Input::get('standard_id'),
            'score'=>Input::get('score'),
            'evaluate'=>Input::get('evaluate'),
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
     * 提交成绩详情
     * @method GET
     * @url /osce/admin/invigilatepad/save-exam-result
     * @access public
     * @param Request $request get请求<br><br>
     * <b>get请求字段：</b>
     * * string     station_id    考站id   (必须的)
     * @return view
     * @version 1.0
     * @author zhouqiang <zhouqiang@misrobot.com>
     * @date
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */

      public  function getSaveExamResult(Request $request){
           $this->validate($request,[
               'student_id'=>'required|integer',
               'station_id'=>'required|integer',
               'exam_screening_id'=>'required|integer',
               'begin_dt'=>'required|integer',
               'end_dt'=>'required|integer',
               'time'=>'required|integer',
               'score'=>'required|integer',
               'score_dt'=>'required|integer',
               'teacher_id'=>'required|integer',

           ]);
          $data=[
              'station_id'=>Input::get('station_id'),
              'student_id'=>Input::get('student_id'),
              'exam_screening_id'=>Input::get('exam_screening_id'),
              'begin_dt'=>Input::get('begin_dt'),//考试开始时间
              'end_dt'=>Input::get('end_dt'),//考试实际结束时间
              'time'=>Input::get('time'),//考试用时
              'score'=>Input::get('score'),//最终成绩
              'score_dt'=>Input::get('score_dt'),//评分时间
              'teacher_id'=>Input::get('teacher_id'),

          ];
           $save =DB::connection('osce_mis')->table('test_result')->insertGetId($data);
          if($save){
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
             $stationId = Input::get('station_id');
             $stationvcrModel = new StationVcr();
             $list = $stationvcrModel->vcrlist($stationId);
          $vcrdata= [
              'name'=>$list->name,
              'code'=>$list->code,
              'ip'=>$list->ip,
              'username'=>$list->username,
              'port'=>$list->port,
              'channel'=>$list->channel,
          ];
          if($list->status==0){
              return response()->json(
                  $this->success_data(0,'摄像头损坏')
              );
          }else{
              return response()->json(
                  $this->success_data($vcrdata,1,'摄像头可用')
              );
          }


      }

    /**
     * 语音点评
     * @method GET
     * @url /osce/admin/invigilatepad/voice-prompt
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
      public  function getVoicePrompt(Request $request){
          $this->validate($request,[
              'exam_id'=>'required|integer',
              'station_id'=>'required|integer',
              'student_id'=>'required|integer',
          ]);



      }




}