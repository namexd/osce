<?php
/**
 * Created by PhpStorm.
 * User: j5110
 * Date: 2016/5/2
 * Time: 17:10
 */

namespace Modules\Osce\Entities\Drawlots\Validator;


use Modules\Osce\Entities\Drawlots\DrawValidatorInterface;
use Modules\Osce\Entities\Drawlots\Station;
use Modules\Osce\Entities\ExamQueue;
use Modules\Osce\Entities\ExamStationStatus;

class NotEndPrepare implements DrawValidatorInterface
{
    private $station = null;

    public function __construct(Station $station)
    {
        $this->station = $station;
    }

    public function validate($studentId, $screenId, $roomId, $examId)
    {
        // TODO: Implement validate() method.

        $stations = $this->station->site($examId, $roomId, $screenId);
        $stationIds = $stations->pluck('station_id')->toArray();

        /*
        $ready = ExamStationStatus::whereIn('station_id', $stationIds)
            ->whereExamScreeningId($screenId)
            ->whereStatus(1)
            ->count();

        if ($ready == count($stationIds)) {
            if (ExamQueue::where('status', 2)
                ->where('exam_screening_id', $screenId)
                ->where('room_id', $roomId)
                ->count() > 0) {
                throw new \Exception('上场考试未完成，请稍后签到', -13);
            }
        } elseif ($ready < count($stationIds)) {
            throw new \Exception('上场考试未完成，请稍后签到', -14);
        }
        */

        //直接去exam_station_status表中寻找同场次有没有状态值为4的
        if (ExamStationStatus::whereIn('station_id', $stationIds)
            ->whereExamScreeningId($screenId)
            ->whereStatus(4)
            ->first()
        ) {
            throw new \Exception('上场考试未完成，请稍后签到', -13);
        }

        return true;
    }
}