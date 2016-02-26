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

        dd($rew->toArray());

     /*    $exam =new Exam();
          $examlist =$exam->select(['name'])->get();
          dd($examlist);*/

    }

    public function SubjectGradeInfo(){
       dd('科目详情');
   }

    public function  SubjectGradeAnalyze(){
        //dd('科目难度分析');
        $subject = new Subject();
        $subjectlist= $subject->select('title')->get()->toarray();
        //把二维数组转换为一维数组
        $list=array();
        foreach ($subjectlist as $k=>$v) {
            $list[] =$v['title'];
       }
        dd($list);
    }


}