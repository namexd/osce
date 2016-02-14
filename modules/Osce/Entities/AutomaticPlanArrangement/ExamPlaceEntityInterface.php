<?php
/**
 * Created by PhpStorm.
 * User: j5110
 * Date: 2016/2/14
 * Time: 16:56
 */

namespace modules\Osce\Entities\AutomaticPlanArrangement;


interface ExamPlaceEntityInterface
{
    //准备考试，发通知告诉考生
    //进入 准备工作 倒计时
    function prepareTest();

    //开始考试
    //进入 考试 倒计时
    //1个考站或考场一个队列
    //方法内需触发考试结束事件（结束时间，考试人）
    function beginTest();

    //考试结束
    //关闭考试，设置考站或考场为空闲
    function endTest();

    //获取当前考站或考场状态
    function getStatus();
}