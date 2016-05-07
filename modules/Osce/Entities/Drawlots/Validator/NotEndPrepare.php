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
        $ready = ExamStationStatus::whereIn('station_id', $stationIds)
            ->whereExamScreeningId($screenId)
            ->whereStatus(1)
            ->count();

        if ($ready > 1) {
            if (ExamQueue::where('status', 2)
                ->where('exam_screening_id', $screenId)
                ->where('room_id', $roomId)
                ->count() == count($stationIds)) {
                throw new \Exception('上场考试未完成，请稍后签到', -13);
            }
        }

//        if (!$ready) {
//            //throw new \Exception('上场考试未完成，请稍后签到', -13);
//        }

        return true;
    }
}