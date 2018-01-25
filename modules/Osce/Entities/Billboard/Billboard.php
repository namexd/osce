<?php
/**
 * Created by PhpStorm.
 * User: j5110
 * Date: 2016/5/4
 * Time: 21:38
 */

namespace Modules\Osce\Entities\Billboard;


use Modules\Osce\Entities\ExamDraft;
use Modules\Osce\Entities\ExamPlan;
use Modules\Osce\Entities\ExamQueue;
use Modules\Osce\Entities\ExamScreening;

class Billboard
{
    /**
     * 获取数据
     * @access public
     * @param $examId
     * @param $teacherId
     * @return mixed
     * @version
     * @author ZouYuChao <ZouYuChao@sulida.com>
     * @time 2016-05-04
     * @copyright 2013-2017 sulida.com Inc. All Rights Reserved
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
                'cases.description as case_description',
                'exam_draft.room_id as room_id'
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
    public function get2Queue($examId, $roomId)
    {
        $examscreening = ExamScreening::where('exam_id', $examId)->where('status', 1)->first();
        $exam_screening_id = $examscreening->id;
        $list2 = ExamPlan::leftjoin('student', 'exam_plan.student_id', '=', 'student.id')
            ->select('exam_plan.id as planid', 'exam_plan.begin_dt','student.id as stuid', 'student.avator', 'student.idcard', 'student.code',  'student.exam_sequence','exam_plan.student_id as pstuid', 'student.name as stuname')
            ->where('exam_plan.exam_id', $examId)
            ->where('exam_plan.exam_screening_id', $exam_screening_id)
            ->where('exam_plan.room_id', $roomId)
            ->where('exam_plan.status','<',2)
            ->orderBy('exam_plan.begin_dt', 'asc')
            ->first();
        return $list2;
    }

    /**
     * 获取下一个考场
     * @access public
     * @param $examId
     * @param $teacherId
     * @return mixed
     * @version
     * @author ZouYuChao <ZouYuChao@sulida.com>
     * @time 2016-05-04
     * @copyright 2013-2017 sulida.com Inc. All Rights Reserved
     */
    public function getNextRoom($examId, $studentId)
    {
        //通过得到的exam_id查exam_screeing
        $examscreening = ExamScreening::where('exam_id',$examId)->where('status',1)->first();
        $exam_screening_id = $examscreening->id;

        $data = ExamPlan::leftjoin('student', 'exam_plan.student_id', '=', 'student.id')
            ->leftjoin('room','exam_plan.room_id','=','room.id')
            ->select('exam_plan.id as planid','room.id as room_id','room.name as room_name','exam_plan.student_id as student_id','student.name as student_name')
            ->where('exam_plan.exam_id',$examId)
            ->where('exam_plan.exam_screening_id',$exam_screening_id)
            ->where('exam_plan.student_id',$studentId)
            ->where('exam_plan.status','<',2)
            ->orderBy('exam_plan.begin_dt','asc')
            ->get();
        $examarr = $data->toarray();
        if(array_key_exists(1, $examarr)){
            $NextQueue = $data[1];
        }else{
            $NextQueue = '';
        }

        return $NextQueue;
    }
}