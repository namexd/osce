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






    //    url   /osce/watch/studentwatch/wait_exam

    protected $timeDiff = 120;

    const EXAM_BEFORE = 0;// 待考
    const EXAM_WILL_BEGIN = 1; //将要开始 考试开始两分钟内提醒
    const EXAM_TAKING = 2; // 考试中
    const EXAM_JUST_AFTER = 3;// 刚考完,下一场提示 考试完成后两分钟内提醒


    public  function getWaitExam(Request $request){

        dd(222222);


        $this->validate($request,[
            'watch_id'=>'required|integer'
        ]);
        $watchId=$request->input('watch_id');

        $watchStudent= WatchLog::where('watch_id','=',$watchId)->select('student_id')->first()->student_id;
        //查到该学生的所有考试
//        dd($watchStudent);

        $ExamQueueModel= new ExamQueue();
        $result =  $ExamQueueModel->StudentExamInfo($watchStudent);
        dump($result);

        //获取到当前时间;
        $time = time();
        $status = self::EXAM_BEFORE;
        $curExam = $nextExam =  null;
        $list=[];

        foreach($result as $item){
            $itemStart = $item['begin_time'] = strtotime($item['begin_dt']);
            $itemEnd   = $item['end_time'] = strtotime($item['end_dt']);
            $diff = $itemEnd - $itemStart;
            $key = $itemStart - $time;
            $endDiff = $time - $itemEnd;

            //
            if ($key<0 && ($this->timeDiff+$key) > 0 ) {
                $status = self::EXAM_WILL_BEGIN;
                $curExam = $item;
                break;
            }

            if ( $itemStart <= $time &&  $itemEnd >= $time) {
                $status = self::EXAM_TAKING;
                $curExam = $item;
                break;
            }

            // self::EXAM_JUST_AFTER
            if ( $endDiff > 0 && $endDiff < $this->timeDiff ) {
                $status = self::EXAM_JUST_AFTER;
                $curKey = $key;
            }

            $list[$key] = $item;
        }

        ksort($list);


        switch ( $status ) {
            case self::EXAM_BEFORE:
                break;
            case self::EXAM_WILL_BEGIN:
                $curExam['room_id'];
                // todo ..
                break;
            case self::EXAM_TAKING:
                $surplus = $curExam['end_time'] - $time;
                $surplus = floor($surplus/60) . ':' . $surplus%60;
                return response()->json([

                ]);
                break;
            case self::EXAM_JUST_AFTER:
                foreach ($list as $key => $item) {
                    if ($curKey == $key) {
                        $nextExam = current($list);
                    }
                }
                $nextExam['room_id'];
                // todo ..
                break;
        }

        dump($list);die;

    }


}