<?php
/**
 * Created by PhpStorm.
 * User: j5110
 * Date: 2016/4/7
 * Time: 15:53
 */

namespace Modules\Osce\Entities\SmartArrange\Entity;


class RoomMode implements EntityInterface
{
    function entity($exam)
    {
        // TODO: Implement entity() method.

    }

    function dataBuilder($exam, $screen, $student, $entity, $i)
    {
        // TODO: Implement dataBuilder() method.
        $data = [
            'student_id' => is_null($student->id) ? $student->student_id : $student->id,
            'room_id' => $entity->room_id,
            'station_id' => null,
            'exam_id' => $exam->id,
            'exam_screening_id' => $screen->id,
            'begin_dt' => date('Y-m-d H:i:s', $i),
            'serialnumber' => $entity->order,
            'flow_id' => $entity->flow_id,
            'gradation_order' => $screen->gradation_order
        ];

        return $data;
    }
}