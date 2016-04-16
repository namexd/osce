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
     * 寻找相同的考站或考场
     * @param $result
     * @param $field
     * @throws \Exception
     * @author Jiangzhiheng
     * @time 2016-04-15 10:24
     */
    public function checkSameEntity($result, $field)
    {
        foreach ($result as $item) {
            $entityIds = $item->pluck($field);
            $uniEntityIdsIds = collect(array_unique($entityIds->toArray()));

            if (count($entityIds) != count($uniEntityIdsIds)) {
                $entityId = $this->getDiff($entityIds, $uniEntityIdsIds);
                switch ($field) {
                    case 'station_id':
                        $entityName = Station::findOrFail($entityId)->name;
                        break;
                    case 'room_id':
                        $entityName = Room::findOrFail($entityId)->name;
                        break;
                    default:
                        throw new \Exception('系统异常，请重试');
                        break;
                }
                throw new \Exception('当前考试安排中' . $entityName . '出现了多次');
            }
        }
        return true;
    }
}