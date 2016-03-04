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
use Modules\Osce\Repositories\SubjectStatisticsRepositories;
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
    public function getAjaxGetSubject(Request $request,TestScoreRepositories $TestScoreRepositories,SubjectStatisticsRepositories $subjectStatisticsRepositories){
        //获取筛选参数
        $examid = Intval($request->examid);
        $student_id = intval($request->student_id);
        //echo $examid;
        //查找学生所有科目考试数据
            //\DB::connection("osce_mis")->enableQueryLog();

        $singledata = $TestScoreRepositories->getTestSubject($examid,$student_id,'')->toArray();
       // dd(\DB::connection("osce_mis")->getQueryLog());

        //查找所有科目
        $subject = $subjectStatisticsRepositories->subjectDownlist($examid);
        $subjectId = 0;
        if(!empty($subject)){
            $subjectId = $subject->pluck('id');
        }
        //查找当前考试所有科目平均成绩
        $avgdata = $TestScoreRepositories->getTestSubject($examid,'',$subjectId)->toArray();

        $avgInfo = [];
        foreach($singledata as $k=>$v){
            foreach($avgdata as $kk=>$vv){
                if($v['id'] == $vv['id']){
                    $singledata[$k]['timeAvg'] = $subjectStatisticsRepositories->timeTransformation(sprintf('%.2f',$avgdata[$kk]['timeAvg']));
                    $singledata[$k]['scoreAvg'] = sprintf('%.2f',$avgdata[$kk]['scoreAvg']);
                    $avgInfo[] = $vv;
                }
                $avgdata[$kk]['scoreAvg'] = sprintf('%.2f',$vv['scoreAvg']);
            }
            $singledata[$k]['mins'] = $subjectStatisticsRepositories->timeTransformation($v['mins']);
            $singledata[$k]['time'] = $subjectStatisticsRepositories->timeTransformation($v['time']);
        }
        //重新排列 平均成绩数组的顺序
        foreach($avgdata as $v){
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

        //dd($avgInfo);
        $data = [
            'list' => $singledata,//列表
            'singledata' => $singledata,//雷达图学生成绩
            'avgdata' => $avgInfo,//平均成绩
            //'subject' => $subject
        ];
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
    public function studentSubjectList(Request $request,TestScoreRepositories $TestScoreRepositories,SubjectStatisticsRepositories $subjectStatisticsRepositories){
        //获取已考过试的所有学生
        $student_id = $request->student_id;
        //$examid = $request->examid;
        $subid = $request->subid;
        $examid = [];
        //获取学生科目成绩
        $singledata = $TestScoreRepositories->getStudentHistoryScoreCount($student_id,$subid);
        if(!empty($singledata)){
            $examid = $singledata->pluck('exam_id');
        }
        //获取科目平均成绩
        $avgdata = $TestScoreRepositories->getStudentHistoryScoreCount($student_id,$subid,$examid);

        foreach($singledata as $k=>$v){
            foreach($avgdata as $kk=>$vv){
                if($v['id'] == $vv['id']){
                    $singledata[$k]['timeAvg'] = $subjectStatisticsRepositories->timeTransformation(sprintf('%.2f',$vv['timeAvg']));
                    $singledata[$k]['scoreAvg'] = sprintf('%.2f',$vv['scoreAvg']);
                    $singledata[$k]['time'] = date("Y年m月",strtotime($v['begin_dt']));
                    $avgdata[$kk]['mins'] = $subjectStatisticsRepositories->timeTransformation($vv['mins']);
                    unset($singledata[$k]['begin_dt']);
                }else{
                    $singledata[$k]['timeAvg'] = 0;
                    $singledata[$k]['scoreAvg'] = 0;
//                    $singledata[$k]['time'] = date("Y年m月",strtotime($vv['begin_dt']));
//                    unset($singledata[$k]['begin_dt']);
                }
            }
            $singledata[$k]['mins'] = $subjectStatisticsRepositories->timeTransformation($v['mins']);
        }
       // dd($singledata);
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
            $singledata[$kk]['mins'] = $subjectStatisticsRepositories->timeTransformation($vvv['mins']);
        }
        //dd($singledata);
        $data = [
            'list' => $singledata,//列表
            'singledata' => $singledata,//雷达图学生成绩
            'avgdata' => $avgdata//平均成绩
        ];
        //dd($data);
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
     * @url osce.admin.TestScoresController.testScoresCount
     * @access public
     * @param Request $request,TestScoreRepositories $TestScoreRepositories
     * @author weihuiguo <weihuiguo@misrobot.com>
     * @date    2016-3-2 16:51:27 .com Inc. All Rights Reserved
     */
    public function testScoresCount(Request $request,SubjectStatisticsRepositories $SubjectStatisticsRepositories){
        //获取考试的数据
        $examlist = $SubjectStatisticsRepositories->GetExamList();
        return view('osce::admin.statisticalanalysis.statistics_teach_score',[
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
        return $this->success_data(['datalist'=>$datalist]);
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
        $maxScore = '';
        $minScore = '';
        foreach($datalist as $k=>$v){
            if($v->teacher_name){
                $teacherStr .= $v->teacher_name.',';
            }

            if($v->avgScore){
                $avgStr .= sprintf('%.2f',$v->avgScore).',';
            }
            if($v->maxScore){
                $maxScore .= $v->maxScore.',';
            }

            if($v->minScore){
                $minScore .= $v->minScore.',';
            }

            if($v->minScore){
                $datalist[$k]['avgScore'] = sprintf('%.2f',$v->avgScore);
            }
        }
        $data = [
            'datalist' => $datalist,
            'teacherStr' => trim($teacherStr,','),
            'avgStr' => trim($avgStr,','),
            'maxScore' => trim($maxScore,','),
            'minScore' => trim($minScore,',')
        ];
        return $this->success_data(['data'=>$data]);
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
        $subid = $request->subid;
        $examid = $request->examid;
        //获取当前班级历史记录
        $datalist = $TestScoreRepositories->getGradeScore($classId,$subid,$examid)->toArray();
        //获取当前考试记录
        $curent = $TestScoreRepositories->getGradeScore('',$subid,$examid)->toArray();

        $classData = '';
        $allData = '';
        $timeData = '';
        foreach($datalist as $k=>$v){
            foreach($curent as $kk=>$vv){
                if($v['id'] == $vv['id']){
                    //把当前考试平均成绩加入班级中
                    //dd($vv['avgScore']);
                    $datalist[$k]['AllavgScore'] = sprintf('%.2f',$vv['avgScore']);
                }
                $allData .= sprintf('%.2f',$vv['avgScore']).',';
            }
            $classData .= sprintf('%.2f',$v['avgScore']).',';
            $timeData .= $v['name'].',';
            $datalist[$k]['avgScore'] = sprintf('%.2f',$v['avgScore']);
        }
       // dd($datalist);
        $data = [
            'datalist' => $datalist,//列表数据
            'classData' => trim($classData,','),//班级平均分
            'allData' => trim($allData,','),//考试平均分
            'timeData' => trim($timeData,',')//考试时间
        ];
        //dd($datalist);
        return view('osce::admin.statisticalanalysis.statistics_teach_history',[
            'data' => $data,
            'classId'=>$classId,
            'subname' => $request->subname
        ]);
    }

    /**
     * 教学成绩分析-班级成绩明细
     * @method  POST
     * @url testscores/grade-detail
     * @access public
     * @param Request $request,TestScoreRepositories $TestScoreRepositories
     * @author weihuiguo <weihuiguo@misrobot.com>
     * @date   2016-3-3 10:17:25 .com Inc. All Rights Reserved
     */
    public function getGradeDetail(Request $request,TestScoreRepositories $TestScoreRepositories,SubjectStatisticsRepositories $subjectStatisticsRepositories){
        $examID = $request->examid;
        $subjectID = $request->subid;
        $ResultID = $request->resultID;
        //班级成绩明细简介

        $data = $TestScoreRepositories->getExamDetails($examID,$ResultID,$subjectID);
        $data->time = date('Y-m-d H:i',strtotime($data->begin_dt)).' ~ '.date('H:i',strtotime($data->end_dt));
        //列表数据
       $datalist = $TestScoreRepositories->getGradeDetailList($examID,$subjectID);
        foreach($datalist as $k=>$v){
            $datalist[$k]['time'] = $subjectStatisticsRepositories->timeTransformation($v->time);
        }
        return view('osce::admin.statisticalanalysis.statistics_teach_detail',[
            'data' => $data,
            'datalist'=>$datalist
        ]);

    }
}


















