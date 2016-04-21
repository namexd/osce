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
        $entities = $this->getStation($exam, $screen);
        //将各个考站的真正时间给到各个对象
        $entities = $this->entityTime($entities);
        //为考站设定考试时间
        $entities = $this->entityMins($entities, $exam->same_time);
        //去重
        $entities = $this->mergeRoom($entities);
        //加上序号
        $entities = $this->setSerialnumber($entities);
        //为考站设定needNum
        foreach ($entities as &$entity) {
            $entity->needNum = 1;
        }

        return $entities;
    }

    function dataBuilder($exam, $screen, $student, $entity, $i)
    {
        $data = [
            'student_id' => is_null($student->id) ? $student->student_id : $student->id,
            'room_id' => $entity->room_id,
            'station_id' => $entity->station_id,
            'exam_id' => $exam->id,
            'exam_screening_id' => $screen->id,
            'begin_dt' => date('Y-m-d H:i:s', $i),
            'serialnumber' => $entity->serialnumber,
            'flow_id' => $entity->flow_id,
            'gradation_order' => $screen->gradation_order
        ];

        return $data;
    }

    function mergeRoom($entities)
    {
        $array = [];
        $entities = $entities->groupBy('station_id');
        foreach ($entities as $entity) {
            if (count($entity) > 1) {
                $array[] = $entity->sortBy('mins')->pop();
            } else {
                $array[] = $entity->pop();
            }
        }

        return collect($array);
    }
}