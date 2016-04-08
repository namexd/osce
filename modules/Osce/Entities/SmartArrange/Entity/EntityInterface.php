<?php
/**
 * Created by PhpStorm.
 * User: j5110
 * Date: 2016/4/7
 * Time: 11:13
 */

namespace Modules\Osce\Entities\SmartArrange\Entity;


interface EntityInterface
{
    /**
     * 考试实体，以集合方式返回
     * @param $exam
     * @return mixed
     * @author Jiangzhiheng
     * @time 2016-04-07 11:15
     */
    function entity($exam);

    /**
     * 拼装插入库的数据
     * @param $exam
     * @param $screen
     * @param $student
     * @param $entity
     * @param $i
     * @return mixed
     * @author Jiangzhiheng
     * @time 2016-04-08 14:17
     */
    function dataBuilder($exam, $screen, $student, $entity, $i);

}