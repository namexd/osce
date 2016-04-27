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
use Illuminate\Support\Collection;

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

    /**
     * 将考试实体加上序号
     * @param Collection $collection
     * @param string $groupBy
     * @param string $sortBy
     * @param bool $desc
     * @return object
     * @author Jiangzhiheng
     * @time 2016-04-13 16:20
     */
    function setSerialnumber(Collection $collection, $groupBy = 'order', $sortBy = 'order', $desc = false)
    {
        if ($desc === false) {
            $collections = $collection->sortBy($sortBy)->groupBy($groupBy);
        } else {
            $collections = $collection->sortByDesc($sortBy)->groupBy($groupBy);
        }


        $result = [];
        $k = 1;
        foreach ($collections as $items) {
            foreach ($items as $item) {
                if ($item->optional == 1) {
                    $item->serialnumber = $k;
                    $k++;
                } else {
                    $item->serialnumber = $k;
                }
                $result[] = $item;
            }
            if ($items->first()->optional == 0) {
                $k++;
            }

        }

        return collect($result);
    }

    protected function mergeRoom(Collection $entities, $field)
    {
        $array = [];
        $tempGroups = $entities->groupBy($field);
        foreach ($tempGroups as $item)
        {
            if (count($item) > 1) {
                $array[] = $item->sortBy('mins')->pop();
            } else {
                $array[] = $item->pop();
            }
        }

        return collect($array);
    }

    /**
     * 将每个大站的第一个序号写进每个实体
     * @author Jiangzhiheng
     * @time 2016-04-26 19:23
     */
    protected function setMinSerialnumber($entities)
    {
        $entities = $entities->groupBy('order');
        $arrays = [];
        foreach ($entities as $key => $entity) {
            foreach ($entity as $k => $item) {
                if ($item->optional == 0) {
                    $item->min_serialnumber = true;
                } else {
                    if ($k == 0) {
                        $item->min_serialnumber = true;
                    } else {
                        $item->min_serialnumber = false;
                    }
                }

                $arrays[] = $item;
            }
        }

        return collect($arrays);
    }
}