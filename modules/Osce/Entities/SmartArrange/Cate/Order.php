<?php
/**
 * Created by PhpStorm.
 * User: j5110
 * Date: 2016/4/7
 * Time: 16:53
 */

namespace Modules\Osce\Entities\SmartArrange\Cate;


use Modules\Osce\Entities\Student;
use Modules\Osce\Entities\SmartArrange\Traits\SQLTraits;
use Modules\Osce\Entities\SmartArrange\Traits\SundryTraits;

class Order extends AbstractCate implements CateInterface
{
    use SQLTraits, SundryTraits;

    function needStudents($entity, $screen, $exam, $planSerialRecords = [], $noEndPlanSerialRecords = [])
    {
        // TODO: Implement needStudents() method.
        $result = [];

        if ($entity->serialnumber == 1) {
            for ($i = 0; $i < $entity->needNum; $i++) {
                if (!empty($this->_S_W)) {
                    $thisStudent = array_shift($this->_S_W);
                    if (!is_null($thisStudent)) {
                        $result[] = $thisStudent;
                    }

                    if (!$this->_S->isEmpty()) {
                        $this->_S_W[] = $this->_S->shift();
                    }
                }
            }
            return $result;
        } else {
            $testStudent = $this->orderTestStudent($entity, $planSerialRecords, $noEndPlanSerialRecords);
            if (count($testStudent) <= $entity->needNum) {
                return $testStudent;
            } else {
                for ($i = 0; $i < $entity->needNum; $i++) {
                    $result[] = array_shift($testStudent);
                }
                return $result;
            }
        }
    }

    /**
     * 寻找循序模式需要的考生
     * @param $entity
     * @param $planSerialRecords
     * @param $noEndPlanSerialRecords
     * @return array
     */
    protected function orderTestStudent($entity, $planSerialRecords, $noEndPlanSerialRecords)
    {
        /**
         * 需要查当前的实例是不是第一个
         * 如果等于1，就说明是第一个，直接从侯考区取人
         * 如果不是，就说明前面有流程了
         */
        if ($entity->serialnumber != 1) {
            $tempArrays = $this->orderBeginStudent($entity, $planSerialRecords, $noEndPlanSerialRecords);
            return $tempArrays;
        } else {
            return [];
        }
    }
}