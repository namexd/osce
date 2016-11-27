<?php
/**
 * Created by PhpStorm.
 * User: j5110
 * Date: 2016/5/2
 * Time: 14:18
 */

namespace Modules\Osce\Entities\Drawlots;

use Modules\Osce\Entities\ExamDraft;
use Modules\Osce\Entities\ExamQueue;
use Modules\Osce\Entities\ExamScreening;

class Station
{
    /**
     * 将考场下面有多少考站查出来
     * @access public
     * @version 3.6
     * @author JiangZhiheng <JiangZhiheng@163.com>
     * @time 2016-05-02
     * @copyright 2013-2016 MIS 163.com Inc. All Rights Reserved
     */
    public function site($examId, $roomId, $screenId)
    {
        return ExamDraft::screening()
            ->join('exam_order', 'exam_order.exam_screening_id', '=', 'exam_screening.id')
            ->where('exam_screening.id', $screenId)
            ->where('exam_gradation.exam_id', $examId)
            ->where('exam_draft.room_id', $roomId)
            ->select(
                'exam_draft.station_id as station_id'
            )->distinct()
            ->get();
    }


    /**
     * 获取已经使用了的考站
     * @access public
     * @param $screenId
     * @param $roomId
     * @return mixed
     * @version 3.6
     * @author JiangZhiheng <JiangZhiheng@163.com>
     * @time 2016-05-02
     * @copyright 2013-2016 MIS 163.com Inc. All Rights Reserved
     */
    public function usedStation($screenId, $roomId)
    {
        return ExamQueue::usedStations($screenId, $roomId)
            ->select('station_id')
            ->get()
            ->pluck('station_id')
            ->toArray();
    }

    /**
     * 获取能去的考站
     * @access public
     * @param $stations
     * @version 3.6
     * @author JiangZhiheng <JiangZhiheng@163.com>
     * @time 2016-05-02
     * @copyright 2013-2016 MIS 163.com Inc. All Rights Reserved
     */
    public function accessStation($stations, $screenId, $roomId)
    {
        $arrayStations = $stations->toArray();
        return collect(array_diff($arrayStations, $this->usedStation($screenId, $roomId)));
    }
}