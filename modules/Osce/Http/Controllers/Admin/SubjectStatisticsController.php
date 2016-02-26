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
     * @param StatisticsRepositories $statisticsRepositories
     * @author tangjun <tangjun@misrobot.com>
     * @date    2016年2月23日15:43:34
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function SubjectGradeList(SubjectStatisticsRepositories $subjectStatisticsRepositories){

        \DB::connection('osce_mis')->enableQueryLog();
        $rew = $subjectStatisticsRepositories->GetSubjectStatisticsList(274);
        $rewTwo = $subjectStatisticsRepositories->GetSubjectStatisticsList(274,true);
        $queries = \DB::connection('osce_mis')->getQueryLog();
        echo '<pre>';
        foreach($rew as $key => $val){
            $rew[$key]['qualifiedPass'] = '0%';
            foreach($rewTwo as $v){
                if($val['subjectId'] == $v['subjectId']){

                    $rew[$key]['qualifiedPass'] = sprintf("%.0f", ($v['studentQuantity']/$val['studentQuantity'])*100).'%';
                }
            }
        }
        dd($rew->toArray());
    }
}