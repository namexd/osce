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

    public function getResultExam(Request $request){

         $exams=Exam::select()->get();
         return response()->json(
           $this->success_data($exams,1,'success')
         );

    }

    public function getResultStation(Request $request){
         $stations=Station::select()->get();
         return response()->json(
            $this->success_data($stations,1,'success')
        );
    }

    public function geExamResultList(Request $request){
         $this->validate($request,[
             'exam_id'     => 'sometimes',
             'station_id'  => 'sometimes',
             'name'        => 'sometimes',
         ]);

         $examId=$request->get('exam_id');
         $stationId=$request->get('station_id');
         $name=$request->get('name');

         $examResult=new ExamResult();
         $examResults=$examResult->getResultList($examId,$stationId,$name);
         return view()->with('examResults',$examResults);
    }
}