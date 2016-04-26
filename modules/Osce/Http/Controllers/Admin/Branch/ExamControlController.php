<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/3/10 0010
 * Time: 14:05
 */

namespace Modules\Osce\Http\Controllers\Admin\Branch;

use Illuminate\Support\Facades\Redis;
use Modules\Osce\Entities\AutomaticPlanArrangement\Student;
use Modules\Osce\Entities\ExamScreening;
use Modules\Osce\Entities\ExamScreeningStudent;
use Modules\Osce\Entities\QuestionBankEntities\ExamControl;
use Modules\Osce\Entities\Watch;
use Modules\Osce\Http\Controllers\Api\StudentWatchController;
use Modules\Osce\Http\Controllers\CommonController;
use Illuminate\Http\Request;


/**考试监控
 * Class ExamControlController
 * @package Modules\Osce\Http\Controllers\Admin\Branch
 */

class ExamControlController extends CommonController
{
    /**正在考试列表
     * @method
     * @url /osce/
     * @access public
     * @param Request $request
     * @return \Illuminate\View\View
     * @author xumin <xumin@misrobot.com>
     * @date
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */

    public function getExamlist()
    {
        $examControlModel = new ExamControl();
        $data = $examControlModel->getDoingExamList();
        //dd($data);
        return view('osce::admin.testMonitor.monitor_test', [
            'data'      =>$data,
        ]);
    }



    /**终止考试数据交互
     * @method
     * @url /osce/
     * @access public
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @author xumin <xumin@misrobot.com>
     * @date
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function postStopExam(Request $request)
    {

        $this->validate($request,[
            'examId'       => 'required|integer',//考试编号
            'studentId'    => 'required|integer',//考生编号
            'examScreeningId'    => 'required|integer',//场次编号
            'stationId'    => 'required|integer',//考站编号
            'userId'    => 'required|integer',//老师id
            'examScreeningStudentId'    => 'required|integer',//考试场次-学生关系id
            'status'    => 'required|integer', //1确认弃考 2确认替考 3终止考试
        ]);

        $data=array(
            'examId' =>$request->input('examId'), //考试编号
            'studentId' =>$request->input('studentId'), //考生编号
            'examScreeningId' =>$request->input('examScreeningId'), //场次编号
            'stationId' =>$request->input('stationId'), //考站编号
            'userId' =>$request->input('userId'), //老师id
            'examScreeningStudentId' =>$request->input('examScreeningStudentId'), //考试场次-学生关系id
            'status' =>$request->input('status'), //1确认弃考 2确认替考 3终止考试
        );
        if($request->input('status')==3){
            $data['description'] = $request->input('description');
            $data['type'] = -1;//终止考试
        }elseif($request->input('status')==2){
            $data['description'] = -1;
            $data['type'] = 1;//上报替考
        }elseif($request->input('status')==1){
            $data['description'] = -1;
            $data['type'] = 2;//上报弃考
        }

/*
        $examControlModel = new ExamControl();
        $result = $examControlModel->stopExam($data);
        if($result){
            //向pad端推送消息
            $redis = Redis::connection('message');
            $redis->publish('pad_message', json_encode($this->success_data([],106,'考试终止成功')));
            $examScreeningStudentData = ExamScreeningStudent::where('exam_screening_id','=',$data['examScreeningId'])
                ->where('student_id','=',$data['studentId'])->first();

            if(!empty($examScreeningStudentData)){
                //向watch端推送消息
                $watchData = Watch::where('id','=',$examScreeningStudentData->watch_id)->first();
                $request['nfc_code'] = $watchData->code;
                //拿到阶段序号
                $gradationOrder = ExamScreening::find($data['examScreeningId']);
                //拿到所有场次id
                $examscreeningId = ExamScreening::where('exam_id','=',$data['examId'])->where('gradation_order','=',$gradationOrder->gradation_order)->get()->pluck('id');
                $studentWatchController = new StudentWatchController();
                $studentWatchController->getStudentExamReminder($request,$data['stationId'],$examscreeningId);
            }
            return response()->json(
                $this->success_data([],1,'success')
            );
        }
*/


        try{

            $examControlModel = new ExamControl();
            $examControlModel->stopExam($data);
            //向pad端推送消息
            $redis = Redis::connection('message');
            $redis->publish(md5($_SERVER['SERVER_NAME']).'pad_message', json_encode($this->success_data([],106,'考试终止成功')));
            $examScreeningStudentData = ExamScreeningStudent::where('exam_screening_id','=',$data['examScreeningId'])
                ->where('student_id','=',$data['studentId'])->first();
            if(!empty($examScreeningStudentData)){
                //向watch端推送消息
                $watchData = Watch::where('id','=',$examScreeningStudentData->watch_id)->first();
                $request['nfc_code'] = $watchData->code;
                //拿到阶段序号
                $gradationOrder = ExamScreening::find($data['examScreeningId']);
                //拿到所有场次id
                $examscreeningId = ExamScreening::where('exam_id','=',$data['examId'])->where('gradation_order','=',$gradationOrder->gradation_order)->get()->pluck('id');
                $studentWatchController = new StudentWatchController();
                $studentWatchController->getStudentExamReminder($request,$data['stationId'],$examscreeningId);

            }
            return response()->json(
                $this->success_data([],1,'success')
            );
        }catch (\Exception $ex) {
            return response()->json($this->fail($ex));
        }

    }

    public function getVcrsList(Request $request)
    {
        $this->validate($request,[
            'examId'       => 'required|integer',
            'stationId'    => 'required|integer',
        ]);
        //获取参数
        $examId    = $request->input('examId');
        $stationId = $request->input('stationId');
        $examControlModel = new ExamControl();
        $vcrInfo    = $examControlModel->getVcrInfo($examId, $stationId);
       // dd($vcrInfo);

        return view('osce::admin.testMonitor.monitor_test_video', [
            'data'      =>$vcrInfo,
        ]);

    }






























}