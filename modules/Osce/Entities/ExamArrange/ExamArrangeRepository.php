<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/4/14 0014
 * Time: 16:21
 */

namespace Modules\Osce\Entities\ExamArrange;


use Illuminate\Database\Eloquent\Collection;
use Modules\Osce\Entities\ExamArrange\Traits\SqlTraits;
use Modules\Osce\Entities\ExamArrange\Traits\SundryTraits;
use Modules\Osce\Entities\Room;
use Modules\Osce\Entities\Station;

class ExamArrangeRepository extends AbstractExamArrange
{
    use SqlTraits, SundryTraits;
    /**
     * 返回实体类的类名，带命名空间
     * @method GET
     * @url /msc/admin/resources-manager/路径名/getModel
     * @access public
     *
     * @param Request $request post请求<br><br>
     * <b>post请求字段：</b>
     * * string        参数英文名        参数中文名(必须的)
     * * string        参数英文名        参数中文名(必须的)
     * * string        参数英文名        参数中文名(必须的)
     * * string        参数英文名        参数中文名(必须的)
     *
     * @return ${response}
     *
     * @version 1.0
     * @author zhouqiang <zhouqiang@misrobot.com>
     * @date ${DATE} ${TIME}
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function getModel()
    {
        return 'Modules\Osce\Entities\ExamArrange\ExamArrange';
    }

    /**
     * 检查传入的数据的合理性
     * @author Jiangzhiheng
     * @time 2016-04-14 17:27
     */
    function checkData($examId, $field = 'station_id', Collection $collection = null)
    {
        if (is_null($collection)) {
            //获取该场考试的数据
            $data = $this->checkExamArrange($examId);

            //打包数据，用考试阶段来打包
            $result = $data->groupBy('exam_gradation_id');

            //遍历之，查看其中是否有相同的考站
            foreach ($result as $item) {
                $entityIds = $item->pluck($field);
                $uniEntityIdsIds = $entityIds->unique();
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
            return $data;
        } else {
            $data = $collection;
            //打包数据，用考试阶段来打包
            $result = $data->groupBy('exam_gradation_id');

            //遍历之，查看其中是否有相同的考站
            foreach ($result as $item) {
                $entityIds = $item->pluck($field);
                $uniEntityIdsIds = $entityIds->unique();
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
            return $data;
        }
    }
    





}