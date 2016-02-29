<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/2/26 0026
 * Time: 14:30
 */

namespace Modules\Osce\Http\Controllers\Admin;
use Illuminate\Http\Request;
use Modules\Msc\Entities\Student;
use Modules\Osce\Http\Controllers\CommonController;
use Modules\Osce\Entities\Exam;
use Modules\Osce\Repositories\TestScoreRepositories;
class TestScoresController  extends CommonController
{
    /**
     * 考生成绩分析
     * @method  GET
     * @url /osce/admin/testscores/test-score-list
     * @access public
     * @param Request $request
     * @author weihuiguo <weihuiguo@misrobot.com>
     * @date    2016年2月26日14:56:58
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function TestScoreList(Request $request){
        //查找所有考试信息
        $examlist = Exam::get();
        return view('osce::admin.statistics_query.student_statistics');
    }
    /**
     * 根据考试ID查找当前考试下的所有学生ID
     * @method  GET
     * @url /osce/admin/testscores/test-score-list
     * @access public
     * @param Request $request,TestScoreRepositories $TestScoreRepositories
     * @author weihuiguo <weihuiguo@misrobot.com>
     * @date    2016年2月26日14:56:58
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
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
        return response()->json($this->success_data($tester));
    }

    /**
     * 根据考试ID和学生ID对学生科目成绩分析
     * @method  GET
     * @url /osce/admin/testscores/test-score-list
     * @access public
     * @param Request $request,TestScoreRepositories $TestScoreRepositories
     * @author weihuiguo <weihuiguo@misrobot.com>
     * @date    2016年2月26日14:56:58
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function postAjaxGetSubject(Request $request,TestScoreRepositories $TestScoreRepositories){
        //获取筛选参数
        $examid = $request->examid;
        $student_id = $request->student_id;
        //查找学生所有科目考试数据
        $singledata = $TestScoreRepositories->getTestSubject($examid,$student_id)->toArray();
        //查找当前考试所有科目平均成绩
        $avgdata = $TestScoreRepositories->getTestSubject($examid,'')->toArray();
        foreach($singledata as $k=>$v){
            if($avgdata[$k]['id'] == $v['id']){
                $singledata[$k]['timeAvg'] = $avgdata[$k]['timeAvg'];
                $singledata[$k]['scoreAvg'] = $avgdata[$k]['scoreAvg'];
            }
        }
        dd($singledata);
    }
    /**
     * 考生成绩分析
     * @method  GET
     * @url /osce/admin/testscores/student-subject-list
     * @access public
     * @param Request $request,TestScoreRepositories $TestScoreRepositories
     * @author weihuiguo <weihuiguo@misrobot.com>
     * @date    2016年2月26日14:56:58
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function studentSubjectList(TestScoreRepositories $TestScoreRepositories){
        //获取已考过试的所有学生
        $studentList = $TestScoreRepositories->getStudent();
        return view('osce::admin.statistics_query.teach_statistics');
    }

    /**
     * ajax获取当前考生所有已考科目
     * @method  POST
     * @url /osce/admin/testscores/test-score-list
     * @access public
     * @param Request $request,TestScoreRepositories $TestScoreRepositories
     * @author weihuiguo <weihuiguo@misrobot.com>
     * @date    2016-2-29 09:45:15
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function ajaxGetSubject(Request $request,TestScoreRepositories $TestScoreRepositories){
        $stuid = $request->student_id;
        $studentSublist = $TestScoreRepositories->getStudentSubject($stuid);
        dd($studentSublist);
    }

    /**
     * ajax统计当前考生科目成绩
     * @method  POST
     * @url ajax-get-student-test-count
     * @access public
     * @param Request $request,TestScoreRepositories $TestScoreRepositories
     * @author weihuiguo <weihuiguo@misrobot.com>
     * @date    2016-2-29 09:45:15
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function ajaxGetStudentTestCount(Request $request,TestScoreRepositories $TestScoreRepositories){
        $student_id = $request->student_id;
        $subid = $request->subject_id;
        $data = $TestScoreRepositories->getStudentScoreCount($student_id,$subid);
    }

    /**
     * 考生成绩和科目成绩详情
     * @method  POST
     * @url ajax-get-student-test-count
     * @access public
     * @param Request $request,TestScoreRepositories $TestScoreRepositories
     * @author weihuiguo <weihuiguo@misrobot.com>
     * @date    2016-2-29 15:12:37
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function getTesterScoreDetail(Request $request,TestScoreRepositories $TestScoreRepositories){
        //根据传过来的ID和类型判断是成绩还是科目
        $id = $request->id;
        $type = $request->type;
        $studentid = $request->student_id;
        $datalist = $TestScoreRepositories->getStudentDataList($studentid,$id,$type);
    }
}


















