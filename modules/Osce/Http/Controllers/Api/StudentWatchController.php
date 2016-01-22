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
use Modules\Osce\Entities\ExamFlow;
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

        $examInfo= Student::where('id','=',$studentId)->select('exam_id')->first();

        $examId= $examInfo->exam_id;

        $ExamQueueModel= new ExamQueue();

        $examQueueCollect =  $ExamQueueModel->StudentExamQueue($studentId);
        dump($examQueueCollect);

        $nowNextQueue   =   $ExamQueueModel->nowQueue($examQueueCollect);
        $nowQueue =   $nowNextQueue[0] ;
        $nextQueue =  $nowNextQueue[1];
        $nowTime =time();

        if(empty($nowQueue)){
            //查询出学生所有应该的考试
            $ExamFlowModel = new  ExamFlow();
            $studentExamSum = $ExamFlowModel->studentExamSum($examId);
            //学生完成的考试
            $ExamFinishStatus =ExamQueue::where('status','=',3)->where('student_id','=',$studentId)->count();

            if($ExamFinishStatus == 0){
                return response()->json(
                    $this->success_data(3,'你目前没有考试')
                );
            }
            if($ExamFinishStatus < $studentExamSum ){
                 return response()->json(
                     $this->success_data(2 ,'你还有考试还未完成，请到候考区候考')
                 );
             }else {
                 $TestResultModel = new TestResult();
                 $TestResult = $TestResultModel->AcquireExam($studentId);
                 if ($TestResult) {
                     $studentExamScore = $TestResult->score;
                     return response()->json(
                         $this->success_data($studentExamScore, '考试完成 最终成绩')
                     );
                 }else{
                     return response()->json(
                         $this->success_data(0,'成绩推送失败')
                     );
                 }
             }

        }else{

            if($nowQueue->status==1){
                dump('待考');
                if(strtotime($nowQueue->begin_dt)-$nowTime <= 120 && strtotime($nowQueue->begin_dt)-$nowTime>0){
                    $examRoomName=$nowQueue->room_name;
                    return response()->json(
                        $this->success_data($examRoomName,'考生开考通知')
                    );
                }else{
                    $willStudents = ExamQueue::where('room_id','=',$nowQueue['room_id'])
                        ->whereBetween('status',[1,2])
                        ->count();
                    $examtimes=   strtotime($nowQueue->begin_dt);
                    $examRoomName=$nowQueue->room_name;
                    $data=[
                        'willStudents'=>$willStudents,
                        'examtimes'=>$examtimes,
                        'examRoomName'=>$examRoomName
                    ];
                    return response()->json(
                        $this->success_data($data,'考生等待信息')
                    );
                }

            }else{
                dump('考试中');
                $surplus =((strtotime($nowQueue['begin_dt']) + ($nowQueue->mins*60)) - $nowTime);
                if($surplus <=0){
                    if(!empty($nextQueue)){
                        dump('下一场');
                        $nextExamName=  $nextQueue ['room_name'];
                        return response()->json(
                            $this->success_data($nextExamName,'下一场')
                        );
                    }else{
                        return response()->json(
                            $this->success_data(4,'目前没有下一场，请等待下一步通知')
                        );
                    }

                }else{
                    $surplus = floor($surplus/60) . ':' . $surplus%60;
                    dump('当前考站剩余时间'.$surplus);
                    return response()->json(
                        $this->success_data($surplus,'当前考站剩余时间')
                    );
                }

            }

        }

    }

}