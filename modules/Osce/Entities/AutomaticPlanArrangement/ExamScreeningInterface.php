<?php
/**
 * Created by PhpStorm.
 * User: j5110
 * Date: 2016/2/14
 * Time: 17:13
 */

namespace modules\Osce\Entities\AutomaticPlanArrangement;


interface ExamScreeningInterface
{
    /*
     * 获取场次列表
     */
    function screenList();

    /*
     * 获取当前正在进行场次
     */
    function screening();

    /*
     * 开始场次
     * 修改状态
     */
    function beginScreen();

    /*
     * 结束场次
     * 修改状态
     */
    function endScreen();
}