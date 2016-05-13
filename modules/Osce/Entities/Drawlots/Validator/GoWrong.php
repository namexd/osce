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
            ->whereBlocking(1)
            ->orderBy('begin_dt', 'asc')
            ->first();
        \Log::debug('GoWrong', [$studentId, $screenId, $roomId, $examId]);
        \Log::debug('room', [$temp]);
        if (is_null($temp)) {
            throw new \Exception('当前学生信息错误');
        }
        if ($temp->room_id != $roomId) {
            $room = Room::find($temp->room_id);
            Common::valueIsNull($room, -11, '数据错误，请重试');

            throw new \Exception('请到' . $room->name . '考场进行考试');
        }

        return true;
    }
}