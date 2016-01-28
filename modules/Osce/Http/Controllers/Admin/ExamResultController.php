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
use Modules\Osce\Entities\Station;
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
         return view('osce::admin.exammanage.score_query')->with(['examResults'=>$examResults,'stations'=>$stations,'exams'=>$exams]);
    }
}