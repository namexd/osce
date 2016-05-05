<?php
/**
 * Created by PhpStorm.
 * User: j5110
 * Date: 2016/4/7
 * Time: 14:00
 */

namespace Modules\Osce\Entities\SmartArrange\Traits;

use Modules\Osce\Entities\ExamPlanRecord;
use Modules\Osce\Entities\ExamScreening;

trait CheckTraitsForHuaxi
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

    /**
     * screen的验证
     * @access public
     * @param $exam
     * @param $order
     * @throws \Exception
     * @version
     * @author JiangZhiheng <JiangZhiheng@misrobot.com>
     * @time 2016-05-02
     * @copyright 2013-2016 MIS misrobot.com Inc. All Rights Reserved
     */
    function checkUnnecessaryScreen($exam, $order)
    {
        try {
            $screen = ExamScreening::join('exam_gradation', 'exam_gradation.order', '=',
                'exam_screening.gradation_order')
                ->where('exam_screening.exam_id', $exam->id)
                ->where('exam_gradation.exam_id', $exam->id)
                ->where('exam_gradation.order', $order)
                ->select('exam_screening.id as id')
                ->get()
                ->groupBy('id');

            $temp = ExamPlanRecord::whereExamId($exam->id)
                ->whereGradationOrder($order)
                ->get()
                ->groupBy('exam_screening_id');


            if ($screen->count() != $temp->count()) {
                $key1 = $screen->keys()->toArray();
                $key2 = $temp->keys()->toArray();
                $diffKey = array_diff($key1, $key2);
                $message = '开始时间为';
                foreach ($diffKey as $item) {
                    $message .= ExamScreening::find($item)->begin_dt;
                    $message .= ',';
                }
                $message .= '没有人次考试';
                throw new \Exception($message);
            }

            return true;
        } catch (\Exception $ex) {
            throw $ex;
        }
    }
}