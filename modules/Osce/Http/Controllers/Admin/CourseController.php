<?php
/**
 * Created by PhpStorm.
 * User: j5110
 * Date: 2016/1/28
 * Time: 17:33
 */

namespace Modules\Osce\Http\Controllers\Admin;


use Illuminate\Http\Request;
use Modules\Osce\Entities\Exam;
use Modules\Osce\Entities\ExamPaper;
use Modules\Osce\Entities\ExamPaperStation;
use Modules\Osce\Entities\ExamResult;
use Modules\Osce\Entities\ExamScreening;
use Modules\Osce\Entities\QuestionBankEntities\ExamMonitor;
use Modules\Osce\Entities\Station;
use Modules\Osce\Entities\StationTeacher;
use Modules\Osce\Entities\Student;
use Modules\Osce\Entities\Subject;
use Modules\Osce\Http\Controllers\CommonController;
use Cache;
use Modules\Osce\Repositories\Common;
use Modules\Osce\Repositories\SubjectStatisticsRepositories;
use Modules\Osce\Repositories\TestScoreRepositories;

class CourseController extends CommonController
{
    /**
     * 科目统计的主页,此控制器暂时只支持一次一个考试
     * @param Request $request
     * @author ZouYuChao
     * @return \Illuminate\View\View
     */
    public function getIndex(Request $request, SubjectStatisticsRepositories $subjectStatisticsR,TestScoreRepositories $testScoreR)
    {
        try {
            //验证
            $this->validate($request, [
                'exam_id'       => 'sometimes|integer',
                'subject_id'    => 'sometimes',
                'sign'          => 'sometimes',  //是否为试卷考试的标记
            ]);
            //考试的下拉菜单（父ID=0，考试状态=[1,2]）
            $examlist = $subjectStatisticsR->GetExamList(0);
            //获取考试下拉列表第一条
            $examObj = $examlist->first();

            if (is_null($examObj))
            {
                $subjectData = [];
                $examId      = '';
                $subjectId   = '';
                $subjectList = [];
                $examName    = [];
                $backMes     = '目前还没有 有考试成绩的考试';
            } else
            {
                $examId    = intval($request->input('exam_id'))? :$examObj->id;
                $subjectId = $request->input('subject_id');
                $sign      = $request->input('sign');
                //通过考试id获取所有场次包括子场次的id
                list($screening_ids, $elderExam_ids) = ExamScreening::getAllScreeningByExam($examId);
                //科目列表数据
                $subject     = new Subject();
                $exam        = new Exam();
                $examName    = $exam->where('id',$examId)->pluck('name');
                $subjectData = $exam->CourseControllerIndex($screening_ids,$subjectId, $sign);
                if (count($subjectData) == 0) {
                    $backMes = '该考试还未出成绩';
                }
                foreach ($subjectData as &$item)
                {
                    //找到按科目为基础的所有分数还有总人数
                    $avg = $subject->CourseControllerAvg($screening_ids, $item->subject_id, $item->paper_id);
                    //如果avg不为空
                    if (!empty($avg))
                    {
                        if ($avg->pluck('score')->count() != 0 || $avg->pluck('time')->count() != 0)
                        {
                            $item->avg_score = number_format($avg->pluck('score')->sum() / $avg->pluck('score')->count(), 2);
                            //处理时间显示
                            $item->avg_time  = Common::handleTime($avg->pluck('time')->sum() / $avg->pluck('time')->count());
                            $item->avg_total = $avg->count();
                        } else {
                            $item->avg_score = 0.00;
                            $item->avg_time  = '00:00:00';
                            $item->avg_total = $avg->count();
                        }
                    }
                }

                list($subjectList, $paperList) = $testScoreR->getSubjectPaperList($examId);
                //将考卷数组 与 考试项目数组合并
                $subjectList = array_merge($subjectList, $paperList);

            }
            return view('osce::admin.statisticalAnalysis.subject_scores_list', [
                'exam_name'         => $examName,
                'data'              => $subjectData,
                'examDownlist'      => $examlist,
                'subjectDownlist'   => $subjectList,
                'exam_id'           => $examId,
                'subject_id'        => $subjectId,
                'backMes'           => isset($backMes) ? $backMes : ''
            ]);

        } catch (\Exception $ex) {
            return redirect()->back()->withErrors($ex->getMessage());
        }
    }

    /**
     * 考试项目管理-学生的主页
     * @param Request $request
     * @param SubjectStatisticsRepositories $subjectStatisticsRepositories
     * @return \Illuminate\View\View
     *
     * @author ZouYuChao     fandian <fandian@sulida.com>
     * @date                    2016-06-23 15:45
     * @copyright  2013-2017 sulida.com  Inc. All Rights Reserved
     */
    public function getStudent(Request $request, SubjectStatisticsRepositories $subjectStatisticsRepositories)
    {
        //验证
        $this->validate($request, [
            'exam_id'    => 'required|integer',
            'subject_id' => 'required|integer',
            'subject'    => 'sometimes',
            'sign'       => 'required',
        ]);
        //获得参数
        $examId    = $request->input('exam_id');
        $subjectId = $request->input('subject_id');
        $sign      = $request->input('sign');

        //获取对应的考生以及考生的成绩信息，以及主考试信息、整个考试时间段
        list($datas, $examInfo) = $subjectStatisticsRepositories->getStudentByExamAndSubject($examId, $subjectId, $sign);
        
        //todo:gaodapeng 2016.06.29查询成绩中无效成绩的标示
        $examResult = new ExamResult();
        foreach ($datas as $k=>&$v){
            $v->invalidSign = $examResult->getInvalidSign($v->exam_result_id);
        }
        return view('osce::admin.statisticalAnalysis.subject_student_list', [
            'data'          => $datas,
            'examInfo'      => $examInfo,
            'exam'          => $request->input('exam'),
            'subject'       => $request->input('subject'),
            'paper'         => $request->input('paper'),
        ]);
    }

    /**
     * 考生统计
     * @param Request $request
     * @return \Illuminate\View\View
     * @author ZouYuChao
     */
    public function getStudentScore(Request $request)
    {
        $this->validate($request, [
            'exam_id' => 'sometimes|integer',
            'message' => 'sometimes'
        ]);
        $examId = '';
        $message = '';
        $examDownlist = Exam::select('id', 'name')->where('exam.status', '<>', 0)->where('pid','=',0)->orderBy('begin_dt', 'desc')->get();
        //获得最近的考试的id
        $lastExam = Exam::orderBy('begin_dt', 'desc')->where('exam.status', '<>', 0)->where('pid','=',0)->first();

        if (is_null($lastExam)) {
            $list = [];
            $backMes = '目前没有已结束的考试';
        } else {
            $lastExamId = $lastExam->id;
            //获得参数
            $examId = $request->input('exam_id', $lastExamId);
            $message = $request->input('message');
            list($screening_ids, $elderExam_ids) = ExamScreening::getAllScreeningByExam($examId);
            //获得学生的列表在该考试的列表
            $list = Student::getStudentScoreList($screening_ids, $message);
            //为每一条数据插入统计值
            foreach ($list as $key => &$item) {
                $item->ranking = $key + 1;
            }
            if (!count($list)) {
                $backMes = '该考试还未出成绩';
            }
        }

        return view('osce::admin.statisticalAnalysis.student_scores_list', [
            'data' => $list,
            'examDownlist' => $examDownlist,
            'exam_id' => $examId,
            'message' => $message,
            'backMes' => isset($backMes) ? $backMes : ''
        ]);
    }

    /**
     * 考生成绩详情
     * @param Request $request
     * @return \Illuminate\View\View url    /osce/admin/course/student-details
     * url    /osce/admin/course/student-details
     * @throws \Exception
     * @author zhouqiang
     */
    public function getStudentDetails(Request $request)
    {
        $this->validate($request, [
            'student_id' => 'required|integer'
        ]);
        $studentId = $request->get('student_id');
        $examResultModel = new ExamResult();
        $studentList = $examResultModel->getStudentData($studentId);
       

        if (!$studentList) {
            throw new \Exception('数据查询失败');
        }
        //转化时间（耗时）
        //todo:gaodapeng 2016.06.29查询成绩中无效成绩的标示
        foreach ($studentList as $key => &$item) {
            $item->time = Common::handleTime($item->time);
            $item->invalidSign = $examResultModel->getInvalidSign($item->result_id);
        }
        return view('osce::admin.statisticalAnalysis.student_subject_list', ['studentList' => $studentList]);

    }

    /**
     * 动态获取ajax列表
     * @author ZouYuChao
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getSubject(Request $request,TestScoreRepositories $testScoreR)
    {
        //验证
        $this->validate($request, [
            'exam_id' => 'sometimes|integer'
        ]);

        $examId = $request->input('exam_id', "");

        try {
            list($subjectList, $paperList) = $testScoreR->getSubjectPaperList($examId);
            //将考卷数组 与 考试项目数组合并
            $dataList = array_merge($subjectList, $paperList);

            return response()->json($this->success_data($dataList));
        }
        catch (\Exception $ex) {
            return response()->json($this->fail($ex));
        }
    }

    /**
     * 出科目的下拉菜单
     * @param $examId
     * @return array|\Illuminate\Support\Collection
     * @author ZouYuChao
     */
    private function subjectDownlist($examId)
    {
        /*
         * 给考试对应的科目下拉数据
         */
        $station_ids = StationTeacher::where('exam_id', $examId)
                        ->groupBy('station_id')->get()->pluck('station_id');

        //TODO：fandian 2016-06-01 （添加条件：->whereNotNull('subject_id')）
        $stationList = Station::whereIn('id', $station_ids)->whereNotNull('subject_id')->groupBy('subject_id')->get();
        $subjectList = [];
        foreach ($stationList as $value) {
            $subjectList[] = $value->subject->toArray();
        }
        //$subjectList = collect($subjectList);
        //todo:gaodapeng 2016-06-15 将理论考试项目添加到下拉框
        $paperIdList = ExamPaperStation::where('exam_id',$examId)->get()->pluck('exam_paper_id');
        $paperList = ExamPaper::whereIn('id',$paperIdList)->groupBy('id')->get();
        foreach ($paperList as &$item){
            $item->title = $item->name;
            $item->mins  = $item->length;
            $item->subject_id = 0;
        }
        $subjectList = array_merge($subjectList, $paperList->toArray());
        $subjectList = collect($subjectList);
        return $subjectList;
    }

    /**
     * 无效成绩原因弹出
     * @url course/invalid-score
     * @access public
     * @param Request $request
     * @return mixed
     * @author GaoDapeng <gaodapeng@sulida.com>
     * @copyright 2013-2017 sulida.com Inc. All Rights Reserved
     */

    public function invalidScore(Request $request)
    {
        //验证
        $this->validate($request, [
            'result_id' => 'required|integer'
        ]);
        $resultId = $request->input('result_id');
        try {
            //通过resultId找到考站id
            $stationId=ExamResult::where('id',$resultId)->pluck('station_id');
            //获取弃考信息
            $data = ExamMonitor::leftJoin('exam_result','exam_monitor.student_id','=','exam_result.student_id')
                ->where('exam_result.id',$resultId)
                ->where('exam_monitor.station_id',$stationId)
                ->select(
                    'exam_monitor.reason as reason',
                    'exam_monitor.description as description'
                )
                ->get()
                ->toarray();
            return \Response::json($this->success_data($data));
        } catch (\Exception $ex) {
            return \Response::json($this->fail($ex));
        }
    }
}
