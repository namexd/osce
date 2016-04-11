<?php
/**
 * Created by PhpStorm.
 * User: j5110
 * Date: 2016/4/7
 * Time: 15:54
 */

namespace Modules\Osce\Entities\SmartArrange\Entity;


use Modules\Osce\Entities\SmartArrange\Traits\SQLTraits;

class StationMode extends AbstractEntity implements EntityInterface
{
    use SQLTraits;

    /**
     * 返回该场次所对应的实体
     * @param $screen
     * @return mixed
     * @author Jiangzhiheng
     * @time 2016-04-08 17：40
     */
    function entity($exam, $screen)
    {
        // TODO: Implement entity() method.
        //获得该考试下的所有考站
        $entities = $this->getStation($screen);
        //为考站设定考试时间
        $entities = $this->entityMins($entities, $exam->same_time);

        return $entities;
    }
    
    function dataBuilder($exam, $screen, $student, $entity, $i)
    {
        $data = [
            'student_id' => is_null($student->id) ? $student->student_id : $student->id,
            'room_id' => $entity->room_id,
            'station_id' => $entity->id,
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