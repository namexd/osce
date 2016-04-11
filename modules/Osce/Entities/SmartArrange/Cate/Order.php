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
    function needStudents($entity, $screen, $exam, $params)
    {
        // TODO: Implement needStudents() method.
        $result = [];

        if ($entity->serialnumber == 1) {
            for ($i = 0; $i < $entity->needNum; $i++) {
                if (count($params['wait']) > 0) {
                    $thisStudent = array_shift(count($params['wait']));
                    if (!is_null($thisStudent)) {
                        $result[] = $thisStudent;
                    }

                    if ($params['total'] > 0) {
                        if (is_array($params['total'])) {
                            $params['wait'] = array_shift($params['total']);
                        } else {
                            $params['wait'] = $params['total']->shift();
                        }
                    }
                }
            }
            return [$result, $params];
        } else {
            $testStudent = $this->orderTestStudent($entity, $screen);
            if (count($testStudent) <= $entity->needNum) {
                $result = $testStudent;
                return [$result, $params];
            } else {
                for ($i = 0; $i < $entity->needNum; $i++) {
                    $result[] = $testStudent->shift();
                }
                return [$result, $params];
            }
        }
    }

    
}