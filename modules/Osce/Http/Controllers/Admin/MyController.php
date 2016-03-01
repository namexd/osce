<?php
/**
 * Created by PhpStorm.
 * @author tangjun <tangjun@misrobot.com>
 * @date 2016-02-23 14:28
 * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
 */

namespace Modules\Osce\Http\Controllers\Admin;
use Modules\Osce\Http\Controllers\CommonController;
use Modules\Osce\Repositories\MyRepositories;
use Modules\Osce\Entities\Exam;
use Modules\Osce\Entities\Subject;
use Illuminate\Http\Request;

/**
 * Class SubjectStatisticsController
 * @package Modules\Osce\Http\Controllers\Admin
 */
class MyController  extends CommonController
{

    /**
     * 考站成绩分析列表
     * @method  GET
     * @url /osce/admin/subject-statistics/station-grade-list
     * @access public
     * @param SubjectStatisticsRepositories $subjectStatisticsRepositories
     * @author tangjun <tangjun@misrobot.com>
     * @date    2016年2月23日15:43:34
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function stationGradeList(Request $request,MyRepositories $subjectStatisticsRepositories){
        //获取考试列表信息
        $examList = $subjectStatisticsRepositories->GetExamList();
        $examInfo = '';
        if(count($examList)>0){
            foreach($examList as $k=>$v){
                $examInfo[$k]['id'] = $v->id;
                $examInfo[$k]['name'] = $v->name;
            }
        }
        //获取科目列表信息
        $subjectList = $subjectStatisticsRepositories->GetSubjectList();
        $subjectInfo = '';
        if(count($subjectList)>0){
            foreach($subjectList as $k=>$v){
                $subjectInfo[$k]['id'] = $v->id;
                $subjectInfo[$k]['title'] = $v->title;
            }
        }
        //验证
        $this->validate($request, [
            'examId' => 'sometimes|int',//考试编号
            'subjectId' => 'sometimes|int',//科目编号
        ]);
        $examId = $request->input('examId',0);
        $subjectId = $request->input('subjectId',0);
        //获取考站分析列表
        $list = $subjectStatisticsRepositories->GetSubjectStationStatisticsList($examId, $subjectId);
        $datas = [];
        $stationNameStr = '';
        $scoreAvgStr = '';
        if(count($list) > 0){
            foreach($list as $k=>$item){
                $datas[] = [
                    'number'           => $k+1,//序号
                    'stationId'        => $item->stationId,//考站id
                    'stationName'      => $item->stationName,//考站名称
                    'teacherName'      => $item->teacherName,//评分老师
                    'examMins'          => $item->examMins,//考试限时
                    'timeAvg'           => $item->timeAvg,//平均耗时
                    'scoreAvg'          => $item->scoreAvg,//平均成绩
                    'studentQuantity'  => $item->studentQuantity,//考试人数
                ];
                if($stationNameStr){
                    $stationNameStr .= ','.$item->stationName;
                    $scoreAvgStr .= ','.$item->scoreAvg;
                }else{
                    $stationNameStr .= $item->stationName;
                    $scoreAvgStr .= $item->scoreAvg;
                }
            }
        }

        $StrList = [
            'stationNameStr' => $stationNameStr,
            'scoreAvgStr' => $scoreAvgStr,
        ];

        if ($request->ajax()) {
            return $this->success_data(['stationList'=>$datas,'StrList'=>$StrList]);
        }
        //dd($datas);
        //将数据展示到页面
        return view('osce::admin.statistics_query.examation_statistics', [
            'examInfo' =>$examInfo ,//考试列表
            'subjectInfo' =>$subjectInfo ,//科目列表
            'stationList'=>$datas, //考站成绩分析列表
            'StrList'=>$StrList
        ]);
    }

    /**
     * 考核点分析列表
     * @method  GET
     * @url /osce/admin/subject-statistics/standard-grade-list
     * @access public
     * @param MyRepositories $subjectStatisticsRepositories
     * @author tangjun <tangjun@misrobot.com>
     * @date    2016年2月23日15:43:34
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function standardGradeList(Request $request,MyRepositories $subjectStatisticsRepositories){
        //获取考试列表信息
        $examList = $subjectStatisticsRepositories->GetExamList();
        $examInfo = '';
        if(count($examList)>0){
            foreach($examList as $k=>$v){
                $examInfo[$k]['id'] = $v->id;
                $examInfo[$k]['name'] = $v->name;
            }
        }
        //获取科目列表信息
        $subjectList = $subjectStatisticsRepositories->GetSubjectList();
        $subjectInfo = '';
        if(count($subjectList)>0){
            foreach($subjectList as $k=>$v){
                $subjectInfo[$k]['id'] = $v->id;
                $subjectInfo[$k]['title'] = $v->title;
            }
        }
        //验证
        $this->validate($request, [
            'examId' => 'sometimes|int',//考试编号
            'subjectId' => 'sometimes|int',//科目编号
        ]);
        $examId = $request->input('examId');
        $subjectId = $request->input('subjectId');
        //查询考核点分析所需数据
        $rew = $subjectStatisticsRepositories->GetSubjectStandardStatisticsList('326', '52');
        //统计合格的人数
        $rewTwo = $subjectStatisticsRepositories->GetSubjectStandardStatisticsList('326', '52',true);
        $datas = [];
        if(count($rew) > 0){
            foreach($rew as $key=>$val){
                $rew[$key]['qualifiedPass'] = '0%';
                //统计合格率
                if(count($rewTwo) > 0){
                    foreach($rewTwo as $v){
                        if($val['pid'] == $v['pid']){
                            //$v['studentQuantity']:合格人数，$val['studentQuantity']总人数
                            $rew[$key]['qualifiedPass'] = sprintf("%.0f", ($v['studentQuantity']/$val['studentQuantity'])*100).'%';//合格率
                        }
                    }
                }
                $datas[] = [
                    'number'         =>$key+1, //序号
                    'standardContent'     => $val->standardContent,//考核点名称
                    'pid'                   => $val->pid,//评分标准父编号
                    'scoreAvg'             => $val->scoreAvg,//平均成绩
                    'studentQuantity'     => $val->studentQuantity,//考试人数
                    'qualifiedPass'       => $val->qualifiedPass,//合格率
                ];
            }
        }
        dd($datas);
        //将数据展示到页面
        return view('osce::admin.resourcemanage.standardGradeList', [
            'examInfo'      =>$examInfo ,//考试列表
            'subjectInfo' =>$subjectInfo ,//科目列表
            'standardList' =>$datas //考核点分析列表
        ]);
    }

    /**
     * 考核点查看（详情）
     * @method  GET
     * @url /osce/admin/subject-statistics/standardDetails
     * @access public
     * @param MyRepositories $subjectStatisticsRepositories
     * @author tangjun <tangjun@misrobot.com>
     * @date    2016年2月23日15:43:34
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function standardDetails(Request $request,MyRepositories $subjectStatisticsRepositories){
        $standardPid = urldecode(e($request->input('standardPid')));
        //查询考核点分析所需数据
        $result = $subjectStatisticsRepositories->GetStandardDetails('558');
        //所点击的考核点的子考核点对应的数据
        $datainfo=[];
        if(count($result['data'])>0){
            foreach($result['data'] as $k=>$v){
                $datainfo[$k]['number'] = $k+1;//序号
                $datainfo[$k]['content'] = $v->content;//考核内容
                $datainfo[$k]['standardScore'] = $v->standardScore; //总分
                $datainfo[$k]['grade'] = $v->grade; //成绩
            }
        }
        //所点击的考核点对应的数据
        $datainfoPid = [];
        if($result['pidData']){
            $datainfoPid['pid'] = $result['pidData']->pid;//考核内容
            $datainfoPid['content'] = $result['pidData']->content;//考核内容
            $datainfoPid['totalScore'] = $result['pidData']->totalScore; //总分
            if(count($result['data'])>0){
                $datainfoPid['totalGrade'] = $result['data'][0]->totalGrade; //总成绩
            }
        }
        dd($datainfo);
        dd($datainfoPid);
        //die(json_encode($datainfo));
    }
}
