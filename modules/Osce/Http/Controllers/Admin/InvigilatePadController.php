<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/1/13 0013
 * Time: 11:42
 */

namespace Modules\Osce\Http\Controllers\Admin;


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

class InvigilatePadController extends CommonController
{
    /**
     * @param $files
     * @param $params
     * @param $date
     * @param $testResultId
     * @return static
     * @throws \Exception
     */
    protected static function uploadFileBuilder($files, $date, array $params, $testResultId)
    {
        try {
            //将上传的文件遍历
            foreach ($files as $key => $file) {
                //拼凑文件名字
                $fileName = '';
                //获取文件的MIME类型
                $fileMime = $file->getMimeType();
                foreach ($params as $param) {
                    $fileName .= $param;
                }
                $fileName .= $file->getClientOriginalExtension(); //获取文件名的正式版

                //取得保存路径
                $savePath = public_path('osce/Attach/') . $fileMime . '/' . $date . '/' . $params['student_name'] . '/';
                $savePath = realpath($savePath);

                //如果没有这个文件夹，就新建一个文件夹
                if (!file_exists($savePath)) {
                    mkdir($savePath, 0755, true);
                }

                //将文件放到自己的定义的目录下
                if (!$file->move($savePath, $fileName)) {
                    throw new \Exception('文件保存失败！请重试！');
                }

                //生成附件url地址
                $attachUrl = $savePath . $fileName;

                //将要插入数据库的数据拼装成数组
                $data = [
                    'test_result_id' => $testResultId,
                    'url' => $attachUrl,
                    'type' => $fileMime,
                    'name' => $fileName,
                    'description' => $date . '-' . $params['student_name'],
                ];

                //将内容插入数据库
                if (!$result = TestAttach::create($data)) {
                    if (!Storage::delete($attachUrl)) {
                        throw new \Exception('未能成功保存文件！');
                    }
                    throw new \Exception('附件数据保存失败');
                }
                return $result;
            }
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    /**
     * 身份验证
     * @method GET
     * @url /osce/admin/invigilatepad/authentication
     * @access public
     * @param Request $request get请求<br><br>
     * <b>get请求字段：</b>
     * * string    id      老师id(必须的)
     *
     * @return view
     *
     * @version 1.0
     * @author zhouqiang <zhouqiang@misrobot.com>
     * @date
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */

    public function  getAuthentication(Request $request)
    {
//        dd(222222222);
        $this->validate($request, [
            'id' => 'required|integer'
        ], [
            'id.required' => '请检查PAD是否登陆成功'
        ]);
        $teacher_id = (int)$request->input('id');
        $teacherType =Teacher::where('id','=',$teacher_id)->select('type')->first()->type;
        if($teacherType!==1){
            return response()->json(
                $this->fail(new \Exception('你目前不是监考老师'))
            );
        }else{
            $studentModel = new  Student();
            $studentData = $studentModel->studentList($teacher_id);
//            dd($studentData);
            $list = [];
            foreach ($studentData as $itme) {
                $list[]= [
                    'name' => $itme->name,
                    'code' => $itme->code,
                    'idcard' => $itme->idcard,
                    'mobile' => $itme->mobile
                ];
            }

        }

        dd($list);
        return response()->json(
            $this->success_data($list,1,'验证完成')
        );
    }
    /**
     * 根据考站ID和考试ID获取科目信息(考核点、考核项、评分参考)
     * @method GET
     * @url /osce/admin/invigilatepad/exam-grade
     * @access public
     * @param Request $request get请求<br><br>
     * <b>get请求字段：</b>
     * * int    station_id    考站id   (必须的)
     * * int     exam_id       考试id   (必须的)
     *
     * @return json
     *
     * @version 1.0
     * @author zhouqiang <zhouqiang@misrobot.com>
     * @date
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */


    public function getExamGrade(Request $request,Collection $collection){
      $this->validate($request,[
            'station_id' =>'required|integer',
            //'exam_id'  => 'required|integer'
      ],[
         'station_id.required'=>'没有获取到当前考站',
         'exam_id.required'=>'没有获取到当前考试'
      ]);

        $stationId =$request->get('station_id');
        $examId = $request->get('exam_id');
        //根据考站id查询出下面所有的考试项目
        $station    =   Station::find($stationId);
        //考试标准时间
        $mins = $station->mins;
        $exam =Exam::find($examId);
        $StandardModel  =   new Standard();
        $standardList   =   $StandardModel->ItmeList($station->subject_id);
        dd($standardList);
        if(count($standardList)!=0){
            return response()->json(
        $this->success_data($standardList,1,'数据传送成功')
            );
        }else{
            return response()->json(
                $this->fail(new \Exception('数据查询失败'))
            );

        }

//        echo json_encode($standardList);
//         return response()->json(
//        $this->success_data($data,1,'数据传送成功')
//            );

    }
    /**
     *   * 提交评价
     * @method GET
     * @url /osce/admin/invigilatepad/save-exam-evaluate
     * @access public
     * @param Request $request get请求<br><br>
     * <b>get请求字段：</b>
     * * int     subject_id    考试项目id  (必须的)
     * * int     standard_id  评分标准 id   (必须的)
     * * int     score       根据评分标准所得的分值
     * * string         evaluate     评价内容
     *
     * @return  json
     *
     * @version 1.0
     * @author zhouqiang <zhouqiang@misrobot.com>
     * @date
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function  getSaveExamEvaluate(Request $request,$ExamResultId){
        $this->validate($request,[
            'subject_id' =>'required|integer',
            'standard_id' =>'required|integer',
            'score' =>'required',
            'evaluate'=>'required'
        ],[
            'subject_id.required'=>'请检查考试项目',
            'standard_id.required'=>'请检查评分标准',
            'score.required'=>'请检查评分标准分值',
            'evaluate.required'=>'评价内容',
        ]);
        $data =[
            'subject_id'=>Input::get('subject_id'),
            'standard_id'=>Input::get('standard_id'),
            'score'=>Input::get('score'),
            'evaluate'=>Input::get('evaluate'),
        ];
        $data['exam_result_id'] =$ExamResultId;
        $Save =ExamScore::create($data);
        if($Save){
            return response()->json(
                $this->success_data(1,'评价保存成功')
            );
        }else{
            return response()->json(
                $this->success_data(0,'评价保存失败')
            );
        }

    }

    /**
     * 提交成绩评分详情，考试结果
     * @method GET
     * @url /osce/admin/invigilatepad/save-exam-result
     * @access public
     * @param Request $request get请求<br><br>
     * <b>get请求字段：</b>
     * * string     station_id    考站id   (必须的)
     * @return view
     * @version 1.0
     * @author zhouqiang <zhouqiang@misrobot.com>
     * @date
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */

      public  function postSaveExamResult(Request $request){
           $this->validate($request,[
               'student_id'=>'required|integer',
               'station_id'=>'required|integer',
               'exam_screening_id'=>'required|integer',
               'begin_dt'=>'required|integer',
               'end_dt'=>'required|integer',
               'time'=>'required|integer',
               'score'=>'required|integer',
               'score_dt'=>'required|integer',
               'teacher_id'=>'required|integer',
           ]);
          $data=[
              'station_id'=>Input::get('station_id'),
              'student_id'=>Input::get('student_id'),
              'exam_screening_id'=>Input::get('exam_screening_id'),
              'begin_dt'=>Input::get('begin_dt'),//考试开始时间
              'end_dt'=>Input::get('end_dt'),//考试实际结束时间
              'time'=>Input::get('time'),//考试用时
              'score'=>Input::get('score'),//最终成绩
              'score_dt'=>Input::get('score_dt'),//评分时间
              'teacher_id'=>Input::get('teacher_id'),
          ];
            $TestResultModel  =new TestResult();
            $result= $TestResultModel->addTestResult($data);
          //得到考试结果id
          $ExamResultId =$result->id;
          //考站id
          $stationId =$result->station_id;
          //学生id
          $studentId =$result->student_id;
          //考试场次id
          $ExamScreeningId = $result->exam_screening_id;
          $array = [
              'test_result_id'=>$ExamResultId,
              'station_id'=>$stationId,
              'student_id'=>$studentId,
              'exam_screening_id'=>$ExamScreeningId,
          ];
          //调用照片上传方法，传入数据。
           $this->postTestAttach($request, $array);

          //存入考试评分详情表
          $SaveEvaluate = $this->getSaveExamEvaluate($request,$ExamResultId);

          if($result){
              return response()->json(
                  $this->success_data(1,'详情保存成功')
              );
          }else{
              return response()->json(
                  $this->success_data(0,'详情保存失败')
              );
          }

      }

    /**
     * 照片附件的上传
     * @method POST
     * @url /osce/admin/invigilatepad/save-exam-result
     * @access public
     * @param Request $request get请求<br><br>
     * <b>get请求字段：</b>
     * * string     station_id    考站id   (必须的)
     * @param array $array
     * @return view
     * @throws \Exception
     * @version 1.0
     * @author jiangzhiheng <jiangzhiheng@misrobot.com>
     * @date   2016-01-16  14:33
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function postTestAttach(Request $request, Array $array)
    {
        try {
            //获取考站、考生、和考试关联的id
            list($stationId, $studentId, $examScreenId, $testResultId) = $array;

            //根据ID找到对应的名字
            $studentName = Student::findOrFail($studentId)->first()->name;
            $stationName = Station::findOrFail($stationId)->first()->name;
            $examName = ExamScreening::findOrFail($examScreenId)->ExamInfo->name;

            //将参数拼装成一个数组
            $params = [
                'exam_name' => $examName,
                'student_name' => $studentName,
                'station_name' => $stationName,
            ];
            //获取当前日期
            $date = date('Y-m-d');

            //获取上传的文件,验证文件是否成功上传
            if (!$request->hasFile('photo')) {
                throw new \Exception('上传的照片不存在');
            }

            if ($request->hasFile('radio')) {
                throw new \Exception('上传的音频不存在');
            }

            $photos = $request->file('photo');
            $radios = $request->file('radio');

            //判断照片上传中是否有出错
            if (!$photos->isValid()) {
                throw new \Exception('上传的照片出错');
            }

            if (!$radios->isValid()) {
                throw new \Exception('上传的音频出错');
            }

            //拼装文件名
            $resultPhoto[] = self::uploadFileBuilder($photos, $date, $params, $testResultId);
            $resultRadio[] = self::uploadFileBuilder($radios, $date, $params, $testResultId);

            $result = [$resultPhoto, $resultRadio];
            return $result;

        } catch (\Exception $ex) {
            throw $ex;
        }
    }


    /**
     *  查看现场视屏
     * @method GET
     * @url /osce/admin/invigilatepad/see-exam-evaluate
     * @access public
     * @param Request $request get请求<br><br>
     * <b>get请求字段：</b>
     * * string     station_id    考站id   (必须的)
     *
     * @return view
     *
     * @version 1.0
     * @author zhouqiang <zhouqiang@misrobot.com>
     * @date
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */


      public  function getLocale(Request $request){
            $this->validate($request,[
               'station_id'=>'required|integer'
            ]);
             $stationId = Input::get('station_id');
             $stationvcrModel = new StationVcr();
             $list = $stationvcrModel->vcrlist($stationId);
          $vcrdata= [
              'name'=>$list->name,
              'code'=>$list->code,
              'ip'=>$list->ip,
              'username'=>$list->username,
              'port'=>$list->port,
              'channel'=>$list->channel,
          ];
          if($list->status==0){
              return response()->json(
                  $this->success_data(0,'摄像头损坏')
              );
          }else{
              return response()->json(
                  $this->success_data($vcrdata,1,'摄像头可用')
              );
          }
          
      }








//    url   /osce/admin/invigilatepad/wait_exam

    protected $timeDiff = 120;
    const EXAM_TAKING = 1; // 考试中
    const EXAM_BEFORE = 2;// 待考
    const EXAM_JUST_AFTER = 3;// 刚考完,下一场提示 考试完成后两分钟内提醒
    const EXAM_WILL_BEGIN = 4; //将要开始 考试开始两分钟内提醒

    public  function getWaitExam(Request $request){
        $this->validate($request,[
            'watch_id'=>'required|integer'
        ]);
        $watchId=$request->input('watch_id');

        $watchStudent= WatchLog::where('watch_id','=',$watchId)->select('student_id')->first();
        if(!$watchStudent){
            return response()->json(
                $this->fail(new \Exception('没有找到学生的腕表信息'))
            );
        }
        //查到该学生的所有考试
//        dd($watchStudent);

        $ExamQueueModel= new ExamQueue();
        $result =  $ExamQueueModel->StudentExamInfo($watchStudent->student_id);
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
            //待考信息
             if($key<3000 && $key>200){
                 $status = self::EXAM_BEFORE;
                 $curExam = $item;
                 break;
             }
            //开考前通知
            if ($key>0 && ($this->timeDiff-$key) >0 ) {
                $status = self::EXAM_WILL_BEGIN;
                $curExam = $item;
                break;
            }

          // 考试中提醒时间
            if ( $itemStart <= $time &&  $itemEnd >= $time) {
                $status = self::EXAM_TAKING;
                $curExam = $item;
                break;
            }
             //考试完成通知下一场
            // self::EXAM_JUST_AFTER
            if ( $endDiff > 0 && $endDiff < $this->timeDiff) {
                $status = self::EXAM_JUST_AFTER;
                $curKey = $key;
            }
            $list[$key] = $item;
        }
        //对考试时间排序
        ksort($list);
        switch ($status) {
            case self::EXAM_BEFORE:
                $willStudents = ExamQueue::where('room_id','=',$curExam['room_id'])
                    ->whereBetween('status',[1,2])
                    ->count();
                dump('前面还有'.($willStudents-1));
                break;

            case self::EXAM_WILL_BEGIN:
                dump('你即将开始考试'.$curExam['room_name']);


                return response()->json(
                    $this->success_data($curExam['room_name'],'你即将开始考试')
                );
                // todo ..
                break;
            case self::EXAM_TAKING:

                $surplus = $curExam['end_time'] - $time;
                $surplus = floor($surplus/60) . ':' . $surplus%60;
                $changeStatus= ExamQueue::where('id','=',$curExam['id'])->update(['status'=>2]);

                dump('当前考试剩余时间'.$surplus);
                return response()->json(
                    $this->success_data($surplus,'当前考试剩余时间')
                );
                break;
            case self::EXAM_JUST_AFTER:
                //dump('考试已完成');
                $studentStatus= ExamQueue::where('student_id','=',$watchStudent->student_id)
                    ->whereBetween('status',[1,2])
                    ->count();
                if($studentStatus==0){
                     dump('考试已完成');
                }else{
                    foreach ($list as $key => $item) {
                        if ($curKey == $key) {
                            $nextExam = current($list);
                        }
                    }
                }
                // todo ..
                break;
        }
    dd($nextExam ,$curExam);
        //dump($list);die;

    }





    public function nowQueue($examQueueCollect,$nowTime)
    {
        foreach ($examQueueCollect as $examQueue) {
            if(strtotime($examQueue->begin_dt) > $nowTime){
                     return $examQueue;
                 }
            if (strtotime($examQueue->begin_dt) < $nowTime && strtotime($examQueue->end_dt) > $nowTime)
            {
                return $examQueue;
            }
        }
        return [];
    }
    public function nextQueue($examQueueCollect,$nowTime){
        $nowQueue   =   $this->nowQueue($examQueueCollect,$nowTime);
//        dd($nowQueue);
        $queueLeave =   [];
        foreach ($examQueueCollect as $examQueue)
        {

            if(strtotime($examQueue->begin_dt)>strtotime($nowQueue->end_dt))
            {
                $queueLeave[$examQueue->begin_dt]   =   $examQueue;
            }
        }
        ksort($queueLeave);
        return array_shift($queueLeave);
    }
//    public function getCheckStatus(){
//        $this->nowQueue();
//    }

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
        $nowQueue   =   $this->nowQueue($examQueueCollect,$nowTime);
        if(empty($nowQueue))
        {
        //当前无考试
            return response()->json(
                $this->success_data('当前无考试')
            );
        }
        else
        {
            //            待考/开考通知
            if(strtotime($nowQueue->begin_dt)-$nowTime > 200){
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
            }elseif(strtotime($nowQueue->begin_dt)-$nowTime <= 200 && strtotime($nowQueue->begin_dt)-$nowTime>0) {
                $examRoomName=$nowQueue->room_name;
                return response()->json(
                    $this->success_data($examRoomName,'考生开考通知')
                );
            }

            $nextQueue  =   $this->nextQueue($examQueueCollect,$nowTime);
            if(empty($nextQueue))
            {
                //考完了
                dd(22222);

                return response()->json(
                    $this->success_data('考试完成')
                );
            }
            else
            {
//
                //当前剩余考试时间
                $surplus = strtotime($nowQueue['end_dt']) - $nowTime;
                $surplus = floor($surplus/60) . ':' . $surplus%60;
                dump('当前考试剩余时间'.$surplus);

            }
        }


    }

}