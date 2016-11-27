<?php
/**
 * Created by PhpStorm.
 * User: j5110
 * Date: 2016/2/14
 * Time: 17:13
 */

namespace Modules\Osce\Entities\AutomaticPlanArrangement;


interface ExamScreeningInterface
{
    /*
     * 获取当前正在进行场次
     */
    function screening($examId);

    /*
     * 开始场次
     * 修改状态
     */
    function beginScreen($examId);

    /*
     * 结束场次
     * 修改状态
     */
    function endScreen($examId);
}