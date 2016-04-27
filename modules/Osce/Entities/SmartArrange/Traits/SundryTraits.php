<?php
/**
 * Created by PhpStorm.
 * User: j5110
 * Date: 2016/4/7
 * Time: 17:26
 */

namespace Modules\Osce\Entities\SmartArrange\Traits;

use Symfony\Component\VarDumper\Dumper\DataDumperInterface;

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
        foreach ($this->_E_F as $value) {
//        foreach ($this->_E as $v) {
            //如果是数组，先将时间字符串变成时间戳，然后排序，并取最后（最大的数）;
            if (is_array($value->all())) {
                $flowTime += $value->pluck('mins')->sort()->pop();
                //否则就直接加上这个值
            } else {
                $flowTime += $value->mins;
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
        for ($i = 0; $i < $this->stationCount * config('osce.wait_student_num'); ++$i) {
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
//        dump($examPlanRecord);
        //如果有，说明是关门状态
        if ($examPlanRecord->isEmpty()) {
            return false;  //开门状态
        } else {
            return true;   //关门状态
        }
    }

    /**
     * 为数组的最大值求取最大值
     * @param array $mins
     * @return mixed
     * @author Jiangzhiheng
     * @time 18:08
     */
    function mins(array $mins)
    {
        sort($mins);
        return array_pop($mins);
    }

    /**
     * 顺序模式下是否有符合要求的学生
     * @param $screen
     * @param $serialnumber
     * @author Jiangzhiheng
     * @time 2016-04-11 11:05
     */
    function orderBeginStudent($screen, $entity)
    {
        try {
            $prevSerial = $this->prevSerial($screen, $entity->serialnumber);

            $thisSerial = $this->thisSerial($screen, $entity->serialnumber);

            //求取差集
            return array_diff($prevSerial->toArray(), $thisSerial->toArray());
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    /**
     * 轮询模式下需要的学生
     * @param $screen
     * @param $entity
     * @param $last
     * @return mixed
     * @author Jiangzhiheng
     * @time 2016-04-26 20:24
     */
    function pollStudents($screen, $entity, $last)
    {
        if ($entity->serialnumber - 1 <= 0) {
            $prevSerial = $this->thisSerial($screen, $last);
            $thisSerial = $this->thisSerial($screen, $entity->serialnumber);
            $thisNotSerial = $this->thisNotSerial($screen, $entity->serialnumber);
            $a = array_diff($prevSerial->toArray(), $thisSerial->toArray(), $thisNotSerial->toArray());
//            if (count($a) != 0) {
//                dump($a);
//            }
            return $a;
        } else {
            $prevSerial = $this->prevSerial($screen, $entity->serialnumber);
            $thisSerial = $this->thisSerial($screen, $entity->serialnumber);
            //求取差集
            return array_diff($prevSerial->toArray(), $thisSerial->toArray());
        }



    }

}