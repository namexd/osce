<?php
/**
 * Created by PhpStorm.
 * @author tangjun <tangjun@misrobot.com>
 * @date 2016-02-23 14:28
 * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
 */

namespace Modules\Osce\Http\Controllers\Admin;
use Modules\Osce\Http\Controllers\CommonController;
use Modules\Osce\Repositories\SubjectStatisticsRepositories;
use Illuminate\Http\Request;

/**
 * Class SubjectStatisticsController
 * @package Modules\Osce\Http\Controllers\Admin
 */
class SubjectStatisticsController  extends CommonController
{


    /**
     * 科目成绩分析列表
     * @method  GET
     * @url /osce/admin/subject-statistics/subject-grade-list
     * @access public
     * @param SubjectStatisticsRepositories $subjectStatisticsRepositories
     * @author tangjun <tangjun@misrobot.com>
     * @date    2016年2月23日15:43:34
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function SubjectGradeList(SubjectStatisticsRepositories $subjectStatisticsRepositories){

        //\DB::connection('osce_mis')->enableQueryLog();
        //查询统计所需数据
        $rew = $subjectStatisticsRepositories->GetSubjectStatisticsList(274);
        //主要用来统计合格的人数
        $rewTwo = $subjectStatisticsRepositories->GetSubjectStatisticsList(274,true);
        //$queries = \DB::connection('osce_mis')->getQueryLog();
        //统计合格率
        foreach($rew as $key => $val){
            $rew[$key]['qualifiedPass'] = '0%';
            foreach($rewTwo as $v){
                if($val['subjectId'] == $v['subjectId']){

                    $rew[$key]['qualifiedPass'] = sprintf("%.0f", ($v['studentQuantity']/$val['studentQuantity'])*100).'%';
                }
            }
        }
        dd($subjectStatisticsRepositories->GetSubjectStandardStatisticsList(275,75,true));
    }

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
    public function stationGradeList(Request $request,SubjectStatisticsRepositories $subjectStatisticsRepositories){
        //验证
        $this->validate($request, [
            'examId' => 'sometimes|int',//考试编号
            'subjectId' => 'sometimes|int',//科目编号
        ]);
        $examId = $request->input('examId');
        $subjectId = $request->input('subjectId');
        // 客户列表信息
        $list = $subjectStatisticsRepositories->GetSubjectStationStatisticsList('274', '74');
        $datas = [];
        if(count($list) > 0){
            foreach($list as $item){
                $datas[] = [
                    'stationId'        => $item->stationId,//考站id
                    'stationName'      => $item->stationName,//考站名称
                    'teacherName'      => $item->teacherName,//评分老师
                    'examMins'          => $item->examMins,//考试限时
                    'timeAvg'           => $item->timeAvg,//平均耗时
                    'scoreAvg'          => $item->scoreAvg,//平均成绩
                    'studentQuantity'  => $item->studentQuantity,//考试人数
                ];
            }
            $info = $datas;
        }else{
            $info = '';
        }
        dd($info);
        //return view('osce::admin.exammanage.score_query')->with(['examResults'=>$examResults,'stations'=>$stations);
    }

    /**
     * 考核点分析列表
     * @method  GET
     * @url /osce/admin/subject-statistics/standard-grade-list
     * @access public
     * @param SubjectStatisticsRepositories $subjectStatisticsRepositories
     * @author tangjun <tangjun@misrobot.com>
     * @date    2016年2月23日15:43:34
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function standardGradeList(Request $request,SubjectStatisticsRepositories $subjectStatisticsRepositories){
        //验证
        $this->validate($request, [
            'examId' => 'sometimes|int',//考试编号
            'subjectId' => 'sometimes|int',//科目编号
        ]);
        $examId = $request->input('examId');
        $subjectId = $request->input('subjectId');
        // 客户列表信息
        $list = $subjectStatisticsRepositories->GetSubjectStandardStatisticsList('274', '74');
        dd($list);
        $datas = [];
        if(count($list) > 0){
            foreach($list as $item){
                $datas[] = [
                    'pid'                => $item->pid,//评分标准父编号
                    'scoreAvg'          => $item->scoreAvg,//平均成绩
                    'studentQuantity'  => $item->studentQuantity,//考试人数

                ];
            }
            $info = $datas;
        }else{
            $info = '';
        }
        dd($info);
        //return view('osce::admin.exammanage.score_query')->with(['examResults'=>$examResults,'stations'=>$stations);
    }
}


















