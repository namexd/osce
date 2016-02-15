<?php
/**
 * Created by PhpStorm.
 * User: j5110
 * Date: 2016/2/14
 * Time: 17:21
 */

namespace modules\Osce\Entities\AutomaticPlanArrangement;


interface ExamInterface
{
    /*
     * 获取当日有的考试列表
     */
    function getTodayExamList();

    /*
     * 获取当前考试场次列表
     */
    function screenList();

    /*
     * 获取当前正在进行考试
     */
    function getExaming();

    /*
     * 开始考试
     * 修改状态
     */
    function beginExam();

    /*
     * 结束考试
     * 修改状态
     */
    function endExam();
}