<?php
/**
 * Created by PhpStorm.
 * User: zhouchong
 * Date: 2016/1/28 0028
 * Time: 10:32
 */
namespace Modules\Osce\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Modules\Osce\Entities\Exam;
use Modules\Osce\Entities\ExamResult;
use Modules\Osce\Entities\ExamScore;
use Modules\Osce\Entities\Standard;
use Modules\Osce\Entities\Station;
use Modules\Osce\Entities\TestAttach;
use Modules\Osce\Http\Controllers\CommonController;

class ExamResultController extends CommonController{

    /**
     *
     * @method GET
     * @url /exam/result-exam
     * @access public
     *
     * @param Request $request post请求<br><br>
     * <b>post请求字段：</b>
     * * string        参数英文名        参数中文名(必须的)
     *
     * @return ${response}
     *
     * @version 1.0
     * @author zhouchong <zhouchong@misrobot.com>
     * @date ${DATE} ${TIME}
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
//    public function getResultExam(Request $request){
//
//         $exams=Exam::select()->get();
//         return response()->json(
//           $this->success_data($exams,1,'success')
//         );
//
//    }

    /**
     *
     * @method GET
     * @url /exam/result-station
     * @access public
     *
     * @param Request $request post请求<br><br>
     * <b>post请求字段：</b>
     * * string        参数英文名        参数中文名(必须的)
     *
     * @return ${response}
     *
     * @version 1.0
     * @author zhouchong <zhouchong@misrobot.com>
     * @date ${DATE} ${TIME}
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
//    public function getResultStation(Request $request){
//         $stations=Station::select()->get();
//         return response()->json(
//            $this->success_data($stations,1,'success')
//        );
//    }

    /**
     *
     * @method GET
     * @url /exam/exam-result-list
     * @access public
     *
     * @param Request $request post请求<br><br>
     * <b>post请求字段：</b>
     * * int        exam_id          考试id(必须的)
     * * int        station_id       考站id  (必须的)
     * * string     name             学生姓名(必须的)
     *
     * @return ${response}
     *
     * @version 1.0
     * @author zhouchong <zhouchong@misrobot.com>
     * @date ${DATE} ${TIME}
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function geExamResultList(Request $request){
         $this->validate($request,[
             'exam_id'     => 'sometimes',
             'station_id'  => 'sometimes',
             'name'        => 'sometimes',
         ]);

         $examId=$request->get('exam_id');
         $stationId=$request->get('station_id');
         $name=$request->get('name');

         $stations=Station::select()->get();
         $exams=Exam::select()->get();
         $examResult=new ExamResult();
         $examResults=$examResult->getResultList($examId,$stationId,$name);
         foreach($examResults as $item){
              $time=floor($item->time/60).':'.($item->time%60);
              $item->time=$time;
         }
         return view('osce::admin.exammanage.score_query')->with(['examResults'=>$examResults,'stations'=>$stations,'exams'=>$exams]);
    }

    /**
     *获取考试成绩详情页
     * @method GET
     * @url /exam/exam-result-detail
     * @access public
     *
     * @param Request $request post请求<br><br>
     * <b>post请求字段：</b>
     * * int        id        成绩id(必须的)
     *
     * @return ${response}
     *
     * @version 1.0
     * @author zhouchong <zhouchong@misrobot.com>
     * @date ${DATE} ${TIME}
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function getExamResultDetail(Request $request){
       $this->validate($request,[
           'id'   => 'required|integer'
       ]);

        $id=$request->get('id');
        $examResult=new ExamResult();
        $examResult=$examResult->getResultDetail($id);
        $result=[];
        foreach($examResult as $item){
         $result=[
             'id' =>$item->id,
             'exam_name' =>$item->exam_name,
             'student'   =>$item->student,
             'teacher'   =>$item->teacher,
             'begin_dt'  =>$item->begin_dt,
             'time'      =>$item->time,
             'score'     =>$item->score,
             'evaluate'  =>$item->evaluate,
             'operation' =>$item->operation,
             'skilled'   =>$item->skilled,
             'patient'   =>$item->patient,
             'station_id'   =>$item->station_id,
             'affinity'  =>$item->affinity,
             'subject_title' =>$item->subject_title,
             'subject_id' =>$item->subject_id,
         ];
        }
        $result['time']=floor($result['time']/60);
        $result['time'].=':';
        $result['time'].=$result['time']%60;

        $score=ExamScore::where('exam_result_id',$id)->where('subject_id',$result['subject_id'])->select()->get();
        $image=[];
        foreach($score as $itm){
            $image[]=[
                'standard'=>$itm->standard,
                'score'=>$itm->score,
                'image'=>'',
            ];
        }

        $scores=[];
        foreach($image as $img){
            $scores[]=[
                'standard'=>$img['standard'],
                'score'=>$img['score'],
                'image'=>TestAttach::where('test_result_id',$result['id'])->where('standard_id',$img['standard']->id)->select()->get(),
            ];

        }

        $standard=[];
        foreach($scores as $standards){
            if($standards['standard']->pid==0){
                $standard[]=ExamScore::where('exam_result_id',$id)->where('subject_id',$result['subject_id'])->where('standard_id',$standards['standard']->id)->select()->first()->score;
            }
        }

        $standardModel=new Standard();
        $totalScore=$standardModel->getScore($result['station_id'],$result['subject_id']);
        $sort=$totalScore[0]->sort;
        $avg=[];
        for($i=1;$i<=$sort;$i++){
             $avg[]=$standardModel->getAvgScore($i,$result['station_id'],$result['subject_id']);
        }

        return view('osce::admin.exammanage.score_query_detail')->with(['result'=>$result,'scores'=>$scores,'standard'=>$standard,'avg'=>$avg]);

    }



}