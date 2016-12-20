<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/2/26 0026
 * Time: 14:30
 */

namespace Modules\Osce\Http\Controllers\Admin\Branch;
use Illuminate\Http\Request;
use Modules\Msc\Entities\Student;
use Modules\Osce\Entities\ExamDraft;
use Modules\Osce\Entities\ExamResult;
use Modules\Osce\Entities\ExamScreening;
use Modules\Osce\Entities\QuestionBankEntities\ExamPaperExamStation;
use Modules\Osce\Http\Controllers\CommonController;
use Modules\Osce\Entities\Exam;
use Modules\Osce\Repositories\TestScoreRepositories;
use Modules\Osce\Repositories\SubjectStatisticsRepositories;
class TestScoresController  extends CommonController
{
    /**
     * 考生成绩分析
     * @method  GET
     * @url /osce/admin/testscores/test-score-list
     * @access public
     * @param Request $request
     * @author weihuiguo <weihuiguo@sulida.com>
     * @date    2016年2月26日14:56:58
     * @copyright  2013-2017 sulida.com  Inc. All Rights Reserved
     */
    public function TestScoreList(Request $request, SubjectStatisticsRepositories $SubjectStatisticsR){
        //查找所有考试信息
        $examlist = $SubjectStatisticsR->GetExamList(0);
        return view('osce::admin.statisticalAnalysis.statistics_student_score',[
            'examlist' => $examlist
        ]);
    }
    /**
     * 根据考试ID查找当前考试下的所有学生ID
     * @method  GET
     * @url /osce/admin/testscores/ajax-get-tester
     * @access public
     * @param Request $request,TestScoreRepositories $TestScoreRepositories
     * @author weihuiguo <weihuiguo@sulida.com>
     * @date    2016年2月26日14:56:58
     * @copyright  2013-2017 sulida.com  Inc. All Rights Reserved
     */
    public function postAjaxGetTester(Request $request,TestScoreRepositories $TestScoreRepositories){
        //根据页面get过来的考试ID查找当前考试下的所有考生
        $examId = $request->examid?$request->examid:'';

        if(!$examId){
            $tester = array();
        }else{
            $exam = new Exam();
            //获取当前考试下的所有学生
            $tester = $TestScoreRepositories->getTester($examId);
        }
        return $tester;
    }

    /**
     * 根据考试ID和学生ID对学生科目成绩分析
     * @method  GET
     * @url /osce/admin/testscores/ajax-get-subject
     * @access public
     * @param Request $request,TestScoreRepositories $TestScoreRepositories
     * @author weihuiguo <weihuiguo@sulida.com>
     * @date    2016年2月26日14:56:58
     * @copyright  2013-2017 sulida.com  Inc. All Rights Reserved
     */
    public function getAjaxGetSubject(Request $request,TestScoreRepositories $TestScoreR, SubjectStatisticsRepositories $subjectStatisticsR)
    {
        //获取筛选参数
        $examid     = intval($request->examid);
        $student_id = intval($request->student_id);
        //查找学生所有科目考试数据
        $singledata = $TestScoreR->getTestSubject($examid, $student_id)->toArray();

        //查找所有科目、以及考卷
        list($subjectList, $paperList) = $TestScoreR->getSubjectPaperList($examid);
//        list($subjectList, $paperList) = $subjectStatisticsR->subjectDownlist($examid, true, true);
        $subjectId = [];
        $paperIds  = [];
        if(!empty($subjectList)){
            $subjectId = collect($subjectList)->pluck('id')->toArray();
        }
        if(!empty($paperList)){
            $paperIds = collect($paperList)->pluck('id')->toArray();
        }
        //查找当前考试所有科目平均成绩
        $avgdata = $TestScoreR->getTestSubject($examid, '', $subjectId, $paperIds)->toArray();

        $avgInfo = [];
        foreach($singledata as $k=>$v)
        {
            foreach($avgdata as $kk=>$vv)
            {
                if($v['id'] == $vv['id']){
                    $singledata[$k]['timeAvg']  = $avgdata[$kk]['timeAvg'];
                    $singledata[$k]['scoreAvg'] = $avgdata[$kk]['scoreAvg'];
                    $avgInfo[] = $vv;
                }
            }
        }

        //重新排列 平均成绩数组的顺序
        foreach($avgdata as $v)
        {
            $fag = true;
            foreach($avgInfo as $val){
                if($v['id'] == $val['id']){
                    $fag = false;
                }
            }
            if($fag){
                $avgInfo[] = $v;
            }
        }

        $data = [
            'list'       => $singledata,//列表
            'singledata' => $singledata,//雷达图学生成绩
            'avgdata'    => $avgInfo,   //平均成绩
        ];
        return $data;
    }

    /**
     * 考生成绩分析
     * @method  GET
     * @url /osce/admin/testscores/student-subject-list
     * @access public
     * @param Request $request,TestScoreRepositories $TestScoreRepositories
     * @author weihuiguo <weihuiguo@sulida.com>   <fandian@sulida.com>
     * @date    2016年2月26日14:56:58                2016-07-04
     * @copyright  2013-2017 sulida.com  Inc. All Rights Reserved
     */
    public function studentSubjectList(Request $request,TestScoreRepositories $TestScoreR,SubjectStatisticsRepositories $subjectStatisticsR)
    {
        //获取已考过试的所有学生
        $student_id = $request->student_id;
        $subid      = $request->subid;
        $stuname    = $request->stuname;
        $subject    = $request->subject;
        $sign       = $request->sign;

        //获取学生科目成绩
        $singledata = $TestScoreR->getStudentScoreAnalysis($student_id, $subid, 0, $sign);
        //获取考试数组
        $examid     = empty($singledata) ? [] : $singledata->pluck('exam_id');

        //获取科目平均成绩
        $avgdata    = $TestScoreR->getStudentScoreAnalysis($student_id, $subid, $examid, $sign);

        //循环 处理数据
        foreach($singledata as $k=>$v)
        {
            foreach($avgdata as $kk=>$vv)
            {
                if($v['id'] == $vv['id']){
                    $singledata[$k]['timeAvg']  = $vv['timeAvg'];
                    $singledata[$k]['scoreAvg'] = $vv['scoreAvg'];
                    $singledata[$k]['time']     = date("Y年m月", strtotime($v['begin_dt']));
                    unset($singledata[$k]['begin_dt']);
                }else{
                    $singledata[$k]['timeAvg']  = '0.00';
                    $singledata[$k]['scoreAvg'] = '0.00';
                }
            }
        }

        $avg    = implode(',', $this->nullFullZero($singledata->pluck('scoreAvg')->toArray()));
        $totle  = implode(',', $this->nullFullZero($singledata->pluck('score')->toArray()));
        $time   = implode(',', $this->nullFullZero($singledata->pluck('time')->toArray()));

        $data = [
            'list'       => $singledata,//列表
            'singledata' => $singledata,//雷达图学生成绩
            'avgdata'    => $avgdata    //平均成绩
        ];

        return view('osce::admin.statisticalAnalysis.statistics_student_subject',[
            'data'      => $data,
            'avg'       => $avg,
            'totle'     => $totle,
            'time'      => $time,
            'stuname'   => $stuname,
            'subject'   => $subject,
        ]);
    }

    /**
     * ajax获取当前考生所有已考科目
     * @method  POST
     * @url /osce/admin/testscores/ajax-get-subjectlist
     * @access public
     * @param Request $request,TestScoreRepositories $TestScoreRepositories
     * @author weihuiguo <weihuiguo@sulida.com>
     * @date    2016-2-29 09:45:15
     * @copyright  2013-2017 sulida.com  Inc. All Rights Reserved
     */
    public function ajaxGetSubjectlist(Request $request,TestScoreRepositories $TestScoreRepositories){
        $studentSublist = $TestScoreRepositories->getStudentSubject();
        return $studentSublist;
    }

    /**
     * ajax统计当前考生科目成绩
     * @method  POST
     * @url ajax-get-student-test-count
     * @access public
     * @param Request $request,TestScoreRepositories $TestScoreRepositories
     * @author weihuiguo <weihuiguo@sulida.com>
     * @date    2016-2-29 09:45:15
     * @copyright  2013-2017 sulida.com  Inc. All Rights Reserved
     */
    public function ajaxGetStudentTestCount(Request $request,TestScoreRepositories $TestScoreRepositories,SubjectStatisticsRepositories $subjectStatisticsRepositories){
        $student_id = $request->student_id;
        $subid = $request->subject_id;
        //获取学生科目成绩
        $singledata = $TestScoreRepositories->getStudentScoreCount($student_id,$subid)->toArray();
        //获取科目平均成绩
        $avgdata = $TestScoreRepositories->getStudentScoreCount('',$subid)->toArray();
        foreach($singledata as $k=>$v){
            if($avgdata[$k]['id'] == $v['id']){
                $singledata[$k]['timeAvg'] = $subjectStatisticsRepositories->timeTransformation($avgdata[$k]['timeAvg']);
                $singledata[$k]['scoreAvg'] = $avgdata[$k]['scoreAvg'];
                $singledata[$k]['time'] = date("Y年m月",strtotime($v['begin_dt']));
                unset($singledata[$k]['begin_dt']);
            }
        }
        $data = [
            'list' => $singledata,//列表
            'singledata' => $singledata,//雷达图学生成绩
            'avgdata' => $avgdata,//平均成绩

        ];
        return $this->success_data($data);
    }

    /**
     * 教学成绩分析
     * @method  POST
     * @url     /osce/admin/testscores/test-scores-count
     * @access public
     * @param Request $request,TestScoreRepositories $TestScoreRepositories
     * @author weihuiguo <weihuiguo@sulida.com>
     * @date    2016-3-2 16:51:27 .com Inc. All Rights Reserved
     */
    public function testScoresCount(Request $request,SubjectStatisticsRepositories $SubjectStatisticsRepositories){
        //获取考试的数据
        $examlist = $SubjectStatisticsRepositories->GetExamList($pid = 0);
        return view('osce::admin.statisticalAnalysis.statistics_teach_score',[
            'examlist' => $examlist,
        ]);
    }

    /**
     * 教学成绩分析-请求科目数据
     * @method  POST
     * @url testscores/subject-lists
     * @access public
     * @param Request $request,TestScoreRepositories $TestScoreRepositories
     * @author weihuiguo <weihuiguo@sulida.com>   <fandian@sulida.com>
     * @date    2016-3-2 17:00:10                   2016-06-29 10:00
     * .com Inc. All Rights Reserved
     */
    public function getSubjectLists(Request $request,TestScoreRepositories $testScoreRepositories)
    {
        $examid = $request->examid;
        //获取考试的 考试项目、试卷的下拉列表
        list($subjectList, $paperList) = $testScoreRepositories->getSubjectPaperList($examid);
        //将考卷数组 与 考试项目数组合并
        $datalist = array_merge($subjectList, $paperList);

        return $this->success_data(['datalist' => $datalist]);
    }

    /**
     * 教学成绩分析-查找列表数据
     * @method  POST
     * @url testscores/teacher-data-list
     * @access public
     * @param Request $request,TestScoreRepositories $TestScoreRepositories
     *
     * @author fandian <fandian@sulida.com>
     * @date   2016-06-20 10:22:53
     * @copyright  2013-2017 sulida.com  Inc. All Rights Reserved
     */
    public function getTeacherDataList(Request $request,TestScoreRepositories $TestScoreRepositories)
    {
        $this->validate($request,[
            'examid'        => 'required|integer',  //考试id
            'subjectid'     => 'required|integer',  //考核项目id 或者 试卷ID
            'subject'       => 'sometimes',         //用于区分考试项目 与 试卷
        ]);

        $subject    = $request->get('subject');     //用于区分考试项目 与 试卷
        $examid     = $request->examid;
        $paperid    = $request->subjectid;
        $datalist   = $TestScoreRepositories->getTeacherData($examid, $paperid, $subject);

        //整合数据，用于图表显示
        $teacherStr = implode(',', $this->nullFullZero($datalist->pluck('teacher_name')->toArray()));
        $maxScore   = implode(',', $this->nullFullZero($datalist->pluck('maxScore')->toArray()));
        $minScore   = implode(',', $this->nullFullZero($datalist->pluck('minScore')->toArray()));
        $avgStr     = implode(',', $this->nullFullZero($datalist->pluck('avgScore')->toArray()));
//        dump($teacherStr, $maxScore, $minScore, $avgStr);

        $data = [
            'datalist'   => $datalist,
            'teacherStr' => $teacherStr,
            'avgStr'     => $avgStr,
            'maxScore'   => $maxScore,
            'minScore'   => $minScore
        ];

        return $this->success_data(['data' => $data]);
    }

    /**
     * 教学成绩分析-班级历史成绩分析
     * @method  POST
     * @url testscores/grade-score-list
     * @access public
     * @param Request $request,TestScoreRepositories $TestScoreRepositories
     * @author weihuiguo <weihuiguo@sulida.com>   <fandian@sulida.com>
     * @date   2016-3-2 17:50:30                    2016-06-29
     * sulida.com Inc. All Rights Reserved
     */
    public function getGradeScoreList(Request $request,TestScoreRepositories $TestScoreRepositories)
    {
        $paper_id   = intval($request->paper_id);   //试卷ID
        $subject_id = intval($request->subject_id); //考试项目ID
        $gradeClass = e($request->classid);         //班级
        //获取当前班级科目历史记录
        list($datalist,$examId) = $TestScoreRepositories->getGradeScore2([], $paper_id, $subject_id, $gradeClass);
        //获取当前班级所在的考试记录  , 获取考试的平均成绩
        list($curent)   = $TestScoreRepositories->getGradeScore2($examId, $paper_id, $subject_id, '');

        //组合平均分
        $allData    = implode(',', $this->nullFullZero($curent->pluck('avgScore')->toArray()));     //整合考试的平均分
        $classData  = implode(',', $this->nullFullZero($datalist->pluck('avgScore')->toArray()));   //整合班级的平均分
        $nameData   = implode(',', $datalist->pluck('name')->toArray());                            //整合考试的名称

        $data = [
            'datalist'  => $datalist,   //列表数据
            'curent'    => $curent,     //列表数据
            'classData' => $classData,  //班级平均分
            'allData'   => $allData,    //考试平均分
            'nameData'  => $nameData    //考试名称
        ];

        return view('osce::admin.statisticalAnalysis.statistics_teach_history',[
            'data'      => $data,
            'classId'   => $gradeClass,
            'subject'   => $subject_id,
            'paperId'   => $paper_id,
            'subname'   => $request->subname
        ]);
    }

    /**
     * 教学成绩分析-班级成绩明细
     * @method  POST
     * @url testscores/grade-detail
     * @access public
     * @param Request $request,TestScoreRepositories $TestScoreRepositories
     * @author weihuiguo <weihuiguo@sulida.com>   <fandian@sulida.com>
     * @date   2016-3-3 10:17:25                    2016-06-29
     * .com Inc. All Rights Reserved
     */
    public function getGradeDetail(Request $request,TestScoreRepositories $TestScoreRepositories,SubjectStatisticsRepositories $subjectStatisticsRepositories)
    {
        $examID  = intval($request->examid);    //考试ID
        $paperID = intval($request->subid);     //试卷ID
        $classid = e($request->classid);        //班级
        $subject = $request->get('subject');    //用于区分考试项目 与 试卷

        //班级成绩明细简介
        $data = $TestScoreRepositories->getExamDetails($examID, $classid, $paperID, $subject);

        if($data){
            $data->time = date('Y-m-d H:i',strtotime($data->begin_dt)).' ~ '.date('H:i',strtotime($data->end_dt));
        }

        //列表数据
        $datalist = $TestScoreRepositories->getGradeDetailList($examID, $paperID, $classid, $subject);
        return view('osce::admin.statisticalAnalysis.statistics_teach_detail',[
            'data'      => $data,
            'datalist'  => $datalist
        ]);
    }

    /**
     * 值为nul 或者空值的，填充为0.00
     * @param $arr
     * @return mixed
     *
     * @author fandian <fandian@sulida.com>
     * @date   2016-07-07 09:30
     * @copyright  2013-2017 sulida.com  Inc. All Rights Reserved
     */
    public function nullFullZero($arr)
    {
        foreach ($arr as &$item) {
            if(is_null($item) || empty($item)){
                $item = '0.00';
            }
        }
        return $arr;
    }
}


















