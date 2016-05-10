<?php
/**
 * Created by PhpStorm.
 * User: j5110
 * Date: 2016/5/3
 * Time: 14:12
 */

namespace Modules\Osce\Entities\PadLogin;


use Modules\Osce\Entities\ExamOrder;
use Modules\Osce\Entities\ExamDraft;

class PadLogin
{
    /**
     * 获取安排过的考试的id和name
     * @access public
     * @param $date
     * @return mixed
     * @version 3.6
     * @author JiangZhiheng <JiangZhiheng@misrobot.com>
     * @time 2016-05-02
     * @copyright 2013-2016 MIS misrobot.com Inc. All Rights Reserved
     */
    public function screenBegin($beginDt, $endDt)
    {
        return ExamOrder::join('exam_screening', 'exam_screening.id', '=', 'exam_order.exam_screening_id')
            ->join('exam', 'exam.id', '=', 'exam_screening.exam_id')
            ->select(
//			'exam_screening.id as screen_id',
                'exam_screening.end_dt as end_dt',
                'exam_screening.begin_dt as begin_dt',
                'exam.name as name',
                'exam.id as exam_id'
            )
            ->where('exam_screening.begin_dt', '>=', $beginDt)
            ->where('exam_screening.begin_dt', '<=', $endDt)
            ->orWhere(function ($query) use ($beginDt, $endDt) {
                $query->where('exam_screening.end_dt', '<=', $endDt)
                    ->where('exam_screening.end_dt', '>=', $beginDt);
            })->orWhere(function ($query) use($beginDt, $endDt) {
                $query->where('exam_screening.begin_dt', '<=', $beginDt)
                    ->where('exam_screening.end_dt', '>=', $endDt);
            })
            ->orderBy('begin_dt', 'asc')
            ->groupBy('exam_id')
            //->distinct()
            ->get();
    }

    /**
     * 获取考试对应的考站id集合
     * @access public
     * @param $examId
     * @version
     * @author JiangZhiheng <JiangZhiheng@misrobot.com>
     * @time 2016-05-02
     * @copyright 2013-2016 MIS misrobot.com Inc. All Rights Reserved
     */
    public function roomList($examId)
    {
        return ExamDraft::with('room')
            ->join('exam_draft_flow', 'exam_draft_flow.id', '=', 'exam_draft.exam_draft_flow_id')
            ->where('exam_id', $examId)
            ->select(
                'exam_draft.room_id as room_id'
            )
            ->distinct()
            ->get();
    }

    /**
     * 为集合做唯一处理
     * @access public
     * @param $collection
     * @param string $field
     * @return mixed
     * @version
     * @author JiangZhiheng <JiangZhiheng@misrobot.com>
     * @time 2016-05-08
     * @copyright 2013-2016 MIS misrobot.com Inc. All Rights Reserved
     */
    public function wipeEqual($collection, $field = 'exam_id')
    {
        $array = [];

        foreach ($collection as $item) {
            if (!is_null($item)) {
                $array[$item->$field] = $item;
            }
        }

        return collect($array)->values();
    }
}