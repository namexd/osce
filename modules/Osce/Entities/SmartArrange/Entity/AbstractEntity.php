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
}