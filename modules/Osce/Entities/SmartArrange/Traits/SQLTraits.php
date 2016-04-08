<?php
/**
 * Created by PhpStorm.
 * User: j5110
 * Date: 2016/4/7
 * Time: 16:39
 */

namespace Modules\Osce\Entities\SmartArrange\Traits;

use Modules\Osce\Entities\ExamGradation;
use Modules\Osce\Entities\ExamPlanRecord;
use Modules\Osce\Entities\ExamScreening;
use Modules\Osce\Entities\ExamDraftFlow;

trait SQLTraits
{
    /**
     * 根据考试id找到对应的阶段
     * @param $exam
     * @return mixed
     * @author Jiangzhiheng
     * @time
     */
    function getGradations($exam) {
        return ExamGradation::where('exam_id', $exam->id)
            ->get()
            ->keyBy('order');
    }

    /**
     * 根据order找到screen
     * @param $key
     * @param $exam
     * @return mixed
     * @author Jiangzhiheng
     * @time 2016-04-07 16:36
     */
    function getScreenByOrder($key, $exam)
    {
        return ExamScreening::where('gradation_order', $key)
            ->where('exam_id', $exam->id)
            ->get();
    }

    /**
     * 随机模式下的看是否有人考试
     * @param $screen
     * @return mixed
     * @author Jiangzhiheng
     * @time 2016-04-07 16:54
     */
    function randomBeginStudent($screen)
    {
        return ExamPlanRecord::where('exam_screening_id', $screen->id)
            ->whereNotNull('end_dt')
            ->groupBy('student_id')
            ->get();
    }

    function waitingStudentSql($screen)
    {
        return ExamPlanRecord::where('exam_screening_id', $screen->id)
            ->groupBy('student_id')
            ->select(\DB::raw('(count(end_dt) = count(begin_dt)) as num,student_id,count(`station_id`) as flows_num'))
            ->where('exam_screening_id', $screen->id)
            ->havingRaw('num > ?', [0])
            ->havingRaw('flows_num < ?', [$screen->flowNum])
            ->get();
    }

    /**
     * 将流程写进screen
     * @param $screen
     * @return mixed
     * @author Jiangzhiheng
     * @time 2016-04-07 17:30
     */
    function setFlowsnumToScreen($screen)
    {
        $num = ExamDraftFlow::where('exam_screening_id', $screen->id)
            ->count();
        $screen->flowNum = $num;
        return $screen;
    }

    /**
     * 获取当前考试的考试状态
     * @param $entity
     * @param $screen
     * @return mixed
     * @throws \Exception
     * @author Jiangzhiheng
     * @time 2016-04-08 11:30
     */
    function examPlanRecordIsOpenDoor($entity, $screen)
    {
        if ($entity->type == 2) {
            return ExamPlanRecord::where('station_id', '=', $entity->id)
                ->where('exam_screening_id', '=', $screen->id)
                ->whereNull('end_dt')
                ->get();
        } elseif ($entity->type == 1) {
            return ExamPlanRecord::where('room_id', '=', $entity->id)
                ->where('exam_screening_id', '=', $screen->id)
                ->whereNull('end_dt')
                ->get();
        } else {
            throw new \Exception('没有选定的考试模式！', -2);
        }
    }

    /**
     * 获取当前场次下的流程个数
     * @param $exam
     * @param $screen
     * @return mixed
     * @author Jiangzhiheng
     * @time 2016-04-08 14:40
     */
    function flowNum($exam, $screen)
    {
        return count(ExamDraftFlow::where('exam_id', $exam->id)
            ->where('exam_screening_id', $screen->id)
            ->get());
    }

    /**
     * 获取未走完流程的考生
     * @param $exam
     * @param $flowsNum
     * @return mixed
     * @author Jiangzhiheng
     * @time 2016-04-08 14:49
     */
    function testingStudentList($exam, $flowsNum)
    {
        return ExamPlanRecord::where('exam_id', $exam->id)
            ->whereNotNull('end_dt')
            ->groupBy('student_id')
            ->selecet(
                \DB::raw(
                    implode(',',
                        [
                            'count(`id`) as flowsNum',
                            'id',
                            'student_id',
                        ]
                    )
                ))->Having('flowsNum', '<', $flowsNum)
            ->get();
    }
}