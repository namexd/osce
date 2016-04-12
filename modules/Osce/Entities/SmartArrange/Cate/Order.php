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
                    $thisStudent = array_shift(count($this->_S_W));
                    if (!is_null($thisStudent)) {
                        $result[] = $thisStudent;
                    }

                    if ($this->_S > 0) {
                        if (is_array($this->_S)) {
                            $this->_S_W = array_shift($this->_S);
                        } else {
                            $this->_S_W = $this->_S->shift();
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

    
}