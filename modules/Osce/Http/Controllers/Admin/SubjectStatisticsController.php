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
     * @author yangshaolin <yangshaolin@misrobot.com>
     * @date    2016年2月23日15:43:34
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function SubjectGradeList(Request $request,SubjectStatisticsRepositories $subjectStatisticsRepositories){

         $examid=\Input::get('id');
        //\DB::connection('osce_mis')->enableQueryLog();
        //查询统计所需数据
        $rew = $subjectStatisticsRepositories->GetSubjectStatisticsList($examid);
        //主要用来统计合格的人数
        $rewTwo = $subjectStatisticsRepositories->GetSubjectStatisticsList($examid,true);
        //dd($rewTwo);
        //$queries = \DB::connection('osce_mis')->getQueryLog();
        $standardStr = '';
        $timeAvgStr = '';
        $scoreAvgStr = '';
       // $count= 0;
        //统计合格率
        foreach($rew as $key => $val){

            $rew[$key]['qualifiedPass'] = '0%';
            $rew[$key]['scoreAvg'] =sprintf('%.2f',$val['scoreAvg']);
            $rew[$key]['timeAvg'] =sprintf('%.2f',$val['timeAvg']);
            //给结果展示列表中序号列加入数据
            $rew[$key]['number']=$key+1;
            foreach($rewTwo as $v){
                if($val['subjectId'] == $v['subjectId']){
                    $rew[$key]['qualifiedPass'] = sprintf("%.2f", ($v['studentQuantity']/$val['studentQuantity'])*100).'%';
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
        return  view('osce::admin.statisticalanalysis.statistics_subject',['examlist'=>$examlist,'StrList'=>$StrList,'list'=>$rew]);

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
        $subid = \Input::get('id');
        //查询分析所需数据
        $rew = $subjectStatisticsRepositories->GetSubjectDifficultyStatisticsList($subid);

        //主要用来统计合格的人数
        $rewTwo = $subjectStatisticsRepositories->GetSubjectDifficultyStatisticsList($subid, true);
        //$queries = \DB::connection('osce_mis')->getQueryLog();

        $standardStr = '';
        $qualifiedPass='';
        foreach ($rew as $key => $val) {

            //给结果展示列表中序号列加入数据
            $rew[$key]['number'] = $key + 1;
            $rew[$key]['qualifiedPass'] = '0';
            $rew[$key]['scoreAvg'] =sprintf('%.2f',$val['scoreAvg']);
            $rew[$key]['timeAvg'] =sprintf('%.2f',$val['timeAvg']);
            $rew[$key]['ExamBeginTime'] = substr($val['ExamBeginTime'],0,7);

            foreach ($rewTwo as $v) {
                if ($val['ExamId'] == $v['ExamId']) {
                    $rew[$key]['qualifiedPass'] = sprintf("%.2f", ($v['studentQuantity'] / $val['studentQuantity']) * 100);
                }


            }
            if ($standardStr) {
                $standardStr .= ',' . $val['ExamBeginTime'];
                $qualifiedPass.=','.$val['qualifiedPass'];
            } else {
                $standardStr .= $val['ExamBeginTime'];
                $qualifiedPass.=$val['qualifiedPass'];
            }
             $val['qualifiedPass']=$val['qualifiedPass'].'%';

        }
        $StrList = [
            'standardStr' => $standardStr,
            'qualifiedPass'=>$qualifiedPass
        ];
        //获取有效的考试项目
        $ExamList = $subjectStatisticsRepositories->GetExamList();
        $SubjectList = [];
        if(count($ExamList)>0){
            $SubjectList = $subjectStatisticsRepositories->subjectDownlist($ExamList->pluck('id'));
        }
        //ajax请求判断返回不同数据
        if ($request->ajax()) {
            return $this->success_data(['list' => $rew, 'StrList' => $StrList]);
        }

        return view('osce::admin.statisticalanalysis.statistics_subject_level', ['list' => $rew, 'subjectList' => $SubjectList, 'StrList' => $StrList]);
    }


}