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
        $data = [];
        foreach ($result as $key => $items) {
            $gradation = $items->first()->exam_gradation_id;
            foreach ($result as $values) {
                foreach ($values as $value) {
                    if ($value->exam_draft_flow_order != $key && $value->exam_gradation_id == $gradation) {
                        $data[$gradation][$value->exam_draft_flow_order][] = $value->room_id;
                    }
                }
            }
        }
        //判断data中room_id是否重复
        foreach ($data as $item) {
            $array = [];
            foreach ($item as $v) {
                $v = array_unique($v);
                if (count(array_diff($v, $array)) != count($v)) {
                    throw new \Exception('考场安排错误！');
                }
                $array = array_merge($array, $v);
            }
        }

        return true;
    }
}