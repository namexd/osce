<?php
/**
 * Created by PhpStorm.
 * User: j5110
 * Date: 2016/4/7
 * Time: 17:26
 */

namespace Modules\Osce\Entities\SmartArrange\Traits;




trait SundryTraits
{
    /**
     * 重置考站时间
     * @return mixed
     * @author Jiangzhiheng
     * @time 2016-04-07 19:30
     */
    public function resetStationTime()
    {
        foreach ($this->_E as &$entity) {
            $entity->timer = 0;
        }
    }

    /**
     * 获取流程时间
     * @return int
     * @author Jiangzhiheng
     * @time 2016-04-08 10:24
     */
    public function flowTime()
    {
        $flowTime = 0;
        foreach ($this->_E_F as $v) {
            //如果是数组，先将时间字符串变成时间戳，然后排序，并取最后（最大的数）;
            if (is_array($v->all())) {
                $flowTime += $v->pluck('mins')->sort()->pop();
                //否则就直接加上这个值
            } else {
                $flowTime += $v->mins;
            }
        }
        return $flowTime;
    }

    /**
     * 将学生从总清单中放入侯考区
     * @author Jiangzhiheng
     * @time 2016-04-08 10:53
     */
    public function waitExamQueue()
    {
        $temp = [];
        //依据考试实体数量乘上系数为总数，进行循环
        for ($i = 0; $i < (count($this->_E)) * config('osce.wait_student_num'); ++$i) {
            //将最后的学生弹出，放入到侯考区属性里
            if (count($this->_S) != 0) {
                $temp[] = $this->_S->pop();
            }
        }

        return $temp;
    }

    public function checkStatus($entity, $screen)
    {
        $examPlanRecord = $this->examPlanRecordIsOpenDoor($entity, $screen);

        //如果有，说明是关门状态
        if ($examPlanRecord->isEmpty()) {
            return false;  //开门状态
        } else {
            return true;   //关门状态
        }
    }

}