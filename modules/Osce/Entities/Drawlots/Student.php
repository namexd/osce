<?php
/**
 * Created by PhpStorm.
 * User: j5110
 * Date: 2016/5/2
 * Time: 10:03
 */

namespace Modules\Osce\Entities\Drawlots;


use Modules\Osce\Entities\ExamQueue;
use Modules\Osce\Entities\ExamScreeningStudent;


class Student implements StudentInterface
{
    public function getStudent($examScreeningId, $nfc)
    {
        try {
            \Log::debug('nfc', [$nfc]);
            return ExamScreeningStudent::watch()
                ->where('watch.status', 1)
                ->where('watch.code', $nfc)
                ->where('exam_screening_id', $examScreeningId)
                ->where('exam_screening_student.is_end', 0)
                ->select(
                    'exam_screening_student.student_id as student_id',
                    'exam_screening_student.exam_screening_id as exam_screening_id',
                    'exam_screening_student.watch_id as watch_id'
                )
                ->first();
        } catch (\Exception $ex) {
            throw $ex;
        }
    }
}