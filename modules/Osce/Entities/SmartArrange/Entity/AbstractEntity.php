<?php
/**
 * Created by PhpStorm.
 * User: j5110
 * Date: 2016/4/11
 * Time: 17:49
 */

namespace Modules\Osce\Entities\SmartArrange\Entity;


use Modules\Osce\Entities\SmartArrange\Traits\SQLTraits;
use Modules\Osce\Entities\SmartArrange\Traits\SundryTraits;

abstract class AbstractEntity
{
    use SQLTraits, SundryTraits;

    function entityMins($entities, $sameTime)
    {
        //获得考试实体们对应的mins
        switch ($sameTime) {
            case 0:

                return $entities;
            case 1:
                $mins = $entities->pluck('mins')->toArray();
                $min = $this->mins($mins);
                foreach ($entities as &$entity) {
                    $entity->mins = $min;
                }

                return $entities;
            default:
                throw new \Exception('系统错误，请重试！', -20);
                break;
        }
    }

    /**
     * 将考站对应的时间写进考站
     * @param $entities
     * @throws \Exception
     * @author Jiangzhiheng
     * @time 2016-04-18 18:21
     */
    function entityTime($entities)
    {
        foreach ($entities as &$entity) {
//            switch ($entity->station_type) {
//                case 2:
//                    $entity->mins = $this->getTheoryMins($entity)->length;
//                    break;
//                case 1 || 3:
//
//                    break;
//                default:
//                    throw new \Exception('系统异常！', -85);
//            }
            $entity->mins = is_null($entity->length) ? $entity->mins : $entity->length;
        }

        return $entities;
    }
}