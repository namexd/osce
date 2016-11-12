<?php
/**
 * Created by PhpStorm.
 * User: j5110
 * Date: 2016/2/14
 * Time: 16:33
 */

namespace Modules\Osce\Entities\AutomaticPlanArrangement;

/**
 * 学生类的接口，所有和只能排考中学生相关的类都要实现此接口
 * Interface StudentInterface
 * @package modules\Osce\Entities\AutomaticPlanArrangement
 */
interface StudentInterface
{
    //获取学生状态
    function getStatus();

    //设置学生状态
    function setStatus();

    //获取学生考试进度
    function getProgress();

    //设置学生考试初始科目
    function setIniProgress();

    /*
     * 设置学生考试开始
     * 更新学生考试进度也需要写在这个方法里
     */
    function setExamStart();

    //设置学生考试结束
    function setExamEnd();
}