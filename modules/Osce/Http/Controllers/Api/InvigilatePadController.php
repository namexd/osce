<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/1/13 0013
 * Time: 11:42
 */

namespace Modules\Osce\Http\Controllers\Api;


use App\Repositories\Common;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Input;
use Modules\Osce\Entities\ExamAbsent;
use Modules\Osce\Entities\ExamFlow;
use Modules\Osce\Entities\Exam;
use Modules\Osce\Entities\ExamPlan;
use Modules\Osce\Entities\ExamQueue;
use Modules\Osce\Entities\ExamResult;
use Modules\Osce\Entities\ExamScore;
use Modules\Osce\Entities\ExamScreening;
use Modules\Osce\Entities\ExamScreeningStudent;
use Modules\Osce\Entities\Standard;
use Modules\Osce\Entities\StandardItem;
use Modules\Osce\Entities\Station;
use Modules\Osce\Entities\StationVcr;
use Modules\Osce\Entities\StationVideo;
use Modules\Osce\Entities\Student;
use Modules\Osce\Entities\TestAttach;
use Modules\Osce\Entities\Teacher;
use Modules\Osce\Entities\TestResult;
use Modules\Osce\Entities\Watch;
use Modules\Osce\Entities\WatchLog;
use Modules\Osce\Http\Controllers\CommonController;
use DB;
use Storage;
use Auth;
use Redis;
use Symfony\Component\HttpKernel\Tests\DataCollector\DumpDataCollectorTest;
use Modules\Osce\Entities\QuestionBankEntities\ExamMonitor;
class InvigilatePadController extends CommonController
{


//    测试
// url    /osce/api/invigilatepad/test-index
    public function getTestIndex()
    {
        $info = array('coffee', 'brown', 'caffeine');

// 列出所有变量
        list($drink, $color, $power) = $info;
        echo "$drink is $color and $power makes it special.\n";

// 列出他们的其中一个
        list($drink, , $power) = $info;
        echo "$drink has $power.\n";

// 或者让我们跳到仅第三个
        list( , , $power) = $info;
        echo "I need $power!\n";

// list() 不能对字符串起作用
        list($bar) = "abcde";
        var_dump($bar); // NULL
//        $examScreeningModel = new ExamScreening();
//        $result = $examScreeningModel->getExamCheck();
//        $numbers = array('1','2','3','4','5');
////        srand((float)microtime()*1000000);
//        shuffle($numbers);
//
//        while (list(,$number) = each($numbers)) {
//            echo $number."<br/>";
//        }
//
        for ($i = 2; $i <= 5; $i++)
        {
            print "value is now " . $i . "<br>";
        }
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
    protected static function uploadFileBuilder($type, $file, $date, array $params, $standardId,$studentId)
    {
        try {
            //将上传的文件遍历

            //拼凑文件名字
            $fileName = time().'_'.mt_rand(0,99999) . '_';
            //获取文件的MIME类型
//            $fileMime = $file->getMimeType();
//            foreach ($params as $param) {
//                $fileName .= $param . '_';
//            }
            $fileName .= '_'.mt_rand() . '.' . $file->getClientOriginalExtension(); //获取文件名的正式版
            //取得保存路径
            $savePath = 'osce/Attach/' . $type . '/' . $date . '/' . $params['student_name'] . '_' . $params['student_code'] . '/';
//            $savePath = 'osce/Attach/' . $fileMime . '/' . $date . '/' . 13 . '_' . 13 . '/';
//            $savePath = public_path($savePath);
            //TODO iconv用在windows环境下
//            $savePath = iconv("UTF-8", "gb2312", $savePath);
            //如果没有这个文件夹，就新建一个文件夹
            if (!file_exists($savePath)) {
                mkdir($savePath, 0755, true);
            }
            //将文件放到自己的定义的目录下 TODO iconv用在windows环境下
//            $file->move($savePath, iconv("UTF-8", "gb2312", $fileName));
            $file->move($savePath, $fileName);
            //生成附件url地址
            $attachUrl = urldecode($savePath . $fileName);
            //将要插入数据库的数据拼装成数组
            $data = [
                'test_result_id' => null,
                'url' => $attachUrl,
                'type' => $type,
                'name' => $fileName,
                'description' => $date . '-' . $params['student_name'],
                'standard_id' => $standardId,
                 'student_id'=>$studentId,
            ];

            //将内容插入数据库
            if (!$result = TestAttach::create($data)) {
                if (!Storage::delete($attachUrl)) {
                    throw new \Exception('未能成功保存文件！', -140);
                }
                throw new \Exception('附件数据保存失败', -150);
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

    public function getAuthentication(Request $request)
    {
        $this->validate($request, [
            'station_id' => 'required|integer',
            'teacher_id' => 'required|integer'
        ], [
            'station_id.required' => '考站编号必须',
            'teacher_id.required' => '老师编号必须'
        ]);

        try {
            $redis = Redis::connection('message');
            $stationId = (int)$request->input('station_id');
            $teacher_id = (int)$request->input('teacher_id');
            $exam = Exam::doingExam();
            $studentModel = new  Student();
            $studentData = $studentModel->studentList($stationId, $exam,$teacher_id);
            if ($studentData['nextTester']) {
                $studentData['nextTester']->avator = asset($studentData['nextTester']->avator);
                $redis->publish('pad_message', json_encode($this->success_data($studentData['nextTester'], 1, '验证完成')));
                return response()->json(
                    $this->success_data($studentData['nextTester'], 102, '验证完成')
                );
            } else {
                $redis->publish('pad_message', json_encode($this->success_data([], -2, '学生信息查询失败')));
                throw new \Exception('学生信息查询失败', -2);
            }
        } catch (\Exception $ex) {
            return response()->json(
                $this->fail($ex)
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


    public function getExamGrade(Request $request)
    {
        try {

            $this->validate($request, [
                'station_id' => 'required|integer',
            ], [
                'station_id.required' => '没有获取到当前考站',
            ]);

            $stationId = $request->get('station_id');

            $examId = $request->get('exam_id');
            //根据考站id查询出下面所有的考试项目
            $station = Station::find($stationId);

            //考试标准时间
            $mins = $station->mins;
            $exam = Exam::find($examId);
            $standardItemModel = new StandardItem();
            $standardItemList = $standardItemModel->getSubjectStandards($station->subject_id);
            if (count($standardItemList) != 0) {
                return response()->json(
                    $this->success_data($standardItemList, 1, '数据传送成功')
                );

            } else {
                return response()->json(
                    $this->fail(new \Exception('数据查询失败'))
                );
            }
        } catch (\Exception $ex) {
            \Log::alert($ex->getMessage());
        }
    }

    /**
     * 提交成绩评分详情，考试结果
     * @method post
     * @url /osce/api/invigilatepad/save-exam-result
     * @access public
     * @param Request $request post请求<br><br>
     * <b>get请求字段：</b>
     * * string     station_id    考站id   (必须的)
     * @return view
     * @version 1.0
     * @author zhouqiang <zhouqiang@misrobot.com>
     * @date
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     * upload_document_id 音频 图片id集合
     */

    public function postSaveExamResult(Request $request)
    {

        try {

            $this->validate($request, [
                'score' => 'required',
                'student_id' => 'required',
                'station_id' => 'required',
                'exam_screening_id' => 'required',
//                'begin_dt' => 'required',
//                'end_dt' => 'required',
                'teacher_id' => 'required',
            ], [
                'score.required' => '请检查评分标准分值',
            ]);
            $score = Input::get('score');
            $stationId = Input::get('station_id');
            $studentId = Input::get('student_id');
            $examScreeningId = Input::get('exam_screening_id');
            //到队列表里查询出学生的开始和结束时间
            $studentExamTime = ExamQueue::where('station_id', '=', $stationId)
                ->where('student_id', '=', $studentId)
                ->where('exam_screening_id', '=', $examScreeningId)
                ->first();
            if (is_null($studentExamTime)) {
                throw new \Exception('没有查询到该学生队列', -100);
            }
            $useTime = strtotime($studentExamTime->end_dt) - strtotime($studentExamTime->begin_dt);
//            getMinutes
            $data = [
                'station_id' => $stationId,//考站编号
                'examinee_id' => $studentId,//考生编号
                'exam_screening_id' => $examScreeningId,//场次编号
                'begin_dt' => $studentExamTime->begin_dt,//考试开始时间
                'end_dt' => $studentExamTime->end_dt,//考试实际结束时间
                'time' => $useTime,//考试用时
                'score_dt' => Input::get('end_dt'),//评分时间
                'teacher_id' => Input::get('teacher_id'),
                'evaluate' => Input::get('evaluate'),//评价内容
                'operation' => Input::get('operation'),//操作的连贯性
                'skilled' => Input::get('skilled'),//工作的娴熟度
                'patient' => Input::get('patient'),//病人关怀情况
                'affinity' => Input::get('affinity'),//沟通亲和能力/

            ];
            //根据考生id获取到考试id
            $ExamId = Student::where('id', '=', $data['student_id'])->select('exam_id')->first();
            //根据考试获取到考试流程
            $ExamFlowModel = new  ExamFlow();
            $studentExamSum = $ExamFlowModel->studentExamSum($ExamId->exam_id);
            //查询出学生当前已完成的考试
            $ExamFinishStatus = ExamQueue::where('status', '=', 3)->where('student_id', '=',
                $data['student_id'])->count();


            if ($ExamFinishStatus == $studentExamSum) {
                //todo 调用zhoufuxiang接口......
                try {
                    $examResultModel = new ExamResult();

                    $examResultModel->examResultPush($data['student_id'], $data['exam_screening_id']);

                } catch (\Exception $mssge) {
                    \Log::alert($mssge->getMessage() . ';' . $data['student_id'] . '成绩推送失败');
                }
            }
            $TestResultModel = new TestResult();
            $result = $TestResultModel->addTestResult($data, $score);

//                \Log::alert(json_encode($result));

            if ($result) {
                //修改exam_attach表里的结果id
                return response()->json($this->success_data([], 1, '成绩提交成功'));
            }
        } catch (\Exception $ex) {
            \Log::alert($ex->getMessage());
            return response()->json(
                $this->fail(new \Exception('成绩提交失败'))
            );
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
            $this->validate($request, [
                'student_id' => 'required|integer',
                'station_id' => 'required|integer',
                'standard_id' => 'required|integer'
            ]);
            //获取数据
            $studentId =  $request->input('student_id');
            $stationId = $request->input('station_id');
            $standardId = $request->input('standard_id');
            $exam = Exam::where('status', 1)->first();
            if (is_null($exam)) {
                throw new \Exception('当前没有正在进行的考试！', -701);
            }

            //根据ID找到对应的名字
            $student = Student::find($studentId);
            if (is_null($student)) {
                throw new \Exception('当前找不到指定的学生！', -1);
            }
            $studentName = $student->name;
            $studentCode = $student->code;
            $station = Station::find($stationId);
            if (is_null($station)) {
                throw new \Exception('当前找不到指定的考站！', -1);
            }
            $examName = $exam->name;

            //将参数拼装成一个数组
            $params = [
                'exam_name' => $examName,
                'student_name' => $studentName,
                'student_code' => $studentCode,
                'station_name' => $station->name,
            ];
            //获取当前日期
            $date = date('Y-m-d');

            //设定当前文件类型
            $type = 'image';

            //获取上传的文件,验证文件是否成功上传
            if (!$request->hasFile('photo')) {
                throw new \Exception('上传的照片不存在', -100);
            } else {
                $photos = $request->file('photo');
                //判断照片上传中是否有出错
                if (!$photos->isValid()) {
                    throw new \Exception('上传的照片出错', -110);
                }
                //判断照片类型是否不对
                if (!Common::imageMimeCheck($photos)) {
                    throw new \Exception('上传的文件类型不合法！', -120);
                }


                //拼装文件名,并插入数据库
                $result = self::uploadFileBuilder($type, $photos, $date, $params, $standardId,$studentId);
            }
//            header('print',$result->id);
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
    public function postTestAttachRadio(Request $request) {
        try {
            //获取数据
            $studentId = $request->input('student_id');
            $stationId = $request->input('station_id');
            $standardId = $request->input('standard_id');

            $exam = Exam::doingExam();
            if (is_null($exam)) {
                throw new \Exception('当前没有正在进行的考试', -1);
            }
            //根据ID找到对应的名字
            $student = Student::find($studentId);
            if (is_null($student)) {
                throw new \Exception('找不到该考生', -3);
            }
            $studentName = $student->name;
            $studentCode = $student->code;
            $station = Station::find($stationId);
            if (is_null($station)) {
                throw new \Exception('找不到该考站', -4);
            }
            $stationName = $station->name;
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

            //设定当前文件类型
            $type = 'radio';

            if (!$request->hasFile('radio')) {
                throw new \Exception('上传的音频不存在', -120);
            } else {
                $radios = $request->file('radio');
                if (!$radios->isValid()) {
                    throw new \Exception('上传的音频出错', -130);
                }

                $result = self::uploadFileBuilder($type, $radios, $date, $params, $standardId,$studentId);
            }

            return response()->json($this->success_data([$result->id]));
        } catch (\Exception $ex) {
            return response()->json($this->fail($ex));
        }
    }

    /**
     * 将视频锚点插进数据库
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     * @author Jiangzhiheng
     */
    public function postStoreAnchor(Request $request)
    {
        try {
            //验证
            $this->validate($request, [
                'station_id' => 'required|integer',
                'student_id' => 'required|integer',
                'exam_id' => 'required|integer',
                'user_id' => 'required|integer',
                'time_anchors' => 'required|string',
            ]);

            //将视频的锚点信息保存进数据库，因为可能有很多条，所以用foreach
            $stationId = $request->input('station_id');
            $studentId = $request->input('student_id');
            $examId = $request->input('exam_id');
            $timeAnchor = $request->input('time_anchors');
            $teacherId = $request->input('user_id');
            \Log::alert('anchor', $timeAnchor);
            //将戳过来的字符串变成数组
            $timeAnchor = explode(',', $timeAnchor);

            return response()->json($this->success_data(self::storeAnchor($stationId, $studentId, $examId, $teacherId, $timeAnchor)));
        } catch (\Exception $ex) {
            return response()->json($this->fail($ex));
        }
    }

    /**
     * @author Jiangzhiheng
     * @param $stationId 考站id
     * @param $studentId 学生id
     * @param $examId 考试id
     * @param $teacherId 老师id
     * @param $timeAnchors
     * @return bool
     * @throws \Exception
     * @internal param 锚点时间戳 $timeAnchor
     * @internal param $examScreenId
     * @internal param array $timeAnchors
     */
//    private function storeAnchor($stationId, $studentId, $examId, $teacherId, array $timeAnchors)
//    {
//        $connection = \DB::connection('osce_mis');
//        $connection->beginTransaction();
//        try {
//            //获得站点摄像机关联表
//            $stationVcr = StationVcr::where('station_id', $stationId)->first();
//            if (is_null($stationVcr)) {
//                throw new \Exception('该考站未关联摄像机', -200);
//            }
//
//            foreach ($timeAnchors as $timeAnchor) {
//                //拼凑数组
//                $data = [
//                    'station_vcr_id' => $stationVcr->id,
//                    'begin_dt' => date('Y-m-d H:i:s', $timeAnchor),
//                    'end_dt' => date('Y-m-d H:i:s', $timeAnchor),
//                    'created_user_id' => $teacherId,
//                    'exam_id' => $examId,
//                    'student_id' => $studentId,
//                ];
//
//                //将数据插入库
//                if (!$result = StationVideo::create($data)) {
//                    throw new \Exception('保存失败！请重试', -210);
//                }
//            }
//
//            $connection->commit();
//            return ['锚点上传成功！'];
//        } catch (\Exception $ex) {
//            $connection->rollBack();
//            throw $ex;
//        }
//
//    }


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


    public function getLocale(Request $request)
    {
        $this->validate($request, [
            'station_id' => 'required|integer'
        ]);
        $stationId = Input::get('station_id');
        $stationvcrModel = new StationVcr();
        $list = $stationvcrModel->vcrlist($stationId);
        $vcrdata = [
            'name' => $list->name,
            'code' => $list->code,
            'ip' => $list->ip,
            'username' => $list->username,
            'port' => $list->port,
            'channel' => $list->channel,
        ];
        if ($list->status == 0) {
            return response()->json(
                $this->success_data('', 0, '摄像头损坏')
            );
        } else {
            return response()->json(
                $this->success_data($vcrdata, 1, '摄像头可用')
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
    public function getStartExam(Request $request)
    {
//        try {
            $this->validate($request, [
                'student_id' => 'required|integer',
                'station_id' => 'required|integer'

            ], [
                'student_id.required' => '考生编号信息必须',
                'station_id.required' => '考站编号信息必须'
            ]);
            $redis = Redis::connection('message');
            $nowTime = time();
            $date = date('Y-m-d H:i:s', $nowTime);
            $studentId = $request->get('student_id');
            $stationId = $request->get('station_id');
            $teacherId =$request->get('user_id');
            $type =$request->get('type');
            //开始考试时创建成绩
//            $ExamResultData=[
//                'student_id'=>$studentId,
//                'exam_screening_id'=>Null,
//                'station_id'=>$stationId,
//                'begin_dt'=>$date,
//                'end_dt'=>Null,
//                'score'=>0,
//                'score_dt'=>Null,
//                'create_user_id'=>Null,
//                'teacher_id'=>Null,
//                'evaluate'=>Null,
//                'operation'=>0,
//                'skilled'=>0,
//                'patient'=>0,
//                'affinity'=>0,
//
//            ];
//           if(!ExamResult::create($ExamResultData)){
//               throw new \Exception('成绩创建失败',-106);
//           }
            $ExamQueueModel = new ExamQueue();
            $AlterResult = $ExamQueueModel->AlterTimeStatus($studentId, $stationId, $nowTime,$teacherId,$type);
            //dd($AlterResult);
            if ($AlterResult) {
                \Log::alert($AlterResult);
                $redis->publish('pad_message', json_encode($this->success_data([$date], 105, '开始考试成功')));
                return response()->json(
                    $this->success_data([$date], 1, '开始考试成功')
                );
            }
//            return response()->json(
//                $this->fail(new \Exception('开始考试失败,请再次核对考生信息后再试!!!'))
//            );
//        } catch (\Exception $ex) {
//            \Log::alert($ex->getMessage() . '');
//            return response()->json($this->fail($ex));
//        }
    }

    /**
     *  替考警告
     * @method GET
     * @url
     * @access public
     * @param Request $request get请求<br><br>
     * <b>get请求字段：</b>
     * * string     student_id    学生id   (必须的)
     * @return json
     * @version
     * @author zhouqiang <zhouqiang@misrobot.com>
     * @date
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function getTheirSteadWarning(Request $request)
    {
        try {

            $this->validate($request, [
                'student_id' => 'required|integer'
            ]);
            $studentId = $request->get('student_id');
            $student = Student::find($studentId);
            if (!$student) {
                throw new \Exception('没有找到该学生相关信息', -1);
            }
//                 $student->status=5;
            if (!$student->save()) {
                throw new \Exception('替考警告失败', -2);
            }
            return response()->json(
                $this->success_data('替考警告成功', 1)
            );
        } catch (\Exception $ex) {
            return response()->json($this->fail($ex));

        }


    }


    /**
     *  显示所有已绑定但未解绑人员的接口
     * @method GET
     * @url
     * @access public
     * @param Request $request get请求<br><br>
     * <b>get请求字段：</b>
     * * string
     * @return json
     * @version
     * @author weihuiguo <weihuiguo@misrobot.com>
     * @date
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function getBoundWatchMembers(Request $request){
        $WatchLog = new WatchLog();
        $boundWatchInfo = $WatchLog->getBoundWatchInfos();

        if(count($boundWatchInfo) > 0){
            $boundWatchInfo = $boundWatchInfo->toArray();

            foreach($boundWatchInfo as $k=>$v){
                if($v['status'] < 2){
                    $boundWatchInfo[$k]['status'] = '0';
                }elseif($v['status'] == 2){
                    $boundWatchInfo[$k]['status'] = '1';
                }else{
                    $boundWatchInfo[$k]['status'] = '2';
                }
            }
            return response()->json(
                $this->success_data($boundWatchInfo,200)
            );
        }

        return response()->json(
            $this->success_data('',0,'没有数据')
        );
    }

    /**
     *  查看考生及与其绑定的腕表的详细信息
     * @method GET
     * @url invigilatepad/examinee-bound-watch-detail
     * @access public
     * @param Request $request get请求<br><br>
     * <b>get请求字段：</b>
     * * string     student_id   exam_id    (必须的)
     * @return json
     * @version
     * @author weihuiguo <weihuiguo@misrobot.com>
     * @date
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function getExamineeBoundWatchDetail(Request $request){
        $this->validate($request, [
            'nfc_code' => 'required|string',
        ]);

        try {
            //考生基本信息
            $equipmentId = $request->get('nfc_code');

            //查找考生及与其绑定的腕表的详细信息
            $watchModel = new WatchLog();
            $studentWatchData = $watchModel->getExamineeBoundWatchDetails($equipmentId);

            if(count($studentWatchData) > 0){
                $studentWatchData = $studentWatchData->toArray();
            }

            //查找考生的剩余考站数量
            $examQueue = new ExamQueue();
            $station_num = $examQueue->getStationNum($studentWatchData['student_id']);
            if(!empty($studentWatchData)){
                $studentWatchData['station_num'] = $station_num->station_num;
                unset($studentWatchData['student_id']);
            }
            if(count($studentWatchData) > 0){
                return response()->json(
                    $this->success_data($studentWatchData,200,'success')
                );
            }else{
                throw new \Exception('没有找到相关信息', -2);
            }


        } catch (\Exception $ex) {
            return response()->json($this->fail($ex));

        }
    }

    /**
     *  查询使用中的腕表数据
     * @method GET
     * @url api/invigilatepad/useing-watch-data
     * @access public
     * @param Request $request get请求<br><br>
     * <b>get请求字段：</b>
     * * string     status   type    (必须的)
     * @return json
     * @version
     * @author weihuiguo <weihuiguo@misrobot.com>
     * @date
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function getUseingWatchData(Request $request){
        try{
            $this->validate($request, [
                'status' => 'required|integer',
                'type' => 'sometimes|integer',
                'nfc_code' => 'sometimes|string'
            ]);

            $status = $request->get('status')?$request->get('status'):1;  //腕表的使用状态 1 => '使用中',0 => '未使用',2 => '报废',3 => '维修'
            $type = $request->get('type');      //考试状态 考试中（1），等待中（0），已结束（2）
            $nfc_code = $request->get('nfc_code');
            //查询使用中的腕表数据
            $watchModel = new Watch();
            $watchData = $watchModel->getWatchAboutData($status,$type,$nfc_code);

            if(count($watchData) > 0){
                $watchData = $watchData->toArray();
                foreach($watchData as $k=>$v){
                    if($v['status'] < 2){
                        $watchData[$k]['status'] = '0';
                    }elseif($v['status'] == 2){
                        $watchData[$k]['status'] = '1';
                    }else{
                        $watchData[$k]['status'] = '2';
                    }
                }
                return response()->json(
                    $this->success_data($watchData,200,'success')
                );
            }else{
                throw new \Exception('没有找到相关设备信息', -2);
            }
        } catch (\Exception $ex) {
            return response()->json($this->fail($ex));

        }


    }
    /**
     *  查询某个腕表的考试状态
     * @method GET
     * @url api/invigilatepad/useing-watch-data
     * @access public
     * @param Request $request get请求<br><br>
     * <b>get请求字段：</b>
     * * string     status   type    (必须的)
     * @return json
     * @version
     * @author weihuiguo <weihuiguo@misrobot.com>
     * @date
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function getSingleWatchData(Request $request){

        try{
            $this->validate($request, [
                'nfc_code' => 'required|string',
            ]);

            $ncfCode = $request->get('nfc_code');//腕表NCF编码

            //查询某个腕表的考试状态
            $watchModel = new Watch();
            $watchData = $watchModel->getWatchExamStatus($ncfCode);

            if(count($watchData) > 0){
                if($watchData->status < 2){
                    $status = 0;
                }elseif($watchData->status == 2){
                    $status = 1;
                }else{
                    $status = 2;
                }
                return response()->json(
                    $this->success_data($status,200,'success')
                );
            }else{
                throw new \Exception('没有找到相关设备信息', -2);
            }
        } catch (\Exception $ex) {
            return response()->json($this->fail($ex));

        }
    }


    /**
     *  查看考生当前状态
     * @method GET
     * @url api/invigilatepad/useing-watch-data
     * @access public
     * @param Request $request get请求<br><br>
     * <b>get请求字段：</b>
     * * string     status   type    (必须的)
     * @return json
     * @version
     * @author weihuiguo <weihuiguo@misrobot.com>
     * @date
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function getExamineeStatus(Request $request){
        try{
            $this->validate($request, [
                'student_id' => 'required|integer',
            ]);

            //查找当前正在进行的考试
            $examing = Exam::where('status','=',1)->first();
            $studentId = $request->get('student_id');//学生ID

            //查询某个腕表的考试状态
            $examQueue = new ExamQueue();
            $studentStatus = $examQueue->getExamineeStatus($examing->id,$studentId);

            if(count($studentStatus) > 0){
                if($studentStatus->status < 2){
                    $status = 0;
                }elseif($studentStatus->status == 2){
                    $status = 1;
                }else{
                    $status = 2;
                }
                return response()->json(
                    $this->success_data($status,200,'success')
                );
            }else{
                throw new \Exception('没有找到相关信息', -2);
            }
        } catch (\Exception $ex) {
            return response()->json($this->fail($ex));

        }
    }


    /**
     *  解除腕表绑定
     * @method GET
     * @url api/invigilatepad/useing-watch-data
     * @access public
     * @param Request $request get请求<br><br>
     * <b>get请求字段：</b>
     * * string     status   type    (必须的)
     * @return json
     * @version
     * @author weihuiguo <weihuiguo@misrobot.com>
     * @date
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function getWatchUnbundling(Request $request){
        $this->validate($request,[
            'code'      =>'required',//腕表设备编码
            'exam_id'   =>'required', //考试id
        ]);

        $code=$request->get('code');
        $exam_id=$request->get('exam_id');
        //开启事务
        $connection = \DB::connection('osce_mis');
        $connection ->beginTransaction();
        try{
            $id = Watch::where('code',$code)->select('id')->first()->id;    //获取腕表id
            $student_id = WatchLog::where('watch_id',$id)->where('action','绑定')->select('student_id')->orderBy('id','DESC')->first();//腕表使用记录查询学生id
            if(!$student_id){    //如果学生不存在
                $result = Watch::where('id',$id)->update(['status'=>0]);//解绑
                if($result){
                    return \Response::json(array('code'=>2));       //该腕表绑定的学生不存在
                }else{
                    return \Response::json(array('code'=>0));       //解绑失败
                }
            }
            $student_id=$student_id->student_id;
            //获取学生信息
            $studentInfo = Student::where('id', $student_id)->select(['id','name','code as idnum','idcard'])->first();

            $screen_id = ExamOrder::where('exam_id','=',$exam_id)->where('student_id','=',$student_id)->first();  //考试场次编号
            if(!$screen_id){
                $result = Watch::where('id',$id)->update(['status'=>0]);//解绑
                if($result){
                    $action = '解绑';
                    $updated_at = date('Y-m-d H:i:s',time());
                    $data = array(
                        'watch_id'       =>$id,
                        'action'         =>$action,
                        'context'        =>array('time'=>$updated_at,'status'=>0),
                        'student_id'     =>$student_id,
                    );
                    //将解绑记录添加到腕表使用历史表中
                    $watchModel = new WatchLog();
                    $watchModel->unwrapRecord($data);
                    return \Response::json([
                        'code' => 200,
                    ]);   //解绑成功
                }else{
                    throw new \Exception('解绑失败');
                }
            }
            $exam_screen_id = $screen_id->exam_screening_id;
            $ExamFinishStatus = ExamQueue::where('status', '=', 3)->where('student_id', '=', $student_id)->count();
            $ExamFlowModel = new  ExamFlow();
            $studentExamSum = $ExamFlowModel->studentExamSum($exam_id);
            if($ExamFinishStatus==$studentExamSum){ //如果考试流程结束
                ExamScreeningStudent::where('watch_id',$id)->where('student_id',$student_id)->where('exam_screening_id',$exam_screen_id)->update(['is_end'=>1]);//更改考试场次终止状态
                ExamOrder::where('student_id',$student_id)->where('exam_id',$exam_id)->update(['status'=>2]);//更改考生排序状态
                $result = Watch::where('id',$id)->update(['status'=>0]);
                if($result){
                    $action='解绑';
                    $updated_at = date('Y-m-d H:i:s',time());
                    $data = array(
                        'watch_id'       =>$id,
                        'action'         =>$action,
                        'context'        =>array('time'=>$updated_at,'status'=>0),
                        'student_id'     =>$student_id,
                    );
                    $watchModel=new WatchLog();
                    $watchModel->unwrapRecord($data);


                    //TODO:罗海华 2016-02-06 14:27     检查考试是否可以结束
                    $examScreening  =  new ExamScreening();
                    $examScreening  -> getExamCheck();
                    $connection->commit();

                    return \Response::json([
                        'code' => 200,

                    ]);
                }else{
                    throw new \Exception('解绑失败');
                }
            }

            //如果考试流程未结束 还是解绑,把考试排序的状态改为0
            $result=Watch::where('id',$id)->update(['status'=>0]);
            if($result){
                $action = '解绑';
                $result = ExamOrder::where('student_id',$student_id)->where('exam_id',$exam_id)->update(['status'=>0]);
                if($result){
                    $updated_at =date('Y-m-d H:i:s',time());
                    $data = array(
                        'watch_id'       =>$id,
                        'action'         =>$action,
                        'context'        =>array('time'=>$updated_at,'status'=>0),
                        'student_id'     =>$student_id,
                    );
                    $watchModel = new WatchLog();
                    $watchModel->unwrapRecord($data);
                    ExamScreeningStudent::where('watch_id',$id)->where('student_id',$student_id)->where('exam_screening_id',$exam_screen_id)->update(['is_end'=>2]);

                    //TODO:罗海华 2016-02-06 14:27     检查考试是否可以结束
                    $examScreening   =   new ExamScreening();
                    $examScreening  ->getExamCheck();
                    //检查考试是否可以结束
                    $connection->commit();
                }
                return \Response::json([
                    'code' => 200,

                ]);
            }else{
                throw new \Exception('解绑失败');
            }
        }
        catch(\Exception $ex)
        {
            $connection->rollBack();
            return \Response::json(array('code'=>0));
        }
    }

    /**
     *  监控标记学生替考记录表
     * @method GET
     * @url api/invigilatepad/useing-watch-data
     * @access public
     * @param Request $request get请求<br><br>
     * <b>get请求字段：</b>
     * * string     status   type    (必须的)
     * @return json
     * @version
     * @author weihuiguo <weihuiguo@misrobot.com>
     * @date
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function getWatchUnbundlingReportLog($station_id,$exam_id,$student_id,$type,$description){
        $ExamMonitor = new ExamMonitor();
        $data = array();
        $user = Auth::user();
        $data = [
            'station_id'        => $station_id,
            'exam_id'           => $exam_id,
            'student_id'        => $student_id,
            'created_user_id'   => $user->id,
            'type'              => $type,
            'description'       => $description
        ];

        $ExamMonitor->create($data);
    }
    /**
     *  解除腕表绑定并上报
     * @method GET
     * @url api/invigilatepad/watch-unbundling-report
     * @access public
     * @param Request $request get请求<br><br>
     * <b>get请求字段：</b>
     * * string     status   type    (必须的)
     * @return json
     * @version
     * @author weihuiguo <weihuiguo@misrobot.com>
     * @date
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function getWatchUnbundlingReport(Request $request){
        $this->validate($request,[
            'code'      =>'required',//腕表设备编码
            'exam_id'   =>'required', //考试id
            'description'   =>'required', //解绑原因
            'type'   =>'required' //上报类型
        ]);

        $code=$request->get('code');
        $exam_id=$request->get('exam_id');
        $description=$request->get('description');
        $type=$request->get('type');
        //开启事务
        $connection = \DB::connection('osce_mis');
        $connection ->beginTransaction();
        try{
            $id = Watch::where('code',$code)->select('id')->first()->id;    //获取腕表id
            $student_id = WatchLog::where('watch_id',$id)->where('action','绑定')->select('student_id')->orderBy('id','DESC')->first();//腕表使用记录查询学生id
            if(!$student_id){    //如果学生不存在
                $result = Watch::where('id',$id)->update(['status'=>0]);//解绑
                if($result){
                    return \Response::json(array('code'=>2));       //该腕表绑定的学生不存在
                }else{
                    return \Response::json(array('code'=>0));       //解绑失败
                }
            }
            $student_id=$student_id->student_id;
            //获取学生信息
            $studentInfo = Student::where('id', $student_id)->select(['id','name','code as idnum','idcard'])->first();

            $station_id = ExamQueue::where('exam_id','=',$exam_id)->first();
            $screen_id = ExamOrder::where('exam_id','=',$exam_id)->where('student_id','=',$student_id)->first();  //考试场次编号
            if(!$screen_id){
                $result = Watch::where('id',$id)->update(['status'=>0]);//解绑
                if($result){
                    $action = '解绑';
                    $updated_at = date('Y-m-d H:i:s',time());
                    $data = array(
                        'watch_id'       =>$id,
                        'action'         =>$action,
                        'context'        =>array('time'=>$updated_at,'status'=>0),
                        'student_id'     =>$student_id,
                    );
                    //将解绑记录添加到腕表使用历史表中
                    $watchModel = new WatchLog();
                    $watchModel->unwrapRecord($data);

                    //解绑上报
                    $this->getWatchUnbundlingReportLog($station_id->station_id,$exam_id,$student_id,$type,$description);
                    return \Response::json([
                        'code' => 200,
                    ]);   //解绑成功
                }else{
                    throw new \Exception('解绑失败');
                }
            }
            $exam_screen_id = $screen_id->exam_screening_id;
            $ExamFinishStatus = ExamQueue::where('status', '=', 3)->where('student_id', '=', $student_id)->count();
            $ExamFlowModel = new  ExamFlow();
            $studentExamSum = $ExamFlowModel->studentExamSum($exam_id);
            if($ExamFinishStatus==$studentExamSum){ //如果考试流程结束
                ExamScreeningStudent::where('watch_id',$id)->where('student_id',$student_id)->where('exam_screening_id',$exam_screen_id)->update(['is_end'=>1]);//更改考试场次终止状态
                ExamOrder::where('student_id',$student_id)->where('exam_id',$exam_id)->update(['status'=>2]);//更改考生排序状态
                $result = Watch::where('id',$id)->update(['status'=>0]);
                if($result){
                    $action='解绑';
                    $updated_at = date('Y-m-d H:i:s',time());
                    $data = array(
                        'watch_id'       =>$id,
                        'action'         =>$action,
                        'context'        =>array('time'=>$updated_at,'status'=>0),
                        'student_id'     =>$student_id,
                    );
                    $watchModel=new WatchLog();
                    $watchModel->unwrapRecord($data);


                    //TODO:罗海华 2016-02-06 14:27     检查考试是否可以结束
                    $examScreening  =  new ExamScreening();
                    $examScreening  -> getExamCheck();
                    //解绑上报
                    $this->getWatchUnbundlingReportLog($station_id->station_id,$exam_id,$student_id,$type,$description);
                    $connection->commit();

                    return \Response::json([
                        'code' => 200,

                    ]);
                }else{
                    throw new \Exception('解绑失败');
                }
            }

            //如果考试流程未结束 还是解绑,把考试排序的状态改为0
            $result=Watch::where('id',$id)->update(['status'=>0]);
            if($result){
                $action = '解绑';
                $result = ExamOrder::where('student_id',$student_id)->where('exam_id',$exam_id)->update(['status'=>0]);
                if($result){
                    $updated_at =date('Y-m-d H:i:s',time());
                    $data = array(
                        'watch_id'       =>$id,
                        'action'         =>$action,
                        'context'        =>array('time'=>$updated_at,'status'=>0),
                        'student_id'     =>$student_id,
                    );
                    $watchModel = new WatchLog();
                    $watchModel->unwrapRecord($data);
                    ExamScreeningStudent::where('watch_id',$id)->where('student_id',$student_id)->where('exam_screening_id',$exam_screen_id)->update(['is_end'=>2]);

                    //TODO:罗海华 2016-02-06 14:27     检查考试是否可以结束
                    $examScreening   =   new ExamScreening();
                    $examScreening  ->getExamCheck();
                    //解绑上报
                    $this->getWatchUnbundlingReportLog($station_id->station_id,$exam_id,$student_id,$type,$description);
                    //检查考试是否可以结束
                    $connection->commit();
                }
                return \Response::json([
                    'code' => 200,

                ]);
            }else{
                throw new \Exception('解绑失败');
            }
        }
        catch(\Exception $ex)
        {
            $connection->rollBack();
            return \Response::json(array('code'=>0));
        }
    }
}