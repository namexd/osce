<?php
/**
 * Created by PhpStorm.
 * User: j5110
 * Date: 2016/5/4
 * Time: 21:38
 */

namespace Modules\Osce\Entities\Billboard;


use Modules\Osce\Entities\ExamDraft;
use Modules\Osce\Entities\ExamQueue;

class Billboard
{
    /**
     * 获取数据
     * @access public
     * @param $examId
     * @param $teacherId
     * @return mixed
     * @version
     * @author JiangZhiheng <JiangZhiheng@misrobot.com>
     * @time 2016-05-04
     * @copyright 2013-2016 MIS misrobot.com Inc. All Rights Reserved
     */
    public function getData($examId, $teacherId)
    {
        return ExamDraft::join('exam_draft_flow', 'exam_draft_flow.id', '=', 'exam_draft.exam_draft_flow_id')
            ->join('station_teacher', 'station_teacher.station_id', '=', 'exam_draft.station_id')
            ->join('station', 'station.id', '=', 'station_teacher.station_id')
            ->join('subject', 'subject.id', '=', 'exam_draft.subject_id')
            ->join('subject_cases', 'subject_cases.subject_id', '=', 'subject.id')
            ->join('cases', 'cases.id', '=', 'subject_cases.case_id')
            ->where('station_teacher.user_id', $teacherId)
            ->where('station_teacher.exam_id', $examId)
            ->where('exam_draft_flow.exam_id', $examId)
            ->select(
                'station_teacher.exam_id as exam_id',
                'station.id as station_id',
                'station.name as station_name',
                'subject.mins as mins',
                'subject.id as subject_id',
                'cases.name as case_name',
                'cases.description as case_description'
            )
            ->first();
    }

    public function getQueue($examId, $stationId)
    {
        return ExamQueue::whereExamId($examId)
            ->whereStationId($stationId)
            ->whereStatus(1)
            ->orderBy('begin_dt', 'asc')
            ->first();
    }
}