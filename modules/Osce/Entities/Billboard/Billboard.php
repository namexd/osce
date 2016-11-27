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
     * @author JiangZhiheng <JiangZhiheng@163.com>
     * @time 2016-05-04
     * @copyright 2013-2016 MIS 163.com Inc. All Rights Reserved
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
                ->whereIn('status', [1, 2])
                ->orderBy('begin_dt', 'asc')
                ->first();
    }

    /**
     * 获取下一个考场
     * @access public
     * @param $examId
     * @param $teacherId
     * @return mixed
     * @version
     * @author JiangZhiheng <JiangZhiheng@163.com>
     * @time 2016-05-04
     * @copyright 2013-2016 MIS 163.com Inc. All Rights Reserved
     */
    public function getNextRoom($examId, $studentId, $room_id)
    {

        //拿到当前房间的时间
        $currentRoom = ExamQueue::where('exam_queue.student_id', '=', $studentId)
            ->where('exam_queue.exam_id', '=', $examId)
            ->where('exam_queue.room_id', $room_id)
            ->where('exam_queue.status', '=', 0)
            ->first();
        if(is_null($currentRoom)){
            throw new \Exception('获取当前考场信息失败',-1001);
        }
        $roomTime = $currentRoom->begin_dt;
        \Log::debug('电子门牌当前考场时间',[$roomTime]);
        //时间要大于当前考场的
        $NextQueue = ExamQueue::leftjoin('room', 'room.id', '=', 'exam_queue.room_id')
            ->leftjoin('student', 'student.id', '=', 'exam_queue.student_id')
            ->where('exam_queue.student_id', '=', $studentId)
            ->where('exam_queue.exam_id', '=', $examId)
            ->whereNotIn('exam_queue.room_id', [$room_id])
            ->where('exam_queue.status', '=', 0)
            ->whereRaw("UNIX_TIMESTAMP(begin_dt) > UNIX_TIMESTAMP('$roomTime')")
            ->select([
                'room.id as room_id',
                'room.name as room_name',
                'student.id as student_id',
                'student.name as student_name',
            ])  
            ->orderBy('exam_queue.begin_dt', 'asc')
            ->first();
        return $NextQueue;
    }
}