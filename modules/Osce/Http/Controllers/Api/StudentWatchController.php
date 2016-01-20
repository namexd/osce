<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/1/18 0018
 * Time: 9:51
 */

namespace Modules\Osce\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Input;
use Modules\Osce\Entities\Exam;
use Modules\Osce\Entities\ExamQueue;
use Modules\Osce\Entities\ExamScore;
use Modules\Osce\Entities\ExamScreening;
use Modules\Osce\Entities\Standard;
use Modules\Osce\Entities\Station;
use Modules\Osce\Entities\StationVcr;
use Modules\Osce\Entities\Student;
use Modules\Osce\Entities\TestAttach;
use Modules\Osce\Entities\Teacher;
use Modules\Osce\Entities\TestResult;
use Modules\Osce\Entities\WatchLog;
use Modules\Osce\Http\Controllers\CommonController;
use DB;
use Storage;
class StudentWatchController extends  CommonController
{


    /**
     * 学生腕表信息
     * @method GET
     * @url /osce/api/StudentWatch/wait-exam-list
     * @access public
     * @param Request $request get请求<br><br>
     * <b>get请求字段：</b>
     * * string     watch_id    腕表 id   (必须的)
     *
     * @return json
     *
     * @version 1.0
     * @author zhouqiang <zhouqiang@misrobot.com>
     * @date
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function getWaitExamList(Request $request){
        $this->validate($request,[
            'watch_id'=>'required|integer'
        ]);
        $nowTime    =   time();
        $watchId=$request->input('watch_id');

        $watchStudent= WatchLog::where('watch_id','=',$watchId)->where('action','绑定')->select('student_id')->orderBy('id','desc')->first();
        if(!$watchStudent){
            return response()->json(
                $this->fail(new \Exception('没有找到学生的腕表信息'))
            );
        }
        $ExamQueueModel= new ExamQueue();
        $examQueueCollect =  $ExamQueueModel->StudentExamInfo($watchStudent->student_id);

        dump($examQueueCollect);
        $nowQueue   =   $ExamQueueModel->nowQueue($examQueueCollect,$nowTime);
        if(empty($nowQueue))
        {
            return response()->json(
                $this->success_data('当前无考试')
            );
        }
        else
        {
            //   待考/开考通知
            if(strtotime($nowQueue->begin_dt)-$nowTime >= 120){
                $willStudents = ExamQueue::where('room_id','=',$nowQueue['room_id'])
                    ->whereBetween('status',[1,2])
                    ->count();
                $examtimes= strtotime($nowQueue->end_dt) -strtotime($nowQueue->begin_dt);
                $examRoomName=$nowQueue->room_name;
                $data=[
                    'willStudents'=>$willStudents,
                    'examtimes'=>$examtimes,
                    'examRoomName'=>$examRoomName
                ];
                return response()->json(
                    $this->success_data($data,'考生等待信息')
                );
            }elseif(strtotime($nowQueue->begin_dt)-$nowTime <= 120 && strtotime($nowQueue->begin_dt)-$nowTime>0) {
                $examRoomName=$nowQueue->room_name;
                return response()->json(
                    $this->success_data($examRoomName,'考生开考通知')
                );
            }

            $nextQueue  =   $ExamQueueModel->nextQueue($examQueueCollect,$nowTime);
            if(empty($nextQueue))
            {
                if(strtotime($nowQueue['end_dt']) - $nowTime >=0 ){
                    $surplus = strtotime($nowQueue['end_dt']) - $nowTime;
                    $surplus = floor($surplus/60) . ':' . $surplus%60;
                    $changeStatus= ExamQueue::where('id','=',$nowQueue['id'])->update(['status'=>2]);
                    dump('当前考试剩余时间'.$surplus);
                }else{
                    $changeStatus= ExamQueue::where('id','=',$nowQueue['id'])->update(['status'=>3]);
                    //考试完成后成绩推送
                    $TestResultModel= new TestResult();
                    $studentId= $nowQueue['student_id'];

                    $TestResult =$TestResultModel->AcquireExam($studentId);

                    if($TestResult){
                        $studentExamScore  =$TestResult->score;
                        return response()->json(
                            $this->success_data( $studentExamScore, '考试完成 最终成绩')
                        );
                         die;
                    }else{
                        return response()->json(
                            $this->success_data(0,'成绩推送失败')
                        );
                        die;
                    }
                }
            }
            else
            {
                //当前剩余考试时间
                if(strtotime($nowQueue['end_dt']) - $nowTime >= 60 ){
                    $surplus = strtotime($nowQueue['end_dt']) - $nowTime;
                    $surplus = floor($surplus/60) . ':' . $surplus%60;
                    $changeStatus= ExamQueue::where('id','=',$nowQueue['id'])->update(['status'=>2]);

                    dump('当前考试剩余时间'.$surplus);


                }else{
                    if(strtotime($nowQueue['end_dt']) - $nowTime <60 && strtotime($nowQueue['end_dt']) - $nowTime>0 ){
                        $changeStatus= ExamQueue::where('id','=',$nowQueue['id'])->update(['status'=>3]);
                        return response()->json(
                            $this->success_data($nextQueue['room_name'],'下一场考试')
                        );
                    }
                }
            }
        }


    }



    /**
     * 学生腕表信息
     * @method GET
     * @url /osce/api/student-watch/student-exam-reminder
     * @access public
     * @param Request $request get请求<br><br>
     * <b>get请求字段：</b>
     * * string     watch_id    腕表 id   (必须的)
     *
     * @return json
     *
     * @version 1.0
     * @author zhouqiang <zhouqiang@misrobot.com>
     * @date
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */

    public function   getStudentExamReminder(Request $request){
        $this->validate($request,[
            'watch_id'=>'required|integer'
        ]);

        $watchId=$request->input('watch_id');

        $watchStudent= WatchLog::where('watch_id','=',$watchId)->where('action','绑定')->select('student_id')->orderBy('id','desc')->first();
        if(!$watchStudent){
            return response()->json(
                $this->fail(new \Exception('没有找到学生的腕表信息'))
            );
        }
        $studentId = $watchStudent->student_id;

        $ExamQueueModel= new ExamQueue();

        $examQueueCollect =  $ExamQueueModel->StudentExamQueue($studentId);

        dump($examQueueCollect);
          $status = 0;
        $curExam = $nextExam =  null;

        foreach($examQueueCollect as $nowQueue){
          if($nowQueue->status == 1) {
              $status = 1;
              $curExam = $nowQueue;
              break;
          }

          if($nowQueue->status == 2){
              $status =2;
              $curExam = $nowQueue;
              break;

          }
         if($nowQueue->status == 3){
             $status =3;
             $curExam = $nowQueue;

          }



        }



        switch($status){
            case 1;
                dump('待考');
                    $willStudents = ExamQueue::where('room_id','=',$nowQueue['room_id'])
                        ->whereBetween('status',[1,2])
                        ->count();
                    $examtimes= strtotime($nowQueue->end_dt) -strtotime($nowQueue->begin_dt);
                    $examRoomName=$nowQueue->room_name;
                    $data=[
                        'willStudents'=>$willStudents,
                        'examtimes'=>$examtimes,
                        'examRoomName'=>$examRoomName
                    ];
                    return response()->json(
                        $this->success_data($data,'考生等待信息')
                    );
                break;

            case 2;
                dump('考试中');
                    $nowTime =time();
                    $surplus = strtotime($nowQueue['end_dt']) - $nowTime;
                    $surplus = floor($surplus/60) . ':' . $surplus%60;
                    $changeStatus= ExamQueue::where('id','=',$nowQueue['id'])->update(['status'=>3]);

                break;

            case 3;


                dump('当前考试已完成');
                break;
        }


    }

}