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
use Modules\Osce\Entities\Exam;
use Modules\Osce\Entities\Subject;
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
    public function SubjectGradeList(request $request,SubjectStatisticsRepositories $subjectStatisticsRepositories){

         $examid=\Input::get('id');
        //\DB::connection('osce_mis')->enableQueryLog();
        //查询统计所需数据
        $rew = $subjectStatisticsRepositories->GetSubjectStatisticsList($examid);
        //主要用来统计合格的人数
        $rewTwo = $subjectStatisticsRepositories->GetSubjectStatisticsList($examid,true);
        //$queries = \DB::connection('osce_mis')->getQueryLog();
        $standardStr = '';
        $timeAvgStr = '';
        $scoreAvgStr = '';
       // $count= 0;
        //统计合格率
        foreach($rew as $key => $val){

            $rew[$key]['qualifiedPass'] = '0%';
            //给结果展示列表中序号列加入数据
            $rew[$key]['number']=$key+1;
            foreach($rewTwo as $v){
                if($val['subjectId'] == $v['subjectId']){
                    $rew[$key]['qualifiedPass'] = sprintf("%.0f", ($v['studentQuantity']/$val['studentQuantity'])*100).'%';
                }
            }
            if($standardStr){
                $standardStr .= ','.$val['title'];
                $timeAvgStr .= ','.$val['timeAvg'];
                $scoreAvgStr .= ','.$val['scoreAvg'];
            }else{
                $standardStr .= $val['title'];
                $timeAvgStr .= $val['timeAvg'];
                $scoreAvgStr .= $val['scoreAvg'];
            }
        }
        $StrList = [
            'standardStr' => $standardStr,
            'timeAvgStr' => $timeAvgStr,
            'scoreAvgStr' => $scoreAvgStr,
                  ];

        $exam = new Exam();
        $examlist= $exam->where('status','=','2')->select('id','name')->orderBy('end_dt','desc')->get()->toarray();

        if($request->ajax()){
            return $this->success_data(['list'=>$rew,'StrList'=>$StrList],1,'成功');
        }
        return  view('osce::admin.statistics_query.subject_statistics',['examlist'=>$examlist,'StrList'=>$StrList,'list'=>$rew]);

    }

    public function SubjectGradeInfo(){
       dd('科目详情');
   }

    /**
     * 科目难度分析列表
     * @method  GET
     * @url /osce/admin/subject-statistics/subject-analyze
     * @access public
     * @param SubjectStatisticsRepositories $subjectStatisticsRepositories
     * @author yangshaolin <yangshaoliin@misrobot.com>
     * @date    2016年2月29日14:20:34
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function  SubjectGradeAnalyze(request $request,SubjectStatisticsRepositories $subjectStatisticsRepositories){
        $subid=\Input::get('id');
        //查询分析所需数据
        $rew = $subjectStatisticsRepositories->GetSubjectDifficultyStatisticsList(74);


        //主要用来统计合格的人数
        $rewTwo = $subjectStatisticsRepositories->GetSubjectDifficultyStatisticsList(74,true);
        //$queries = \DB::connection('osce_mis')->getQueryLog();
        //统计合格率
       // dd($rewTwo);
        $standardStr = '';
        $timeAvgStr = '';
        $scoreAvgStr = '';
        foreach($rew as $key => $val){
            //dd($val);
            //给结果展示列表中序号列加入数据
            $rew[$key]['number']=$key+1;
            $rew[$key]['qualifiedPass'] = '0%';
            foreach($rewTwo as $v){
                if($val['subjectId'] == $v['subjectId']){
                    $rew[$key]['qualifiedPass'] = sprintf("%.0f", ($v['studentQuantity']/$val['studentQuantity'])*100).'%';
                }
            }
            if($standardStr){
                $standardStr .= ','.$val['ExamName'];
                $timeAvgStr .= ','.$val['timeAvg'];
                $scoreAvgStr .= ','.$val['scoreAvg'];
            }else{
                $standardStr .= $val['ExamName'];
                $timeAvgStr .= $val['timeAvg'];
                $scoreAvgStr .= $val['scoreAvg'];
            }

    }
           $StrList = [
            'standardStr' => $standardStr,
            'timeAvgStr' => $timeAvgStr,
            'scoreAvgStr' => $scoreAvgStr
        ];
        $subject = new Subject();
        $subjectlist= $subject->select('id','title')->get()->toarray();
        // dd($StrList);
        //dd($rew);
        //ajax请求判断返回不同数据
          if($request->ajax()){
              return $this->success_data(['list'=>$rew,'StrList'=>$StrList]);
          }
              return  view('osce::admin.statistics_query.subject_statistics',['list'=>$rew,'subjectList'=>$subjectlist,'StrList'=>$StrList]);
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