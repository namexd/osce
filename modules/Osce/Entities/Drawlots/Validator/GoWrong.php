<?php
/**
 * Created by PhpStorm.
 * User: j5110
 * Date: 2016/5/2
 * Time: 16:56
 */

namespace Modules\Osce\Entities\Drawlots\Validator;


use Modules\Osce\Entities\Drawlots\DrawValidatorInterface;
use Modules\Osce\Entities\ExamQueue;
use Modules\Osce\Entities\Room;
use Modules\Osce\Repositories\Common;

class GoWrong implements DrawValidatorInterface
{
    public function validate($studentId, $screenId, $roomId, $examId)
    {
        $temp = ExamQueue::whereStatus(0)
            ->whereStudentId($studentId)
            ->whereExamScreeningId($screenId)
            ->whereBlock(0)
            ->orderBy('next_num', 'asc')
            ->orderBy('begin_dt', 'asc')
            ->first();

        if ($temp->room_id != $roomId) {
            $room = Room::find($temp->room_id);
            Common::valueIsNull($room, -11, '数据错误，请重试');

            throw new \Exception('您走错考场，请到' . $room->name . '考场进行考试');
        }

        return true;
    }
}