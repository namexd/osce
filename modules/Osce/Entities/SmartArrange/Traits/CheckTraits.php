<?php
/**
 * Created by PhpStorm.
 * User: j5110
 * Date: 2016/4/7
 * Time: 14:00
 */

namespace Modules\Osce\Entities\SmartArrange\Traits;

use Modules\Osce\Entities\ExamPlanRecord;

trait CheckTraits
{
    function checkStudentIsZero($students)
    {
        try {
            if ($students->count() == 0) {
                throw new \Exception('当前考试没有安排学生！', -1);
            }
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    function checkEntityIsZero($entities)
    {
        try {
            if ($entities->count() == 0) {
                throw new \Exception('当前考试没有安排考场或考站！', -2);
            }
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    function checkDataBase($exam) {
        try {
            if (ExamPlanRecord::where('exam_id', $exam->id)->count()) {
                if (!ExamPlanRecord::where('exam_id', $exam->id)->delete()) {
                    throw new \Exception('清空所有数据失败！');
                };
            }
        } catch (\Exception $ex) {
            throw $ex;
        }
    }
}