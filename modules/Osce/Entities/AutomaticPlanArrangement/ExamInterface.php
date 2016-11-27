<?php
/**
 * Created by PhpStorm.
 * User: j5110
 * Date: 2016/2/14
 * Time: 17:21
 */

namespace Modules\Osce\Entities\AutomaticPlanArrangement;


interface ExamInterface
{
    /*
     * 获取当日有的考试列表
     */
//    function getTodayExamList();

    /*
     * 获取当前考试场次列表
     */
    function screenList($examId);

    /*
     * 获取当前正在进行考试
     */
    static function getExaming($examId);

    /*
     * 开始考试
     * 修改状态
     */
    function beginExam($examId);

    /*
     * 结束考试
     * 修改状态
     */
    function endExam($examId);
}