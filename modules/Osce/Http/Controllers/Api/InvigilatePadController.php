<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/1/13 0013
 * Time: 11:42
 */

namespace Modules\Osce\Http\Controllers\Api;


use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Input;
use Modules\Osce\Entities\ExamFlow;
use Modules\Osce\Entities\Exam;
use Modules\Osce\Entities\ExamQueue;
use Modules\Osce\Entities\ExamResult;
use Modules\Osce\Entities\ExamScore;
use Modules\Osce\Entities\ExamScreening;
use Modules\Osce\Entities\Standard;
use Modules\Osce\Entities\Station;
use Modules\Osce\Entities\StationVcr;
use Modules\Osce\Entities\StationVideo;
use Modules\Osce\Entities\Student;
use Modules\Osce\Entities\TestAttach;
use Modules\Osce\Entities\Teacher;
use Modules\Osce\Entities\TestResult;
use Modules\Osce\Entities\WatchLog;
use Modules\Osce\Http\Controllers\CommonController;
use DB;
use Storage;
use Auth;
use Symfony\Component\HttpKernel\Tests\DataCollector\DumpDataCollectorTest;

class InvigilatePadController extends CommonController
{


//    测试
// url    /osce/api/invigilatepad/test-index
    public function getTestIndex(){
        return view('osce::test.test');
    }





    /**
     * @param $file
     * @param $date
     * @param array $params
     * @param $standardId
     * @return static
     * @throws \Exception
     * @internal param $files
     * @internal param $testResultId
     */
    protected static function uploadFileBuilder($file, $date, array $params, $standardId)
    {
        try {
            //将上传的文件遍历

                //拼凑文件名字
                $fileName = '';
                //获取文件的MIME类型
                $fileMime = $file->getMimeType();
                foreach ($params as $param) {
                    $fileName .= $param . '_';
                }
                $fileName .= mt_rand() .'.'. $file->getClientOriginalExtension(); //获取文件名的正式版
                //取得保存路径
                $savePath = 'osce/Attach/' . $fileMime . '/' . $date . '/' . $params['student_name'] . '_' . $params['student_code'] . '/';
                $savePath = public_path($savePath) ;
                $savePath = iconv("UTF-8","gb2312",$savePath);
                //如果没有这个文件夹，就新建一个文件夹
                if (!file_exists($savePath)) {
                    mkdir($savePath, 0755, true);
                }

                //将文件放到自己的定义的目录下
                $file->move($savePath, iconv("UTF-8","gb2312",$fileName));

                //生成附件url地址
                $attachUrl = $savePath . $fileName;
                //将要插入数据库的数据拼装成数组
                $data = [
                    'test_result_id' => NULL,
                    'url' => $attachUrl,
                    'type' => $fileMime,
                    'name' => $fileName,
                    'description' => $date . '-' . $params['student_name'],
                    'standard_id' => $standardId
                ];

                //将内容插入数据库
                if (!$result = TestAttach::create($data)) {
                    if (!Storage::delete($attachUrl)) {
                        throw new \Exception('未能成功保存文件！');
                    }
                    throw new \Exception('附件数据保存失败');
                }
                return $result;

        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    /**
     * 身份验证
     * @method GET
     * @url /osce/api/invigilatepad/authentication
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
        $this->validate($request, [
            'station_id' => 'required|integer'
        ], [
            'station_id.required' => '考站id必须'
        ]);
           $stationId=  (int)$request->input('station_id');
            $studentModel = new  Student();
            $studentData = $studentModel->studentList($stationId);
             if($studentData){
                 return response()->json(
                     $this->success_data($studentData,1,'验证完成')
                 );
             }else{
                 return response()->json(
                     $this->fail(new \Exception('学生信息查询失败'))
                 );
             }


    }
    /**
     * 根据考站ID和考试ID获取科目信息(考核点、考核项、评分参考)
     * @method GET
     * @url /osce/api/invigilatepad/exam-grade
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
//            'exam_id'  => 'required|integer'
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
    }
    /**
     *   * 提交评价
     * @method GET
     * @url /osce/api/invigilatepad/save-exam-evaluate
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
     public function  postSaveExamEvaluate(Request $request,$ExamResultId){


        $this->validate($request,[
            'subject_id' =>'required|integer',
            'standard_id' =>'required|integer',
            'score' =>'required',
        ],[
            'subject_id.required'=>'请检查考试项目',
            'standard_id.required'=>'请检查评分标准',
            'score.required'=>'请检查评分标准分值',
        ]);
        $data =[
            'subject_id'=>Input::get('subject_id'),
            'standard_id'=>Input::get('standard_id'),
            'score'=>Input::get('score'),
        ];

        $data['exam_result_id'] =$ExamResultId;
        $Save =ExamScore::create($data);
         return $Save;
    }

    /**
     * 提交成绩评分详情，考试结果
     * @method post
     * @url /osce/api/invigilatepad/save-exam-result
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
              'exam_screening_id'=>'required',
              'begin_dt'=>'required',
              'end_dt'=>'required',
//              'time'=>'required',
              'scores'=>'required|integer',
              'score_dt'=>'required',
              'teacher_id'=>'required|integer',
              'evaluate'=>'required'
          ]);
           //得到用时
          $times =Input::get('end_dt')-Input::get('begin_dt');
           $time =$times/60;

          $data   =   [
          'station_id'=>Input::get('station_id'),
          'student_id'=>Input::get('student_id'),
          'exam_screening_id'=>Input::get('exam_screening_id'),
          'begin_dt'=>Input::get('begin_dt'),//考试开始时间
          'end_dt'=>Input::get('end_dt'),//考试实际结束时间
          'time'=>$time,//考试用时
          'score'=>Input::get('scores'),//最终成绩
          'score_dt'=>Input::get('score_dt'),//评分时间
          'teacher_id'=>Input::get('teacher_id'),
          'evaluate'=>Input::get('evaluate'),//评价内容
          'operation'=>Input::get('operation'),//操作的连贯性
          'skilled'=>Input::get('skilled'),//工作的娴熟度
          'patient'=>Input::get('patient'),//病人关怀情况
          'affinity'=>Input::get('affinity'),//沟通亲和能力

        ];

          //根据考生id获取到考试id
          $ExamId=Student::where('id', '=', $data['student_id'])->select('exam_id')->first();


          //根据考试获取到考试流程
          $ExamFlowModel = new  ExamFlow();
          $studentExamSum = $ExamFlowModel->studentExamSum($ExamId->exam_id);
          //查询出学生当前已完成的考试
          $ExamFinishStatus = ExamQueue::where('status', '=', 3)->where('student_id', '=', $ExamId)->count();


        try{
            if($ExamFinishStatus == $studentExamSum){
                //todo 调用zhoufuxiang接口......
                try{
                $examResultModel= new ExamResult();
                $examResultModel->examResultPush($data['student_id']);
                }catch (\Exception $mssge) {
                    \Log::alert($mssge->getMessage().';'.$data['student_id'].'成绩推送失败');
                }
            }

               $TestResultModel  =new TestResult();
               $result= $TestResultModel->addTestResult($data);

               if($result){
                   //得到考试结果id
                   $testResultId =$result->id;
                   //考站id
                   $stationId =$result->station_id;
                   //学生id
                   $studentId =$result->student_id;
                   //考试场次id
                   $examScreenId = $result->exam_screening_id;
                   //根据考试附件结果id修改表里的考试结果id
                   //  todo 待最后确定。。。。。。。。
                   //存入考试 评分详情表

                   $SaveEvaluate = $this->postSaveExamEvaluate($request,$testResultId);
                   if(!$SaveEvaluate){
                       return response()->json(
                           $this->fail(new \Exception('成绩推送失败'))
                       );
                   }else{
                       return response()->json(
                           $this->success_data('',1,'成绩保存成功')
                       );

                   }

               }else{
                   return response()->json(
                       $this->fail(new \Exception('成绩推送失败'))
                   );

               }
           } catch (\Exception $ex) {
               throw $ex;
           }
      }

    /**
     * 照片附件的上传
     * @method POST
     * @url /osce/api/invigilatepad/save-exam-result
     * @access public
     * @param Request $request get请求<br><br>
     * <b>get请求字段：</b>
     * @param $stationId
     * @param $studentId
     * @param $examScreenId
     * @param $testResultId
     * @return view
     * @throws \Exception
     * @internal param $timeAnchors
     * @internal param array $array
     * @version 1.0
     * @author jiangzhiheng <jiangzhiheng@misrobot.com>
     * @date   2016-01-16  14:33
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function postTestAttachImage(Request $request)
    {
        try {
            //获取数据
            $studentId = $request->input('student_id');
            $stationId = $request->input('station_id');
            $exam = Exam::where('status',1)->first();
            $standardId = $request->input('standard_id');

            //根据ID找到对应的名字
            $student = Student::findOrFail($studentId)->first();
            $studentName = $student->name;
            $studentCode = $student->code;
            $stationName = Station::findOrFail($stationId)->first()->name;
            $examName = $exam->name;


            //将参数拼装成一个数组
            $params = [
                'exam_name' => $examName,
                'student_name' => $studentName,
                'student_code' => $studentCode,
                'station_name' => $stationName,
            ];
            //获取当前日期
            $date = date('Y-m-d');

            //获取上传的文件,验证文件是否成功上传
            if (!$request->hasFile('photo')) {
                throw new \Exception('上传的照片不存在');
            } else {
                $photos = $request->file('photo');
                //判断照片上传中是否有出错
                if (!$photos->isValid()) {
                    throw new \Exception('上传的照片出错');
                }

                //拼装文件名,并插入数据库
                $result = self::uploadFileBuilder($photos, $date, $params, $standardId);
            }
            return response()->json($this->success_data([$result->id]));

        } catch (\Exception $ex) {
            return response()->json($this->fail($ex));
        }
    }

    /**
     * 音频附件的上传
     * @method POST
     * @url /osce/api/invigilatepad/save-exam-result
     * @access public
     * @param Request $request get请求<br><br>
     * <b>get请求字段：</b>
     * @return view
     * @internal param $stationId
     * @internal param $studentId
     * @internal param $examScreenId
     * @internal param $testResultId
     * @internal param $timeAnchors
     * @internal param array $array
     * @version 1.0
     * @author jiangzhiheng <jiangzhiheng@misrobot.com>
     * @date   2016-01-16  14:33
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function postTestAttachRadio(
        Request $request
    ) {
        try {
            //获取数据
            $studentId = $request->input('student_id');
            $stationId = $request->input('station_id');
            $exam = Exam::where('status',1)->first();
            $standardId = $request->input('standard_id');

            //根据ID找到对应的名字
            $student = Student::findOrFail($studentId)->first();
            $studentName = $student->name;
            $studentCode = $student->code;
            $stationName = Station::findOrFail($stationId)->first()->name;
            $examName = $exam->name;


            //将参数拼装成一个数组
            $params = [
                'exam_name' => $examName,
                'student_name' => $studentName,
                'student_code' => $studentCode,
                'station_name' => $stationName,
            ];

            //获取当前日期
            $date = date('Y-m-d');

            if (!$request->hasFile('radio')) {
                throw new \Exception('上传的音频不存在');
            } else {
                $radios = $request->file('radio');

                if (!$radios->isValid()) {
                    throw new \Exception('上传的音频出错');
                }

                $result = self::uploadFileBuilder($radios, $date, $params, $standardId);
            }

            return response()->json($this->success_data([$result->id]));
        } catch (\Exception $ex) {
            return response()->json($this->fail($ex));
        }
    }

    /**
     * 将视频锚点插进数据库
     * @param Request $request
     * @throws \Exception
     * @author Jiangzhiheng
     */
    public function postStoreAnchor(Request $request)
    {

        //将视频的锚点信息保存进数据库，因为可能有很多条，所以用foreach
        $stationId = $request->input('station_id');
        $studentId = $request->input('student_id');
        $examScreenId = $request->input('exam_screen_id');
        $timeAnchors = $request->input('time_anchors');

        $this->storeAnchor( $stationId, $studentId, $examScreenId,$timeAnchors);
    }

    /**
     * @author Jiangzhiheng
     * @param $stationId
     * @param $studentId
     * @param array $timeAnchors
     * @return bool
     * @throws \Exception
     */
    private function storeAnchor($stationId, $studentId, $examScreenId, array $timeAnchors) {
        try {
            $user = Auth::user();
            if (empty($user)) {
                throw new \Exception('当前用户未登陆');
            }

            //获得站点摄像机关联表
            $stationVcr = StationVcr::where('station_id',$stationId)->first();
            if (empty($stationVcr)) {
                throw new \Exception('该考站未关联摄像机');
            }

            //获取考试
            $exam = ExamScreening::findOrFail($examScreenId);

            foreach ($timeAnchors as $timeAnchor) {
                //拼凑数组
                $data = [
                    'station_vcr_id' => $stationVcr->id,
                    'begin_dt' => $timeAnchor,
                    'end_dt' => $timeAnchor,
                    'created_user_id' => $user->id,
                    'exam_id' => $exam->id,
                    'student_id' => $studentId,
                ];

                //将数据插入库
                if (!StationVideo::create($data)) {
                    throw new \Exception('保存失败！请重试！');
                }
            }

            return true;
        } catch (\Exception $ex) {
            throw $ex;
        }

    }


    /**
     *  查看现场视屏
     * @method GET
     * @url /osce/api/invigilatepad/see-exam-evaluate
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
                  $this->success_data('',0,'摄像头损坏')
              );
          }else{
              return response()->json(
                  $this->success_data($vcrdata,1,'摄像头可用')
              );
          }

      }

    /**
     *  开始考试
     * @method GET
     * @url /osce/api/invigilatepad/start-exam
     * @access public
     * @param Request $request get请求<br><br>
     * <b>get请求字段：</b>
     * * string     student_id    学生id   (必须的)
     *
     * @return json
     *
     * @version 1.0
     * @author zhouqiang <zhouqiang@misrobot.com>
     * @date
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function getStartExam(Request $request){

        $this->validate($request,[
            'student_id'=>'required|integer',
//            'start_time'=>'required|integer',
            'station_id'=>'required|integer'

        ],[
            'student_id.required'=>'考生编号信息必须',
            'station_id.required'=>'考站编号信息必须'
        ]);
              $nowTime    =   time();
              $nowTimes =strtotime($nowTime);

        $studentId= $request->get('student_id');
        $stationId= $request->get('station_id');
//        $StartTime= Input::get('start_time');
        $ExamQueueModel= new ExamQueue();
        $AlterResult  =  $ExamQueueModel->AlterTimeStatus($studentId ,$stationId,$nowTime);


        if($AlterResult){
            return response()->json(
                $this->success_data($nowTimes,1,'开始考试成功')
            );
        }
        return response()->json(
            $this->fail(new \Exception('请再次核对考生信息后再试!!!'))
        );
    }

    /**
     *  结束考试
     * @method GET
     * @url /osce/api/invigilatepad/end-exam
     * @access public
     * @param Request $request get请求<br><br>
     * <b>get请求字段：</b>
     * * string     student_id    学生id   (必须的)
     *
     * @return json
     *
     * @version 1.0
     * @author zhouqiang <zhouqiang@misrobot.com>
     * @date
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */

    public function getEndExam(Request $request){
        $this->validate($request,[
            'student_id'=>'required|integer',
            'station_id'=>'required|integer'

        ],[
            'student_id.required'=>'考生编号信息必须',
            'station_id.required'=>'考站编号信息必须'
        ]);

        $studentId= Input::get('student_id');
        $stationId= Input::get('station_id');
        $nowTime    =   time();
        $nowTimes =strtotime($nowTime);



        $ExamQueueModel= new ExamQueue();
        $EndResult  =  $ExamQueueModel->EndExamAlterStatus($studentId,$stationId ,$nowTime);
        if($EndResult){
            return response()->json(
                $this->success_data($nowTimes,1,'结束考试成功')
            );
        }
        return response()->json(
            $this->fail(new \Exception('请再次核对考生信息后再试!!!'))
        );

    }




}