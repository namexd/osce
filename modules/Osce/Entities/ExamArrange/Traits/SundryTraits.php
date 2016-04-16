<?php
/**
 * Created by PhpStorm.
 * User: j5110
 * Date: 2016/4/14
 * Time: 18:19
 */

namespace Modules\Osce\Entities\ExamArrange\Traits;


use Illuminate\Database\Eloquent\Collection;
use Modules\Osce\Entities\Station;
use Modules\Osce\Entities\Room;

trait SundryTraits
{
    /**
     * 获取集合与其去重集合的差
     * @param Collection $data1
     * @param Collection $data2
     * @return mixed
     * @author Jiangzhiheng
     * @time 2016-04-14 18:23
     */
    public function getDiff($data1, $data2)
    {
        $keys1 = $data1->keys();
        $keys2 = $data2->keys();

        $diff = collect($keys1)->diff($keys2);
        return $diff = $diff->first();
    }

    /**
     * 寻找相同的考站
     * @param $result
     * @param $field
     * @throws \Exception
     * @author Jiangzhiheng
     * @time 2016-04-15 10:24
     */
    public function checkSameEntity($result, $field = 'station_id')
    {
        foreach ($result as $item) {
            $entityIds = $item->pluck($field);
            $uniEntityIdsIds = collect(array_unique($entityIds->toArray()));

            if (count($entityIds) != count($uniEntityIdsIds)) {
                throw new \Exception('当前考试安排不合法');
            }
        }
        return true;
    }

    /**
     * 判断考场是否合乎规则
     * @param $result
     * @throws \Exception
     */
    function checkSameRoom($result)
    {
        $gradation = null;
        $draft = null;
        $data = [];
        foreach ($result as $items) {
            $draft = $items->first()->exam_draft_flow_order;
            $gradation = $items->first()->exam_gradation_id;
            foreach ($result as $values) {
                foreach ($values as $value) {
                    if ($value->exam_draft_flow_id != $draft && $value->exam_gradation_id == $gradation) {
                        $data[] = $value;
                    }
                }
            }
            //判断data中room_id是否重复
            $roomId = collect($data)->pluck('room_id');
            $uniRoomId = array_unique($roomId->toArray());
            if (count($roomId) != count($uniRoomId)) {
                throw new \Exception('考场安排不符规则');
            }


        }
    }
}