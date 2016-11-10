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
use Modules\Osce\Entities\ExamQueue;
use Modules\Osce\Entities\ExamResult;
use Modules\Osce\Entities\ExamScreening;
use Modules\Osce\Entities\ExamScreeningStudent;
use Modules\Osce\Entities\QuestionBankEntities\ExamControl;
use Modules\Osce\Entities\Watch;
use Modules\Osce\Http\Controllers\Api\StudentWatchController;
use Modules\Osce\Http\Controllers\CommonController;
use Illuminate\Http\Request;
use Modules\Osce\Repositories\Common;
use Modules\Osce\Repositories\WatchReminderRepositories;


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
//        dd($data);
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
    public function postStopExam(Request $request,WatchReminderRepositories $watchReminder,ExamResult $examResult)
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
            'reason' =>$request->input('reason'), //终止考试理由
            'type' =>-1, //终止考试
        );
        if($request->input('status')==3){
            $data['description'] = $request->input('description');
//            $data['type'] = -1;//终止考试
        }elseif($request->input('status')==2){
            $data['description'] = 3;  //修改为直接替考结束考试
//            $data['type'] = 1;//上报替考
        }elseif($request->input('status')==1){
            $data['description'] = 1; //修改为直接弃考结束考试
//            $data['type'] = 2;//上报弃考
        }
        try{

            $examControlModel = new ExamControl();
            $examControlModel->stopExam($data);
            //向pad端推送消息
//            $redis = Redis::connection('message');

            $time = date('Y-m-d H:i:s', time());
            //终止考试的情况下
            if($data['description']==1&&$data['type']=-1) {

                //推送 TODO: Zhoufuxiang
                Common::padPublish(md5($_SERVER['HTTP_HOST']) . 'pad_message', $this->success_data(['start_time' => $time, 'student_id' => $data['studentId'], 'exam_screening_id' => $data['examScreeningId']], 107, '考试终止成功'));

//                $redis->publish(md5($_SERVER['HTTP_HOST']) . 'pad_message', json_encode($this->success_data(['start_time' => $time, 'student_id' => $data['studentId'], 'exam_screening_id' => $data['examScreeningId']], 107, '考试终止成功')));
            }elseif($data['description']==2||$data['description']==3||$data['description']==4&&$data['type']=-1){

                //推送 TODO: Zhoufuxiang
                Common::padPublish(md5($_SERVER['HTTP_HOST']) . 'pad_message', $this->success_data(['start_time' => $time, 'student_id' => $data['studentId'], 'exam_screening_id' => $data['examScreeningId']], 106, '考试终止成功'));

//                $redis->publish(md5($_SERVER['HTTP_HOST']) . 'pad_message', json_encode($this->success_data(['start_time' => $time, 'student_id' => $data['studentId'], 'exam_screening_id' => $data['examScreeningId']], 106, '考试终止成功')));
            }elseif($data['description']=-1&& $data['type'] = 1){

                //推送 TODO: Zhoufuxiang
                Common::padPublish(md5($_SERVER['HTTP_HOST']) . 'pad_message', $this->success_data(['start_time' => $time, 'student_id' => $data['studentId'], 'exam_screening_id' => $data['examScreeningId']], 106, '考试终止成功'));

//                $redis->publish(md5($_SERVER['HTTP_HOST']) . 'pad_message', json_encode($this->success_data(['start_time' => $time, 'student_id' => $data['studentId'], 'exam_screening_id' => $data['examScreeningId']], 106, '考试终止成功')));
            }elseif($data['description']=-1&& $data['type'] = 2){

                //推送 TODO: Zhoufuxiang
                Common::padPublish(md5($_SERVER['HTTP_HOST']) . 'pad_message', $this->success_data(['start_time' => $time, 'student_id' => $data['studentId'], 'exam_screening_id' => $data['examScreeningId']], 107, '考试终止成功'));

//                $redis->publish(md5($_SERVER['HTTP_HOST']) . 'pad_message', json_encode($this->success_data(['start_time' => $time, 'student_id' => $data['studentId'], 'exam_screening_id' => $data['examScreeningId']], 107, '考试终止成功')));
            }



            $examScreeningStudentData = ExamScreeningStudent::where('exam_screening_id','=',$data['examScreeningId'])
                ->where('student_id','=',$data['studentId'])->first();
            
            if(!empty($examScreeningStudentData)){
                
                //todo 调用腕表信息
                try{
                    $watchReminder ->getWatchPublish($data['examId'],$data['studentId'], $data['stationId']);

                }catch (\Exception $ex){
                    \Log::debug('终止考试调用腕表出错',[$data['examId'],$data['studentId'], $data['stationId']]);
                }
            }
            
            return response()->json(
                $this->success_data([],1,'success')
            );
        }catch (\Exception $ex) {
            return response()->json($this->fail($ex));
        }

    }

    /**获取获取摄像头信息
     * @method
     * @url /osce/
     * @access public
     * @param Request $request
     * @return \Illuminate\View\View
     * @author xumin <xumin@misrobot.com>
     * @date
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */

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

    /**获取考生剩余考试时间
     * @method
     * @url GET /osce/admin/exam-control/getTime
     * @access public
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @author xumin <xumin@misrobot.com>
     * @date
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */

    public function getTime(Request $request){

        try{

            $this->validate($request,[
                'exam_id'       => 'required|integer',  //考试id
                'student_id'    => 'required|integer'   //学生id
            ]);

            $exam_id    = $request->input('exam_id');
            $student_id = $request->input('student_id');

            $examQueue = ExamQueue::where('exam_id',$exam_id)->where('student_id',$student_id)->where('status',2)->first();
            if(!is_null($examQueue))
            {
                //获取系统的当前时间
                $time = time();
                //获取剩余时间 （剩余时间 = 考试结束时间-当前时间）
                $remainTime = strtotime($examQueue->end_dt) - $time;
                if($remainTime <= 0){
                    $remainTime = 0;
                }
//                //考试时间
//                $examTime =strtotime($examQueue->end_dt)-strtotime($examQueue->begin_dt);
//                $remainTime = 0;
//                if($time-strtotime($examQueue->begin_dt) < $examTime){
//                    //考试时间还没用完
//                    $remainTime =$examTime - ($time-strtotime($examQueue->begin_dt));
//                }
            }else
            {
                throw new \Exception('没有该考生正在考试的队列信息！',-101);
            }
            //返回数据
            return response()->json(
                $this->success_data(['remainTime' => $remainTime], 1, 'success')
            );

        }catch (\Exception $ex){
            return response()->json($this->fail($ex));
        }
    }

}