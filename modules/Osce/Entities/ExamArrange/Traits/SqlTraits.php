<?php
/**
 * Created by PhpStorm.
 * User: j5110
 * Date: 2016/4/14
 * Time: 17:42
 */

namespace Modules\Osce\Entities\ExamArrange\Traits;


use Modules\Osce\Entities\ExamDraftFlow;

trait SqlTraits
{
    public function checkExamArrange($examId)
    {
        return ExamDraftFlow::join('exam_draft', 'exam_draft.exam_draft_flow_id', '=', 'exam_draft_flow.id')
            ->where('exam_id', $examId)
            ->select(
                'exam_draft_flow.order as exam_draft_flow_order',
                'exam_draft_flow.exam_screening_id as exam_screening_id',
                'exam_draft_flow.exam_gradation_id as exam_gradation_id',
                'exam_draft.station_id as station_id',
                'exam_draft.room_id as room_id',
                'exam_draft.exam_draft_flow_id as exam_draft_flow_id',
                'exam_draft.subject_id as subject_id'
            )
            ->get();
    }
}