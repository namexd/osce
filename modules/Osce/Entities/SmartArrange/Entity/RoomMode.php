<?php
/**
 * Created by PhpStorm.
 * User: j5110
 * Date: 2016/4/7
 * Time: 15:53
 */

namespace Modules\Osce\Entities\SmartArrange\Entity;


use Modules\Osce\Entities\SmartArrange\Traits\SQLTraits;
use Modules\Osce\Entities\SmartArrange\Traits\SundryTraits;

class RoomMode extends AbstractEntity implements EntityInterface
{
    use SQLTraits, SundryTraits;

    function entity($exam, $screen)
    {
        // TODO: Implement entity() method.
        $entities = $this->getRoom($exam, $screen);

        //为每个考场写入用时多少
        $entities = $this->entityTime($entities);
        $entities = $this->entityMins($entities, $exam->same_time);

        //去重，将room_id相同的考场合并为一个
        $entities = $this->mergeRoom($entities, 'room_id');
        //加上序号
        $entities = $this->setSerialnumber($entities);
        //加上是否为大站的第一个站
        $entities = $this->setMinSerialnumber($entities);
        //为每个考场写入多少个考站
        foreach ($entities as &$entity) {
            $roomStation = $this->roomStation($exam, $screen, $entity->room_id);

            $entity->needNum = count($roomStation);
        }
        return $entities;
    }

    function dataBuilder($exam, $screen, $student, $entity, $i)
    {
        // TODO: Implement dataBuilder() method.
        return [
            'student_id' => is_null($student->id) ? $student->student_id : $student->id,
            'room_id' => $entity->room_id,
            'station_id' => null,
            'exam_id' => $exam->id,
            'exam_screening_id' => $screen->id,
            'begin_dt' => date('Y-m-d H:i:s', $i),
            'serialnumber' => $entity->serialnumber,
            'flow_id' => $entity->flow_id,
            'gradation_order' => $screen->gradation_order
        ];
    }

}