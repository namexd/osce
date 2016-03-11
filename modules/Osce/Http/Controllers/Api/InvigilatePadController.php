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
    public function getTestIndex()
    {
//        $examScreeningModel = new ExamScreening();
//        $result = $examScreeningModel->getExamCheck();
        $a = strtotime('2016-03-04 10:59:14');
        $c = strtotime('2016-03-04 11:59:14');
        $b = ($c - $a);
        dd($b);


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
    protected static function uploadFileBuilder($type, $file, $date, array $params, $standardId)
    {
        try {
            //将上传的文件遍历

            //拼凑文件名字
            $fileName = '';
            //获取文件的MIME类型
//            $fileMime = $file->getMimeType();
            foreach ($params as $param) {
                $fileName .= $param . '_';
            }
            $fileName .= mt_rand() . '.' . $file->getClientOriginalExtension(); //获取文件名的正式版
            //取得保存路径
            $savePath = 'osce/Attach/' . $type . '/' . $date . '/' . $params['student_name'] . '_' . $params['student_code'] . '/';
//            $savePath = 'osce/Attach/' . $fileMime . '/' . $date . '/' . 13 . '_' . 13 . '/';
            $savePath = public_path($savePath);
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
            $attachUrl = $savePath . $fileName;
            //将要插入数据库的数据拼装成数组
            $data = [
                'test_result_id' => null,
                'url' => $attachUrl,
                'type' => $type,
                'name' => $fileName,
                'description' => $date . '-' . $params['student_name'],
                'standard_id' => $standardId
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
            'station_id' => 'required|integer'
        ], [
            'station_id.required' => '考站编号必须'
        ]);
        $stationId = (int)$request->input('station_id');
        $studentModel = new  Student();
        $studentData = $studentModel->studentList($stationId);
        if ($studentData) {
            return response()->json(
                $this->success_data($studentData, 1, '验证完成')
            );
        } else {
            return response()->json(
                $this->fail(new \Exception('学生信息查询失败', 2))
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
//            'exam_id'  => 'required|integer'
            ], [
                'station_id.required' => '没有获取到当前考站',
                'exam_id.required' => '没有获取到当前考试'
            ]);

            $stationId = $request->get('station_id');
//      $stationId=49;
            $examId = $request->get('exam_id');
            //根据考站id查询出下面所有的考试项目
            $station = Station::find($stationId);

            //考试标准时间
            $mins = $station->mins;
            $exam = Exam::find($examId);
            $StandardModel = new Standard();
            $standardList = $StandardModel->ItmeList($station->subject_id);

            if (count($standardList) != 0) {
                return response()->json(
                    $this->success_data($standardList, 1, '数据传送成功')
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
     * 提交评价
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
    private function postSaveExamEvaluate($scoreData, $ExamResultId)
    {
        $data = [];
        foreach ($scoreData as $data) {
//            $data['exam_result_id'] = $ExamResultId;
            $Save = ExamScore::create($data);
            return $Save;
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
                'station_id' => $stationId,
                'student_id' => $studentId,
                'exam_screening_id' => $examScreeningId,
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
            $studentId = $request->input('student_id');
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
                $result = self::uploadFileBuilder($type, $photos, $date, $params, $standardId);
            }
                  header('print',$result->id);
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

                $result = self::uploadFileBuilder($type, $radios, $date, $params, $standardId);
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

            //将戳过来的字符串变成数组
            $timeAnchor = explode(',', $timeAnchor);

            return response()->json($this->success_data($this->storeAnchor($stationId, $studentId, $examId, $teacherId,
                $timeAnchor)));
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
    private function storeAnchor($stationId, $studentId, $examId, $teacherId, array $timeAnchors)
    {
        $connection = \DB::connection('osce_mis');
        $connection->beginTransaction();
        try {
            //获得站点摄像机关联表
            $stationVcr = StationVcr::where('station_id', $stationId)->first();
            if (is_null($stationVcr)) {
                throw new \Exception('该考站未关联摄像机', -200);
            }

            foreach ($timeAnchors as $timeAnchor) {
                //拼凑数组
                $data = [
                    'station_vcr_id' => $stationVcr->id,
                    'begin_dt' => date('Y-m-d H:i:s', $timeAnchor),
                    'end_dt' => date('Y-m-d H:i:s', $timeAnchor),
                    'created_user_id' => $teacherId,
                    'exam_id' => $examId,
                    'student_id' => $studentId,
                ];

                //将数据插入库
                if (!$result = StationVideo::create($data)) {
                    throw new \Exception('保存失败！请重试', -210);
                }
            }

            $connection->commit();
            return ['锚点上传成功！'];
        } catch (\Exception $ex) {
            $connection->rollBack();
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
        try {
            $this->validate($request, [
                'student_id' => 'required|integer',
                'station_id' => 'required|integer'

            ], [
                'student_id.required' => '考生编号信息必须',
                'station_id.required' => '考站编号信息必须'
            ]);
            $nowTime = time();
            $date = date('Y-m-d H:i:s', $nowTime);
            $studentId = $request->get('student_id');
            $stationId = $request->get('station_id');
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
            $AlterResult = $ExamQueueModel->AlterTimeStatus($studentId, $stationId, $nowTime);


            if ($AlterResult) {
                \Log::alert($AlterResult);
                return response()->json(
                    $this->success_data([$date], 1, '开始考试成功')
                );
            }
            return response()->json(
                $this->fail(new \Exception('开始考试失败,请再次核对考生信息后再试!!!'))
            );
        } catch (\Exception $ex) {
            \Log::alert($ex->getMessage() . '');
            return response()->json($this->fail($ex));
        }
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


}