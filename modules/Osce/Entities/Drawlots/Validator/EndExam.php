<?php
/**
 * Created by PhpStorm.
 * User: j5110
 * Date: 2016/5/2
 * Time: 17:06
 */

namespace Modules\Osce\Entities\Drawlots\Validator;


use Modules\Osce\Entities\Drawlots\DrawValidatorInterface;
use Modules\Osce\Entities\ExamQueue;

class EndExam implements DrawValidatorInterface
{
    public function validate($studentId, $screenId, $roomId, $examId)
    {
        $temp = ExamQueue::whereStatus(3)
            ->whereStudentId($studentId)
            ->whereRoomId($roomId)
            ->whereExamScreeningId($screenId)
            ->orderBy('begin_dt', 'asc')
            ->first();

        if (!is_null($temp)) {
            throw new \Exception('您已完成考试，请交还腕表', -12);
        }

        return true;
    }
}