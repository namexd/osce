<?php
/**
 * Created by PhpStorm.
 * User: j5110
 * Date: 2016/4/7
 * Time: 16:53
 */

namespace Modules\Osce\Entities\SmartArrange\Cate;


use Modules\Osce\Entities\AutomaticPlanArrangement\Student;
use Modules\Osce\Entities\SmartArrange\Traits\SQLTraits;
use Modules\Osce\Entities\SmartArrange\Traits\SundryTraits;

class Order extends AbstractCate implements CateInterface
{
    use SQLTraits, SundryTraits;
    function needStudents($entity, $screen, $exam)
    {
        // TODO: Implement needStudents() method.
        $result = [];

        if ($entity->serialnumber == 1) {
            for ($i = 0; $i < $entity->needNum; $i++) {
                if (count($this->_S_W) > 0) {
                    dump($this->_S_W);
                    $thisStudent = array_shift($this->_S_W);
                    if (!is_null($thisStudent)) {
                        $result[] = $thisStudent;
                    }

                    if (count($this->_S) > 0) {
                        if (is_array($this->_S)) {
                            $this->_S_W[] = array_shift($this->_S);
                        } else {
                            $this->_S_W[] = $this->_S->shift();
                        }
                    }
                }
            }
            return $result;
        } else {
            $testStudent = $this->orderTestStudent($entity, $screen);
            if (count($testStudent) <= $entity->needNum) {
                $result = $testStudent;
                return $result;
            } else {
                for ($i = 0; $i < $entity->needNum; $i++) {
                    $result[] = $testStudent->shift();
                }
                return $result;
            }
        }
    }

    /**
     * 寻找循序模式需要的考生
     * @param $entity
     * @param $screen
     * @return array
     * @throws \Exception
     * @author Jiangzhiheng
     * @time 2016-04-11 11:30
     */
    protected function orderTestStudent($entity, $screen)
    {
        /**
         * 需要查当前的实例是不是第一个
         * 如果等于1，就说明是第一个，直接从侯考区取人
         * 如果不是，就说明前面有流程了
         */
        if ($entity->serialnumber != 1) {
            $tempArrays = $this->orderBeginStudent($screen, $entity->serialnumber);
            if (count($tempArrays) != 0) {
                return Student::whereIn('id', $tempArrays)->get();
            } else {
                return collect([]);
            }
        } else {
            return collect([]);
        }
    }
}