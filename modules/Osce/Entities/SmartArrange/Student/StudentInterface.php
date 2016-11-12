<?php
/**
 * Created by PhpStorm.
 * User: j5110
 * Date: 2016/4/7
 * Time: 10:50
 */

namespace Modules\Osce\Entities\SmartArrange\Student;


interface StudentInterface
{
    /**
     * 获取学生的实例
     * @return mixed
     * @author Jiangzhiheng
     * @time 2016-04-07 11:02
     */
    function get($exam);
}