<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/1/26 0026
 * Time: 10:45
 */

namespace Modules\Osce\Http\Controllers\Wechat;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Modules\Osce\Entities\ExamQueue;
use Modules\Osce\Entities\ExamResult;
use Modules\Osce\Entities\ExamScore;
use Modules\Osce\Entities\ExamScreening;
use Modules\Osce\Entities\ExamStation;
use Modules\Osce\Entities\Standard;
use Modules\Osce\Entities\Station;
use Modules\Osce\Entities\StationTeacher;
use Modules\Osce\Entities\Student;
use Modules\Osce\Entities\Exam;
use Modules\Osce\Entities\Subject;
use Modules\Osce\Entities\Teacher;
use Modules\Osce\Entities\TestResult;
use Modules\Osce\Http\Controllers\CommonController;
use Auth;

class StudentExamQueryController extends CommonController
{
    /**
     * 获取考试 ，还回数据个页面
     * @method GET
     * @url /osce/wechat/student-exam-query/results-query-index
     * @access public
     * @param Request $request get请求<br><br>
     * <b>get请求字段：</b>
     * @return view
     * @version 1.0
     * @author zhouqiang <zhouqiang@misrobot.com>
     * @date
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */

    public function getResultsQueryIndex(Request $request)
    {
        try {
            $user = Auth::user();
            if (empty($user)) {
                throw new \Exception('当前用户未登陆');
            }
            //检查用户是学生还是监考老师
            $invigilateTeacher = Teacher::find($user->id);
            if ($invigilateTeacher && $invigilateTeacher->type == 1) {
                // 根据老师id找到老师所监考得考试考站
                $examModel = new Exam();
                $ExamList = $examModel->getInvigilateTeacher($user->id);

                return view('osce::wechat.resultquery.examination_list_teacher', ['ExamList' => $ExamList]);
            }

            //根据用户获得考试id
            $ExamIdList = Student::where('user_id', '=', $user->id)->select('exam_id')->get();

            if(!$ExamIdList){
                throw new \Exception('目前你还没有参加过考试。');

            }
            $list = [];
            foreach ($ExamIdList as $key => $data) {
                $list[$key] = [
                    'exam_id' => $data->exam_id,
                ];
            }
            $examIds = array_column($list, 'exam_id');
            $ExamModel = new Exam();
            $ExamList = $ExamModel->Examname($examIds);

            //根据考试id获取所有考试
            //dd($ExamList);
            return view('osce::wechat.resultquery.examination_list', ['ExamList' => $ExamList]);

        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    /**
     * 获取每场考试下的所有考站信息
     * @method GET
     * @url /osce/wechat/student-exam-query/every-exam-list
     * @access public
     * @param Request $request get请求<br><br>
     * <b>get请求字段：</b>
     * @return json
     * @version 0.4
     * @author zhouqiang <zhouqiang@misrobot.com>
     * @date
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */


    public function getEveryExamList(Request $request)
    {
        $this->validate($request, [
            'exam_id' => 'required|integer',
            'student_id' => 'sometimes|integer'
        ]);
        $examId = Input::get('exam_id');
        $studentId = Input::get('student_id');
        //获取到考试的时间
        try {
            if (empty($studentId)) {
                //如果是学生根据用户和考试拿到考试的学生id
                $user = Auth::user();
                $student = Student::where('user_id', '=', $user->id)->where('exam_id', '=', $examId)->first();
                $studentId = $student->id;
            }
            //TODO 根据学生id查出学生姓名和电话监考老师成绩查询时用
            $studentInfo = Student::find($studentId);
            $examTime = Exam::where('id', $examId)->select('begin_dt', 'end_dt', 'name')->first();
            // TODO 根据考试id找到对应的考试场次  zhouqiang  2016-3-7

        $examScreeningId = ExamScreening::where('exam_id', '=', $examId)->select('id')->get()->pluck('id');
            //判断学生参加过那几场考试
//            $examScreeningId = ExamQueue::where('exam_id','=',$examId)->where('student_id','=',$studentId)->get()->pluck('id');

//        $examScreening = [];
//        foreach ($examScreeningId as $data) {
//            $examScreening[] = [
//                'id' => $data->id,
//            ];
//        }
//        $examScreeningIds = array_column($examScreening, 'id');

            //根据场次id查询出考站的相关考试结果
            $ExamResultModel = new ExamResult();
            $stationList = $ExamResultModel->stationInfo($studentId,$examScreeningId);
            if (!$stationList) {
                throw new \Exception('没有找到学生成绩信息');
            }
            $stationData = [];
            foreach ($stationList as $key => $stationType) {
                if ($stationType->type == 2) {
                    //获取到sp老师信息
                    $teacherModel = new Teacher();
                    $spteacher = $teacherModel->getSpTeacher($stationType->station_id, $examId);

//                    if (!$spteacher) {
//                        throw new \Exception('没有找到' . $stationType->station_name . 'sp老师');
//                    }
                }
                //转换耗时 TODO： zhoufuxiang 2016-3-14
//                date_default_timezone_set("UTC");
//                $stationType->time = date('H:i:s', $stationType->time);
//                date_default_timezone_set("PRC");

                $stationData[] = [
                    'exam_result_id' => $stationType->exam_result_id,
                    'station_id' => $stationType->id,
                    'score' => $stationType->score,
                    'time' => $stationType->time,
                    'grade_teacher' => $stationType->grade_teacher,
                    'type' => $stationType->type,
                    'station_name' => $stationType->station_name,
                    'sp_name' => isset($spteacher->name) ? $spteacher->name : '-',
                    'begin_dt' => $examTime->begin_dt,
                    'end_dt' => $examTime->end_dt,
                    'exam_screening_id' => $stationType->exam_screening_id,
//                    'student_name' =>$studentInfo->name,
//                    'student_mobile' =>$studentInfo->mobile,
                ];
            }

            //如果是监考老师调用这个方法
            if (Input::get('student_id')) {
                return view('osce::wechat.resultquery.examination_teacher', ['studentInfo' => $studentInfo, 'stationData' => $stationData, 'examName' => $examTime]);
            } else {
                return response()->json(
                    $this->success_data($stationData, 1, '数据传送成功')
                );
            }

        } catch (\Exception $ex) {
            return response()->json($this->fail($ex));
        }
    }

    /**
     * 考生成绩查询详情页根据考站id查询
     * @method GET
     * @url /osce/wechat/student-exam-query/exam-details
     * @access public
     * @param Request $request get请求<br><br>
     * <b>get请求字段：</b>
     * @return view
     * @version 1.0
     * @author zhouqiang <zhouqiang@misrobot.com>
     * @date
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */


    public function  getExamDetails(Request $request)
    {
        $this->validate($request, [
            'exam_screening_id' => 'required|integer',
//            'station_id'    => 'required|integer'
        ]);

        $examScreeningId = intval(Input::get('exam_screening_id'));
        $station_id = intval(Input::get('station_id'));
        //根据考试场次id查询出该结果详情
        $examresultList = ExamResult::where('exam_screening_id', '=', $examScreeningId)->where('station_id', '=', $station_id)->first();
        if(is_null($examresultList)){
            throw new \Exception('该考试结果不存在');
        }
        //得到考试名字
        $examName = ExamScreening::where('id', $examScreeningId)->select('exam_id')->first()->ExamInfo;

        //查询出详情列表
        $examscoreModel = new ExamScore();
        $examScoreList = $examscoreModel->getExamScoreList($examresultList->id);

        //TODO: zhoufuxiang
        $scores = [];
        $itemScore = [];
        foreach ($examScoreList as $itm) {
            $pid = $itm->standard->pid;
            $scores[$pid]['items'][] = [
                'standard' => $itm->standard,
                'score' => $itm->score,
            ];
            $itemScore[$pid]['totalScore'] = (isset($itemScore[$pid]['totalScore']) ? $itemScore[$pid]['totalScore'] : 0) + $itm->score;
        }

        foreach ($scores as $index => $item) {
            //获取考核点信息
            $standardM = Standard::where('id', $index)->first();
            $scores[$index]['sort'] = $standardM->sort;
            $scores[$index]['content'] = $standardM->content;
            $scores[$index]['tScore'] = $standardM->score;
            $scores[$index]['score'] = $itemScore[$index]['totalScore'];
        }


//        $groupData = [];
//        foreach ($examScoreList as $examScore) {
//            $groupData[$examScore->standard->pid][] = $examScore;
//        }
//        $indexData = [];
//        if (empty($groupData[0])) {
//            throw new \Exception('请检查该考站是否有评分详情');
//        }
//        foreach ($groupData[0] as $group) {
//            $groupInfo = $group;
//            try {
//                $groupInfo['child'] = $groupData[$group->standard->id];  //排序array_multisort($volume, SORT_DESC, $edition, SORT_ASC, $data);
//            } catch (\Exception $ex) {
//
//                dd($group->standard->id, $groupData);
//            }
//            $indexData[] = $groupInfo;
//        }
//        $list = [];
//        foreach ($indexData as $goupData) {
//            $childrens = is_null($goupData['child']) ? [] : $goupData['child'];
//            unset($goupData['child']);
//            $list[] = $goupData;
//            foreach ($childrens as $children) {
//                $list[] = $children;
//            }
//        }

        return view('osce::wechat.resultquery.examination_detail',
            [
                'examScoreList' => $scores,
                'examresultList' => $examresultList,
                'examName' => $examName
            ]);
    }


    /**
     * 动态查询出考试科目
     * @method GET
     * @url /osce/wechat/student-exam-query/subject-list
     * @access public
     * @param Request $request get请求<br><br>
     * <b>get请求字段：</b>
     * @return json
     * @version 0.4
     * @author zhouqiang <zhouqiang@misrobot.com>
     * @date
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */

    public function getSubjectList(Request $request)
    {

        $this->validate($request, [
            'exam_id' => 'required|integer'

        ]);
        $examId = $request->get('exam_id');
        //根据考试查询出最近的考试科目
        $subjectModel = new Subject();
        $subject = $subjectModel->getSubjectList($examId);
        $subjectData = [];
        foreach ($subject as $subjectList) {
            $subjectData[] = [
                'subject_name' => $subjectList->subject_name,
                'subject_id' => $subjectList->id,
                'exam_id' => $examId
            ];
        }
        if ($subject) {
            return response()->json(
                $this->success_data($subjectData, 1, '科目数据传送成功')
            );
        } else {
            return response()->json(
                $this->fail(new \Exception('科目数据传送成功'))
            );
        }
    }


    /**
     * 监考老师查询科目成绩和学生情况
     * @method GET
     * @url  /osce/wechat/student-exam-query/teacher-check-score
     * @access public
     * @param Request $request get请求<br><br>
     * <b>get请求字段：</b>
     * @return  json
     * @version 0.4
     * @author zhouqiang <zhouqiang@misrobot.com>
     * @date
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */

    public function getTeacherCheckScore(Request $request)
    {

        $this->validate($request, [
            'exam_id' => 'required|integer',
            'station_id' => 'required|integer'
        ]);
        $examId = $request->get('exam_id');
        $stationId = $request->get('station_id');
        try {
            $examTime = Exam::where('id', $examId)->select('begin_dt', 'end_dt', 'name')->first();


            //根据考站找到对应的科目
            $subjectId = Station::find($stationId)->subject_id;
            $subjectName = Subject::find($subjectId)->title;
            //调用科目成绩统计查询的接口方法
            $subjectModel = new Subject();
            $studentModel = new Student();
            //找到按科目为基础的所有分数还有总人数
            $avg = $subjectModel->CourseControllerAvg($examId, $subjectId);
            //如果avg不为空
            $item = [
                'exam_begin_dt' => $examTime->begin_dt,
                'exam_end_dt' => $examTime->end_dt,
                'subject_name' => $subjectName,
            ];
            if (!empty($avg)) {

                if ($avg->pluck('score')->count() != 0 || $avg->pluck('time')->count() != 0) {
                    $item['avg_score'] = number_format($avg->pluck('score')->sum() / $avg->pluck('score')->count(), 2);
                    date_default_timezone_set("UTC");
                    $item['avg_time'] = date('H:i:s', $avg->pluck('time')->sum() / $avg->pluck('time')->count());
                    date_default_timezone_set("PRC");
                    $item['avg_total'] = $avg->count();
                } else {
                    $item['avg_score'] = 0;
                    $item['avg_time'] = 0;
                    $item['avg_total'] = $avg->count();
                }
            }
            //获取该考试科目所有的学生
            $studentData = $studentModel->getStudentByExamAndSubject($examId, $subjectId);
            $subjectData = [];
            //根据考生id查出该考试在本考试的总成绩
            foreach ($studentData as $student) {
                //调用查看总成绩的方法
                $tesresultModel = new TestResult();
                $StudentScores = $tesresultModel->AcquireExam($student->student_id);
//                $item[$studentId->student_name] = $StudentScores;
                $subjectData[] = [
                    'student_name' => $student->student_name,
                    'student_id' => $student->student_id,
                    'exam_id' => $examId,
                    'Scores' =>$student->exam_result_score,

                ];
            }
//            dd($item,$subjectData);
            return response()->json(
                $this->success_data(['subjectData' => $subjectData, 'item' => $item], 1, '科目数据传送成功')
            );
        } catch (\Exception $ex) {
            return response()->json($this->fail($ex));
        }
    }
}