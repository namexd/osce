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
        //  根据腕表id找到对应的考试场次和学生

        $watchStudent = ExamScreeningStudent::where('watch_id', '=', $watchId->id)->where('is_end', '=', 0)->select('student_id', 'exam_screening_id')->first();
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

        dump($examQueueCollect);
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
            return $this->getStatusOne($examQueueCollect);
        }
        if(in_array(2,$statusArray))
        {
            return $this->getStatusTwo($examQueueCollect);
        }
        if(in_array(3,$statusArray))
        {
            //return $this->getStatusThree
        }
        //return $this->

    }
    //判断腕表提醒状态为1时
    private function getStatusOne($examQueueCollect){
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
            'title'=>'将要考试进入考站',
            'roomName'=>$station->name.'-'.$room->name,
        ];
        return $data;
    }


    //判断腕表提醒状态为2时

   private function getStatusTwo($examQueueCollect){
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
        $surplus = strtotime($items->end_dt) - strtotime($items->begin_dt);
            $data=[
            'code'=>4,
            'title'=>'当前考站剩余时间',
            'surplus'=>$surplus,
           ];

           return $data;

    }

    private function getStatusThree($examQueueCollect){

        $items   =   array_where($examQueueCollect,function($key,$value){
            if($value ->status  ==  3)
            {
                return $value;
            }
        });
        $item   =   array_pop($items);
        if(is_null($item)){
            throw new \Exception('队列异常');
        }
        $data=[
            'code'=>4,
            'title'=>'当前考站剩余时间',
        ];

        return $data;


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