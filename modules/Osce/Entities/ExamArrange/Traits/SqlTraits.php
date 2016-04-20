<?php
/**
 * Created by PhpStorm.
 * User: j5110
 * Date: 2016/4/14
 * Time: 17:42
 */

namespace Modules\Osce\Entities\ExamArrange\Traits;


use Modules\Osce\Entities\ExamDraftFlow;
use Modules\Osce\Entities\ExamOrder;
use Modules\Osce\Entities\ExamPlan;
use Modules\Osce\Entities\ExamPlanRecord;

trait SqlTraits
{
    public function checkExamArrange($examId)
    {
        return ExamDraftFlow::join('exam_draft', 'exam_draft.exam_draft_flow_id', '=', 'exam_draft_flow.id')
            ->where('exam_draft_flow.exam_id', $examId)
            ->select(
                'exam_draft_flow.order as exam_draft_flow_order',
                'exam_draft_flow.exam_screening_id as exam_screening_id',
                'exam_draft_flow.exam_gradation_id as exam_gradation_id',
                'exam_draft.station_id as station_id',
                'exam_draft.room_id as room_id',
                'exam_draft.exam_draft_flow_id as exam_draft_flow_id',
                'exam_draft.subject_id as subject_id',
                'exam_draft.id as exam_draft_id'
            )
            ->get();
    }

    /**
     * 清空plan表
     * @param $examId
     * @return bool
     * @throws \Exception
     * @author Jiangzhiheng
     * @time 2016-04-15 11:14
     */
    public function emptyingPlan($examId)
    {
        if (ExamPlan::where('exam_id', $examId)->first()) {
            if (!ExamPlan::where('exam_id', $examId)->delete()) {
                throw new \Exception('清空排考表失败！');
            };
        }

        return true;
    }

    /**
     * 清空plan_record表
     * @param $examId
     * @return bool
     * @throws \Exception
     * @author Jiangzhiheng
     * @time 2016-04-15 11:17
     */
    public function emptyingPlanRecord($examId)
    {
        if (ExamPlanRecord::where('exam_id', $examId)->first()) {
            if (!ExamPlanRecord::where('exam_id', $examId)->delete()) {
                throw new \Exception('清空排考数据表失败！');
            };
        }


        return true;
    }

    /**
     * 清空order表
     * @param $examId
     * @return bool
     * @throws \Exception
     * @author Jiangzhiheng
     * @time 2016-04-15 11:20
     */
    public function emptyingOrder($examId)
    {
        if (ExamOrder::where('exam_id', $examId)->first()) {
            if (!ExamOrder::where('exam_id', $examId)->delete()) {
                throw new \Exception('清空腕表顺序表失败！');
            };
        }

        return true;
    }
}