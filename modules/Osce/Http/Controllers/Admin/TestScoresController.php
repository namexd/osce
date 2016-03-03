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
        $examlist = Exam::where('status','=',2)->get();
        return view('osce::admin.statisticalanalysis.statistics_student_score',[
            'examlist'=>$examlist
        ]);
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
        return $tester;
    }

    /**
     * 根据考试ID和学生ID对学生科目成绩分析
     * @method  GET
     * @url /osce/admin/testscores/ajax-get-subject
     * @access public
     * @param Request $request,TestScoreRepositories $TestScoreRepositories
     * @author weihuiguo <weihuiguo@misrobot.com>
     * @date    2016年2月26日14:56:58
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function getAjaxGetSubject(Request $request,TestScoreRepositories $TestScoreRepositories){
        //获取筛选参数
        $examid = $request->examid;
        $student_id = $request->student_id;
        //查找学生所有科目考试数据
        $singledata = $TestScoreRepositories->getTestSubject($examid,$student_id)->toArray();
        //查找当前考试所有科目平均成绩
        $avgdata = $TestScoreRepositories->getTestSubject($examid,'')->toArray();
        //dd($avgdata);
        foreach($singledata as $k=>$v){
            foreach($avgdata as $kk=>$vv){
                if($v['id'] == $vv['id']){
                    $singledata[$k]['timeAvg'] = sprintf('%.1f',$avgdata[$kk]['timeAvg']);
                    $singledata[$k]['scoreAvg'] = sprintf('%.1f',$avgdata[$kk]['scoreAvg']);
                }
            }
        }
        //查找所有科目
        $subject = $TestScoreRepositories->getSubList();
        $data = [
            'list' => $singledata,//列表
            'singledata' => $singledata,//雷达图学生成绩
            'avgdata' => $avgdata,//平均成绩
            'subject' => $subject
        ];
       // dd($data);
        return $data;
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
    public function studentSubjectList(Request $request,TestScoreRepositories $TestScoreRepositories){
        //获取已考过试的所有学生
        $student_id = $request->student_id;
        $examid = $request->examid;
        $subid = $request->subid;
        //获取学生科目成绩
        $singledata = $TestScoreRepositories->getStudentScoreCount($student_id,$examid,$subid)->toArray();
        //dd($singledata);
        //获取科目平均成绩
        //dd($singledata);
        $avgdata = $TestScoreRepositories->getStudentScoreCount('',$examid,$subid)->toArray();
        foreach($singledata as $k=>$v){
            foreach($avgdata as $kk=>$vv){
                if($v['id'] == $vv['id']){
                    $singledata[$k]['timeAvg'] = sprintf('%.1f',$vv['timeAvg']);
                    $singledata[$k]['scoreAvg'] = sprintf('%.1f',$vv['scoreAvg']);
                    $singledata[$k]['time'] = date("Y年m月",strtotime($v['begin_dt']));
                    unset($singledata[$k]['begin_dt']);
                }else{
                    $singledata[$k]['timeAvg'] = 0;
                    $singledata[$k]['scoreAvg'] = 0;
//                    $singledata[$k]['time'] = date("Y年m月",strtotime($vv['begin_dt']));
//                    unset($singledata[$k]['begin_dt']);
                }
            }

        }
        foreach($singledata as $kk=>$vvv){
            if($kk == 0){
                $avg = $vvv['scoreAvg'];
                $totle = $vvv['score'];
                $time = $vvv['time'];
                continue;
            }
            $avg .= ','.$vv['scoreAvg'];
            $totle .= ','.$vv['score'];
            $time .= ','.$vv['time'];
        }
        $data = [
            'list' => $singledata,//列表
            'singledata' => $singledata,//雷达图学生成绩
            'avgdata' => $avgdata//平均成绩
        ];
        //dd($avg);
        return view('osce::admin.statisticalanalysis.statistics_student_subject',[
            'data' => $data,
            'avg' => $avg,
            'totle' => $totle,
            'time' => $time,
            'stuname' => $request->stuname,
            'subject' => $request->subject,
        ]);
    }

    /**
     * ajax获取当前考生所有已考科目
     * @method  POST
     * @url /osce/admin/testscores/ajax-get-subjectlist
     * @access public
     * @param Request $request,TestScoreRepositories $TestScoreRepositories
     * @author weihuiguo <weihuiguo@misrobot.com>
     * @date    2016-2-29 09:45:15
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
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
     * @author weihuiguo <weihuiguo@misrobot.com>
     * @date    2016-2-29 09:45:15
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function ajaxGetStudentTestCount(Request $request,TestScoreRepositories $TestScoreRepositories){
        $student_id = $request->student_id;
        $subid = $request->subject_id;
        //获取学生科目成绩
        $singledata = $TestScoreRepositories->getStudentScoreCount($student_id,$subid)->toArray();
        //获取科目平均成绩
        $avgdata = $TestScoreRepositories->getStudentScoreCount('',$subid)->toArray();
        foreach($singledata as $k=>$v){
            if($avgdata[$k]['id'] == $v['id']){
                $singledata[$k]['timeAvg'] = $avgdata[$k]['timeAvg'];
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
     * @url testscores/standardDetails
     * @access public
     * @param Request $request,TestScoreRepositories $TestScoreRepositories
     * @author weihuiguo <weihuiguo@misrobot.com>
     * @date    2016-3-2 16:51:27 .com Inc. All Rights Reserved
     */
    public function testScoresCount(Request $request,TestScoreRepositories $TestScoreRepositories){
        //获取考试的数据
        $examlist = $TestScoreRepositories->getExamList();
        return view('osce::admin.statisticalanalysis.statistics_student_subject',[
            'examlist' => $examlist,
        ]);
    }

    /**
     * 教学成绩分析-请求科目数据
     * @method  POST
     * @url testscores/subject-lists
     * @access public
     * @param Request $request,TestScoreRepositories $TestScoreRepositories
     * @author weihuiguo <weihuiguo@misrobot.com>
     * @date    2016-3-2 17:00:10 .com Inc. All Rights Reserved
     */
    public function getSubjectLists(Request $request,TestScoreRepositories $TestScoreRepositories){
        $examid = $request->examid;
        $datalist = $TestScoreRepositories->getSubjectlist($examid);
        $this->success_data(['datalist'=>$datalist]);
    }

    /**
     * 教学成绩分析-查找列表数据
     * @method  POST
     * @url testscores/teacher-data-list
     * @access public
     * @param Request $request,TestScoreRepositories $TestScoreRepositories
     * @author weihuiguo <weihuiguo@misrobot.com>
     * @date   2016-3-2 17:22:53 .com Inc. All Rights Reserved
     */
    public function getTeacherDataList(Request $request,TestScoreRepositories $TestScoreRepositories){
        $examid = $request->examid;
        $subjectid = $request->subjectid;
        $datalist = $TestScoreRepositories->getTeacherData($examid,$subjectid);
        $teacherStr = '';
        $avgStr = '';
        foreach($datalist as $v){
            if($v->teacher_name){
                $teacherStr .= $v->teacher_name.',';
            }

            if($v->avgScore){
                $avgStr .= sprintf('%.1f',$v->avgScore).',';
            }

        }
        $data = [
            'datalist' => $datalist,
            'teacherStr' => trim($teacherStr,','),
            'avgStr' => trim($avgStr,',')
        ];
        $this->success_data(['data'=>$data]);
    }

    /**
     * 教学成绩分析-班级历史成绩分析
     * @method  POST
     * @url testscores/grade-score-list
     * @access public
     * @param Request $request,TestScoreRepositories $TestScoreRepositories
     * @author weihuiguo <weihuiguo@misrobot.com>
     * @date   2016-3-2 17:50:30 .com Inc. All Rights Reserved
     */
    public function getGradeScoreList(Request $request,TestScoreRepositories $TestScoreRepositories){
        $classId = $request->classid;
        //获取当前班级历史记录
        $datalist = $TestScoreRepositories->getGradeScore($classId)->toArray();
        //获取当前考试记录
        $curent = $TestScoreRepositories->getGradeScore('')->toArray();
        $classData = '';
        $allData = '';
        foreach($datalist as $k=>$v){
            foreach($curent as $kk=>$vv){
                if($v['id'] == $vv['id']){
                    //把当前考试平均成绩加入班级中
                    $datalist[$k]['AllavgScore'] = $vv['avgScore'];
                }
                $allData .= $vv['avgScore'].',';
            }
            $classData .= $v['avgScore'].',';
        }

        $data = [
            'datalist' => $datalist,//列表数据
            'classData' => trim($classData,','),//班级平均分
            'allData' => trim($allData,',')//考试平均分
        ];
        dd($data);
        $this->success_data(['data'=>$data]);
    }
}


















