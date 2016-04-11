<?php
/**
 * Created by PhpStorm.
 * User: j5110
 * Date: 2016/4/7
 * Time: 16:49
 */

namespace Modules\Osce\Entities\SmartArrange\Cate;


use Modules\Osce\Entities\SmartArrange\Traits\SQLTraits;
use Modules\Osce\Entities\SmartArrange\Traits\SundryTraits;

class Poll extends AbstractCate implements CateInterface
{
    use SQLTraits, SundryTraits;
    function needStudents($entity, $screen, $exam, $params)
    {
        // TODO: Implement needStudents() method.
        $testStudnts = $this->pollTestStudents($entity, $screen, $params);

        //申明数组
        $result = [];

        /*
         * 获取当前实体需要几个考生 $station->needNum
         * 从正在考的学生里找到对应个数的考生
         * 如果该考生已经考过了这个流程，就忽略掉
         */
        $result = $this->studentNum($entity, $testStudnts, $result);

        if (count($result) < $entity->needNum) {
            $hasStudent = $entity->needNum - count($result);
            for ($i = 0; $i <= $hasStudent; $i) {
                if (count($params['wait']) > 0) {
                    $thisStudent = array_shift($params['wait']);
                    if (!is_null($thisStudent)) {
                        $result[] = $thisStudent;
                    }
                    if (count($params['total']) > 0) {
                        if (is_array($params['total'])) {
                            $params['wait'][] = array_shift($params['total']);
                        } else {
                            $params['wait'][] = $params['total']->shift();
                        }
                    }
                }
            }
            return [$result, $params];
        }
        return [$result, $params];
    }
}