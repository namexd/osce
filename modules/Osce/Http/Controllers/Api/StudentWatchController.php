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
use Modules\Osce\Entities\ExamResult;
use Modules\Osce\Entities\ExamScore;
use Modules\Osce\Entities\ExamScreening;
use Modules\Osce\Entities\ExamScreeningStudent;
use Modules\Osce\Entities\Standard;
use Modules\Osce\Entities\Station;
use Modules\Osce\Entities\StationVcr;
use Modules\Osce\Entities\Student;
use Modules\Osce\Entities\TestAttach;
use Modules\Osce\Entities\Teacher;
use Modules\Osce\Entities\TestResult;
use Modules\Osce\Entities\Watch;
use Modules\Osce\Entities\WatchLog;
use Modules\Osce\Http\Controllers\CommonController;
use DB;
use Storage;

class StudentWatchController extends CommonController
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

    public function   getStudentExamReminder(Request $request)
    {
        $this->validate($request, [
            'nfc_code' => 'required'
        ]);
        $data = [
            'title' => '',
            'willStudents' => '',
            'estTime' => '',
            'willRoomName' => '',
            'roomName' => '',
            'nextExamName' => '',
            'surplus' => '',
            'score' => '',
        ];
        $code = 0;
        $watchNfcCode = $request->input('nfc_code');



        //根据设备编号找到设备id
        $watchId = Watch::where('code', '=', $watchNfcCode)->select('id')->first();
        if (!$watchId) {
            $code = -1;
            $data['title'] = '没有找到到腕表信息';
            return response()->json(
                $this->success_data($data, $code)
            );
        }
        //判定腕表是否解绑
        $watch =Watch::where('id',$watchId->id)->first();
        if($watch->status==0){
            $code = -1;
            $data['title'] = '该腕表还没有学生绑定';
            return response()->json(
                $this->success_data($data, $code)
            );
        }


        //  根据腕表id找到对应的考试场次和学生
        $watchStudent = ExamScreeningStudent::where('watch_id', '=', $watchId->id)->where('is_end', '=', 0)->orderBy('signin_dt','desc')->first();
        if (!$watchStudent) {
            $data['title'] = '没有找到学生的腕表信息';
            return response()->json(
                $this->success_data($data, $code)
            );
        }


        //得到场次id
//        $examScreeningId= $watchStudent->exam_screening_id;
        //得到学生id
        $studentId = $watchStudent->student_id;
        // 根据考生id找到当前的考试
        $examInfo = Student::where('id', '=', $studentId)->select('exam_id')->first();
        $examId = $examInfo->exam_id;
        //根据考生id在队列中得到当前考试的所有考试队列
        $ExamQueueModel = new ExamQueue();
        $examQueueCollect = $ExamQueueModel->StudentExamQueue($studentId);
        if(is_null($examQueueCollect)){
            $code = -1;
            $data['title'] = '学生队列信息不正确';
            return response()->json(
                $this->success_data($data, $code)
            );

        }
        //判断考试的状态
        $data = $this->nowQueue($examQueueCollect);
        return response()->json(
            $this->success_data($data, $code=$data['code'])
        );

    }




    /**
     * 学生腕表信息 考试信息判断
     * @param $examQueueCollect
     * @return array
     * @internal param $room_id
     * @author zhouqiang
     */
    public function nowQueue($examQueueCollect)
    {
        $status         =   $examQueueCollect->pluck('status');
        $statusArray    =   $status->toArray();
        if(in_array(1,$statusArray))
        {
            return $this->getStatusOneExam($examQueueCollect);
        }
        if(in_array(2,$statusArray))
        {
            return $this->getStatusTwoExam($examQueueCollect);
        }
        if(in_array(3,$statusArray))
        {
            return $this->getStatusThreeExam($examQueueCollect);
        }
        return $this->getStatusWaitExam($examQueueCollect);

    }
    //判断腕表提醒状态为1时
    private function getStatusOneExam($examQueueCollect){
        $items   =   array_where($examQueueCollect,function($key,$value){
            if($value ->status  ==  1)
            {
                return $value;
            }
        });
        $item   =   array_pop($items);
        if(is_null($item)){
            throw new \Exception('队列异常');
        }

        $station=$item->station;
        $room =$item->room;
        $data   =   [
            'code'=>3,
            'title'=>'请请入下面考站考试',
            'roomName'=>$room->name.'-'.$station->name,
        ];
        return $data;
    }


    //判断腕表提醒状态为2时

    private function getStatusTwoExam($examQueueCollect){
//        foreach ($examQueueCollect as $items) {
//            if ($items->status == 2) {
//                return $items;
//            }
//        }

        $items   =   array_where($examQueueCollect,function($key,$value){
            if($value ->status  ==  2)
            {
                return $value;
            }
        });
        $item   =   array_shift($items);
        if(is_null($item)){
            throw new \Exception('队列异常');
        }

        $surplus = strtotime($item->end_dt)-time();
        if($surplus<=0){
            //todo 调用jiangzhiheng接口
            $endStudentExam = ExamQueue::endStudentQueueExam($item->student_id);
        };
        $data=[
            'code'      =>  4,
            'title'     =>  '当前考站剩余时间',
            'surplus'   =>  $surplus,
        ];

           return $data;

    }
    //判断腕表提醒状态为3时
    private function getStatusThreeExam($examQueueCollect){
        $nextExamQueue  =   '';
        $examQueue      =   '';
        foreach($examQueueCollect as $examQueue)
        {
            if($examQueue->status!=3)
            {
                if(empty($nextExamQueue))
                {
                    $nextExamQueue  =   $examQueue;
                    $time           =   strtotime($examQueue->begin_dt);
                }
                else
                {
                    if($time>=strtotime($examQueue->begin_dt))
                    {
                        $nextExamQueue=$examQueue;
                        $time   =   strtotime($examQueue->begin_dt);
                    }
                }
            }
        }

        if(empty($nextExamQueue))
        {
            if(!empty($examQueue))
            {
                return $this->getExamComplete($examQueue);
            }
            else
            {
                throw new \Exception('没有发现该考生相关排考计划');
            }

        }
        else
        {

            if(!is_null($nextExamQueue->station))
            {

                $data = [
                    'code'=> 5,
                    'title' => '当前考站考试完成，进入下一场考试考站名',
                    'nextExamName' =>$nextExamQueue->room->name.'-'.$nextExamQueue->station->name,
                ];
            }
            else
            {
                $data = [
                    'code'=> 5,
                    'title' => '当前考站考试完成，进入下一场考试考场名',
                    'nextExamName' =>$nextExamQueue->room->name,
                ];
            }
        }
        return $data;
    }

    private function  getExamComplete($examQueue){

        //根据考试获取到考试流程
        $ExamFlowModel = new  ExamFlow();
        $studentExamSum = $ExamFlowModel->studentExamSum($examQueue->exam_id);
        //查询出学生当前已完成的考试
        $ExamFinishStatus = ExamQueue::where('status', '=', 3)->where('student_id', '=', $examQueue->student_id)->count();
        if ($ExamFinishStatus >= $studentExamSum){
            //查询出考试结果
            $examResult = ExamResult::where('student_id','=',$examQueue->student_id)->count();
            if($examResult >= $ExamFinishStatus){
                $testresultModel = new TestResult();
                $score =  $testresultModel->AcquireExam($examQueue->student_id);
                $data = [
                    'code'  =>  6,
                    'title' =>'考试完成，最终总成绩',
                    'score' => $score,
                ];
                return $data;
            }else{
                $data=[
                    'code'      =>  -1,
                    'title'     =>  '当前考站考试已完成',
//                    'surplus'   => 0,
                ];

                return $data;
            }

        }else{
            $data=[
                'code'      =>  -1,
                'title'     =>  '还有考试未完成',
            ];

            return $data;

        }

    }

    //判断腕表提醒状态为0时
    private function getStatusWaitExam($examQueueCollect){
        $items   =   array_where($examQueueCollect,function($key,$value){
            if($value ->status  ==  0)
            {
                return $value;
            }
        });
        $item   =   array_shift($items);

        //判断前面是否有人考试
        $examStudent = ExamQueue::where('room_id', '=', $item->room_id)
            ->whereBetween('status', [1, 2])
            ->count();


        //判断前面等待人数
        $studentnum = $this->getwillStudent($item);
          if($examStudent == 0){

              $willStudents =$studentnum;
          }else{
                $willStudents = $studentnum+1;
          }


        //判断预计考试时间
        $examtimes = date('H:i', (strtotime($item->begin_dt)));
        //判断进入如的考场教室名字
        $examRoomName = $item->room->name;
        if($willStudents>0){

            $data =[
                'code'=> 1,
                'title'=> '考生等待信息',
                'willStudents'=> $willStudents,
                'estTime'=> $examtimes,
                'willRoomName'=> $examRoomName,

            ];
        }
        else
        {
            if(is_null($item->station_id)){
                $data =[
                    'code'=> 3,
                    'title'=> '你将要进入下面教室抽签考试',
                    'willStudents'=> '',
                    'estTime'=> '',
                    'willRoomName'=> '',
                    'roomName'=> $examRoomName,

                ];
            }else{
                $data =[
                    'code'=> 3,
                    'title'=> '你将要进入下面教室抽签考试',
                    'willStudents'=> '',
                    'estTime'=> '',
                    'willRoomName'=> '',
                    'roomName'=> $examRoomName.'-'.$item->station->name,

                ];
            }

        }
        return $data;
   }


    private function getWillStudent($item){
        $studentNum=0;
        $willStudents =  ExamQueue::where('room_id', '=', $item->room_id)
            ->where('exam_screening_id','=',$item->exam_screening_id)
            ->where('station_id','=',$item->station_id)
            ->where('status','=',0)
            ->orderBy('begin_dt', 'asc')
            ->get();

          foreach($willStudents as $key=>$willStudent){
//
              if($willStudent->student_id == $item->student_id){
                  $studentNum=$key;
                  continue;
              }
          }
        \Log::alert($willStudents,$studentNum);

        return $studentNum;
    }



    /**
     * 根据腕表code得到nfc_code
     * @method GET
     * @url /osce/api/student-watch/watch-nfc
     * @access public
     * @param Request $request get请求<br><br>
     * <b>get请求字段：</b>
     * * string     code       (必须的)
     *
     * @return json
     *
     * @version 1.0
     * @author zhouqiang <zhouqiang@misrobot.com>
     * @date
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     *
     */
    public function getWatchNfc(Request $request)
    {
        $this->validate($request, [
            'code' => 'required',
        ]);
        $code = $request->get('code');
        $watchNfc = Watch::where('nfc_code', '=', $code)->first();
        if ($watchNfc) {
            $data = [
                'nfc_code' => $watchNfc->code,
            ];
            return response()->json(
                $this->success_data($data, 1)
            );
        } else {
            $data = [
                'nfc_code' => '',
            ];
            return response()->json(
                $this->success_data($data, -2, '没有找到对应的nfc_code')
            );
        }


    }

}