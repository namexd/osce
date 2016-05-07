<?php
/**
 * Created by PhpStorm.
 * User: j5110
 * Date: 2016/5/3
 * Time: 18:35
 */

namespace Modules\Osce\Entities\Drawlots\Validator;


use Modules\Osce\Entities\Drawlots\DrawValidatorInterface;
use Modules\Osce\Entities\Drawlots\Station;
use Modules\Osce\Entities\ExamQueue;

class InExaminee implements DrawValidatorInterface
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
        \Log::debug('stations');
        $examinee = ExamQueue::examineeByRoomId($roomId, $examId, $stations, $screenId);
        \Log::debug('studentIn', [$examinee->pluck('student_id'), $studentId]);
        if ($examinee->pluck('student_id')->search($studentId) === false) {
            throw new \Exception('当前考生目前不应该抽签', -30);
        }


    }
}