<?php
/**
 * Created by PhpStorm.
 * User: j5110
 * Date: 2016/4/7
 * Time: 16:39
 */

namespace Modules\Osce\Entities\SmartArrange\Traits;

use Illuminate\Database\Eloquent\Collection as DBCollection;
use Illuminate\Support\Collection as Collection;
use Modules\Osce\Entities\ExamDraft;
use Modules\Osce\Entities\ExamGradation;
use Modules\Osce\Entities\ExamPlanRecord;
use Modules\Osce\Entities\ExamScreening;
use Modules\Osce\Entities\ExamDraftFlow;
use Modules\Osce\Entities\Station;
use Modules\Osce\Entities\ExamPaper;
use Modules\Osce\Entities\Subject;

trait SQLTraits
{
    /**
     * 根据考试id找到对应的阶段
     * @param $exam
     * @return mixed
     * @author Jiangzhiheng
     * @time
     */
    function getGradations($exam)
    {
        return ExamGradation::join('exam_draft_flow', 'exam_draft_flow.exam_gradation_id', '=', 'exam_gradation.id')
            ->where('exam_draft_flow.exam_id', $exam->id)
            ->select(
                'exam_gradation.order as gradation_order',
                'exam_gradation.id as exam_gradation_id',
                'exam_gradation.sequence_cate as sequence_cate'
            )
            ->get()
            ->keyBy('gradation_order');
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
        return ExamPlanRecord::with('student')
            ->select(\DB::raw('(count(end_dt) = count(begin_dt)) as num,student_id,count(`station_id`) as flows_num'))
            ->where('exam_screening_id', $screen->id)
            ->havingRaw('num > ?', [0])
            ->havingRaw('flows_num < ?', [$screen->flowNum])
            ->groupBy('student_id')
            ->get();
    }

    function waitingPollStudentSql($screen, $entity)
    {
        $build = ExamPlanRecord::with('student')
            ->select(\DB::raw('(count(end_dt) = count(begin_dt)) as num,student_id,count(`station_id`) as flows_num'))
            ->where('exam_screening_id', $screen->id)
            ->havingRaw('num > ?', [0])
            ->havingRaw('flows_num < ?', [$screen->flowNum])
            ->groupBy('student_id');

        switch ($entity->type) {
            case 1:
                return $build->where('room_id', '<>', $entity->room_id)->get();
            case 2:
                return $build->where('station_id', '<>', $entity->station_id)->get();
            default:
                throw new \Exception('System Error');
        }

    }

    /**
     * 将流程写进screen
     * @param $screen
     * @return mixed
     * @author Jiangzhiheng
     * @time 2016-04-07 17:30
     */
    function setFlowsnumToScreen($exam, $screen)
    {
        $num = ExamScreening::join('exam_gradation', 'exam_gradation.order', '=', 'exam_screening.gradation_order')
            ->join('exam_draft_flow', 'exam_draft_flow.exam_gradation_id', '=', 'exam_gradation.id')
            ->join('exam_draft', 'exam_draft.exam_draft_flow_id', '=', 'exam_draft_flow.id')
            ->where('exam_gradation.exam_id', '=', $exam->id)
            ->where('exam_screening.id', '=', $screen->id)
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
            return ExamPlanRecord::where('station_id', '=', $entity->station_id)
                ->where('exam_screening_id', '=', $screen->id)
                ->whereNull('end_dt')
                ->get();
        } elseif ($entity->type == 1) {
            return ExamPlanRecord::where('room_id', '=', $entity->room_id)
                ->where('exam_screening_id', '=', $screen->id)
                ->whereNull('end_dt')
                ->get();
        } else {
            throw new \Exception('没有选定的考试模式！', -2);
        }
    }

    /**
     * 获取当前场次下的流程个数
     * @param Collection $entities
     * @return mixed
     * @author Jiangzhiheng
     * @time 2016-04-08 14:40
     */
    function flowNum(Collection $entities)
    {
        return $entities->groupBy('serialnumber')->count();
    }

    /**
     * 获取未走完流程的考生
     * @param $exam
     * @param $flowsNum
     * @return mixed
     * @author Jiangzhiheng
     * @time 2016-04-08 14:49
     */
    function testingStudentList($exam, $screen, $flowsNum)
    {
        return ExamPlanRecord::where('exam_id', $exam->id)
            ->where('exam_screening_id', $screen->id)
            ->whereNotNull('end_dt')
            ->groupBy('student_id')
            ->select(
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

//        return ExamPlanRecord::where('exam_id', $exam->id)
//            ->where('exam_screening_id', $screen->id)
//            ->whereNotNull('end_dt')
//            ->select(
//                \DB::raw(
//                    implode(',',
//                        [
//                            'count(`serialnumber`) as flowsNum',
//                            'id',
//                            'student_id'
//                        ]
//                    )
//                )
//            )
//            ->having('')
    }

    /**
     * 获取当前场次排考已经写完的条数
     * @param $screenId
     * @return mixed
     * @author Jiangzhiheng
     * @time 2016-04-15 15:44
     */
    public function overStudentCount($screenId)
    {
        return ExamPlanRecord::where('exam_screening_id', $screenId)
            ->whereNotNull('end_dt')
            ->count();
    }

    /**
     * TODO 这个方法现在无法实现，等后续版本
     * 返回考站实体
     * @param $screen
     * @return mixed
     * @author Jiangzhiheng
     * @time 2016-04-08 17:07
     */
    function getStationFuture($screen)
    {
        $stations = ExamDraftFlow::join('exam_draft', 'exam_draft.exam_draft_flow_id', '=', 'exam_draft_flow.id')
            ->join('station', 'station.id', '=', 'exam_draft.station_id')
            ->join('exam_gradation', 'exam_gradation.id', '=', 'exam_draft_flow.exam_gradation_id')
            ->join('subject', 'subject.id', '=', 'exam_draft.subject_id')
            ->where('exam_draft_flow.exam_screening_id', $screen->id)
            ->select(
                'station.name as name',
                'subject.mins as mins',
                'exam_draft.station_id as station_id',
                'exam_draft.room_id as room_id',
                'exam_draft_flow.order as serialnumber',
                'exam_draft_flow.exam_screening_id as exam_screening_id',
                'exam_gradation.order as gradation_order'
            )->get();

        foreach ($stations as &$station) {
            $station->type = 2;
        }

        return $stations;
    }

    /**
     * 现行的获取考站实体的方法
     * @param $exam
     * @param $screen
     * @return mixed
     * @author Jiangzhiheng
     * @time 2016-04-12 14：37
     */
    function getStation($exam, $screen)
    {
        $stations = ExamScreening::join('exam_gradation', 'exam_gradation.order', '=', 'exam_screening.gradation_order')
            ->join('exam_draft_flow', 'exam_draft_flow.exam_gradation_id', '=', 'exam_gradation.id')
            ->join('exam_draft', 'exam_draft.exam_draft_flow_id', '=', 'exam_draft_flow.id')
            ->join('station', 'station.id', '=', 'exam_draft.station_id')
            ->leftJoin('subject', 'subject.id', '=', 'exam_draft.subject_id')
            ->leftJoin('exam_paper_exam_station', 'exam_paper_exam_station.station_id', '=', 'exam_draft.station_id')
            ->leftJoin('exam_paper', 'exam_paper.id', '=', 'exam_paper_exam_station.exam_paper_id')
            ->where('exam_screening.id', $screen->id)
            ->where('exam_gradation.exam_id', $exam->id)
            ->select(
                'station.name as name',
                'station.type as station_type',
                'subject.mins as mins',
                'exam_paper.length as length',
                'exam_draft.station_id as station_id',
                'exam_draft.room_id as room_id',
                'exam_screening.id as exam_screening_id',
                'exam_draft_flow.optional',
                'exam_draft_flow.order as order',
                'exam_draft_flow.exam_id as exam_id',
                'exam_gradation.order as gradation_order'
            )->get();

        foreach ($stations as &$station) {
            $station->type = 2;
        }

        //为集合加上序号
//        $stations = $this->setSerialnumber($stations);

        return $stations;
    }

    /**
     * TODO 这个方法现在无法实现，等后续版本
     * 返回考场实体
     * @param $screen
     * @return mixed
     * @author Jiangzhiheng
     * @time 2016-04-08 17:07
     */
    function getRoomFuture($screen)
    {
        $rooms = ExamDraftFlow::join('exam_draft', 'exam_draft.exam_draft_flow_id', '=', 'exam_draft_flow.id')
            ->join('room', 'room.id', '=', 'exam_draft.room_id')
            ->join('exam_gradation', 'exam_gradation.id', '=', 'exam_draft_flow.exam_gradation_id')
            ->join('subject', 'subject.id', '=', 'exam_draft.subject_id')
            ->where('exam_screening_id', $screen->id)
            ->select(
                'room.name as name',
                'subject.mins as mins',
                'exam_draft.room_id as room_id',
                'exam_screening.id as exam_screening_id',
                'exam_gradation.order as gradation_order',
                'exam_draft_flow.order as order'
            )->distinct()
            ->get();

        foreach ($rooms as &$room) {
            $room->type = 1;
        }

        return $rooms;
    }

    /**
     * 现行的获取考场实体的方法
     * @param $exam
     * @param $screen
     * @return mixed
     * @author Jiangzhiheng
     * @time 2016-04-12 14:36
     */
    function getRoom($exam, $screen)
    {
        $rooms = ExamScreening::join('exam_gradation', 'exam_gradation.order', '=', 'exam_screening.gradation_order')
            ->join('exam_draft_flow', 'exam_draft_flow.exam_gradation_id', '=', 'exam_gradation.id')
            ->join('exam_draft', 'exam_draft.exam_draft_flow_id', '=', 'exam_draft_flow.id')
            ->join('room', 'room.id', '=', 'exam_draft.room_id')
            ->leftJoin('subject', 'subject.id', '=', 'exam_draft.subject_id')
            ->leftJoin('exam_paper_exam_station', 'exam_paper_exam_station.station_id', '=', 'exam_draft.station_id')
//            ->leftJoin('exam_paper', 'exam_paper.id', '=', 'exam_paper_exam_station.exam_paper_id')
            ->leftJoin('exam_paper', function ($join) use ($exam){
                $join->on('exam_paper.id', '=', 'exam_paper_exam_station.exam_paper_id')
                    ->where('exam_paper_exam_station.exam_id', '=', $exam->id);
            })
            ->where('exam_screening.id', $screen->id)
            ->where('exam_gradation.exam_id', $exam->id)
            ->select(
                'room.name as name',
                'subject.mins as mins',
                'exam_paper.length as length',
                'exam_draft.room_id as room_id',
                'exam_draft_flow.exam_screening_id as exam_screening_id',
                'exam_gradation.order as gradation_order',
                'exam_draft_flow.order as order',
                'exam_draft_flow.optional'
            )
            ->get();

        foreach ($rooms as &$room) {
            $room->type = 1;
        }

        //为集合加上序号
//        $rooms = $this->setSerialnumber($rooms);

        return $rooms;
    }


    /**
     * 获取当前考场所对应的考站
     * @param $screen
     * @param $roomId
     * @return mixed
     * @author Jiangzhiheng
     * @time 2016-04-08 17:53
     */
    function roomStation($exam, $screen, $roomId)
    {
        return ExamDraft::join('exam_draft_flow', 'exam_draft_flow.id', '=', 'exam_draft.exam_draft_flow_id')
            ->join('exam_gradation', 'exam_gradation_id', '=', 'exam_gradation.id')
            ->join('exam_screening', 'exam_screening.gradation_order', '=', 'exam_gradation.order')
            ->where('exam_screening.id', $screen->id)
            ->where('exam_gradation.exam_id', $exam->id)
            ->where('exam_draft.room_id', $roomId)
            ->select(
                'exam_draft.id as exam_draft_id',
                'exam_draft.station_id as station_id',
                'exam_draft.room_id as room_id'
            )
            ->get();
    }

    /**
     * 返回考场对应的考站的考试时间数组
     * @param $roomStation
     * @return array
     * @author Jiangzhiheng
     * @time 2016-04-08 18:01
     */
    function stationMins($roomStation)
    {
        $stationIds = $roomStation->pluck('station_id');
        return Station::whereIn('station_id', $stationIds)->lists('mins')->toArray();
    }

    /**
     * 获取学生考过的序号
     * @param $testingStudent
     * @return mixed
     * @author Jiangzhiheng
     * @time 2016-04-08 18:55
     */
    function getStudentSerialnumber($exam, $screen, $testingStudent)
    {
        return ExamPlanRecord::where('student_id', $testingStudent->id)
            ->where('gradation_order', $screen->gradation_order)
            ->where('exam_id', $exam->id)
            ->lists('serialnumber');
    }

    /**
     * 获取上一个流程的学生
     * @param $screen
     * @param $serialnumber
     * @return mixed
     * @author Jiangzhiheng
     * @time 2016-04-11 11:26
     */
    function prevSerial($screen, $serialnumber)
    {
        return ExamPlanRecord::where('exam_screening_id', '=', $screen->id)
            ->where('serialnumber', '=', $serialnumber - 1)
            ->whereNotNull('end_dt')
            ->orderBy('end_dt', 'asc')
            ->lists('student_id');
    }

    /**
     * 获取本流程的学生
     * @param $screen
     * @param $serialnumber
     * @return mixed
     * @author Jiangzhiheng
     * @time 2016-04-11 11:27
     */
    function thisSerial($screen, $serialnumber)
    {
        return ExamPlanRecord::where('exam_screening_id', $screen->id)
            ->whereNotNull('end_dt')
            ->where('serialnumber', '=', $serialnumber)
            ->orderBy('end_dt', 'asc')
            ->lists('student_id');
    }

    function thisNotSerial($screen, $serialnumber)
    {
        return ExamPlanRecord::where('exam_screening_id', $screen->id)
            ->whereNotNull('begin_dt')
            ->where('serialnumber', '=', $serialnumber)
            ->orderBy('begin_dt', 'asc')
            ->lists('student_id');
    }

    /**
     * 轮询模式下看是否有人考试
     * @param $entity
     * @param $screen
     * @param $sequenceMode
     * @return mixed
     * @throws \Exception
     * @author Jiangzhiheng
     * @time 2016-04-12 18:08
     */
    function pollBeginStudent($entity, $screen)
    {
        try {
            if ($entity->type == 1) {
                return ExamPlanRecord::where('exam_screening_id', $screen->id)
                    ->whereNotNull('end_dt')
                    ->where('room_id', '=', $entity->room_id)
                    ->groupBy('student_id')
                    ->get();
            } elseif ($entity->type == 2) {
                return ExamPlanRecord::where('exam_screening_id', $screen->id)
                    ->whereNotNull('end_dt')
                    ->where('station_id', '=', $entity->station_id)
                    ->groupBy('student_id')
                    ->get();
            } else {
                throw new \Exception('未定义的考试模式！', -15);
            }
        } catch (\Exception $ex) {
            throw $ex;
        }
    }



    /**
     * 获取理论考试的考试时间
     * @param $entity
     * @return mixed
     * @author Jiangzhiheng
     * @time 2016-04-18 18:15
     */
    function getTheoryMins($entity)
    {
        return ExamPaper::join('exam_paper_exam_station', 'exam_paper_exam_station.exam_paper_id', '=', 'exam_paper.id')
            ->where('exam_paper_exam_station.station_id', '=', '$entity.station_id')
            ->where('exam_id', $entity->exam_id)
            ->first();
    }

    /**
     * 返回当场考试的考站及screen
     * @author Jiangzhiheng
     * @time 2016-04-19 10:52
     */
    function getDraft($exam)
    {
        return ExamScreening::join('exam_gradation', 'exam_gradation.order', '=', 'exam_screening.gradation_order')
            ->join('exam_draft_flow', 'exam_draft_flow.exam_gradation_id', '=', 'exam_gradation.id')
            ->join('exam_draft', 'exam_draft.exam_draft_flow_id', '=', 'exam_draft_flow.id')
            ->where('exam_gradation.exam_id', '=', $exam->id)
            ->where('exam_screening.exam_id', '=', $exam->id)
            ->select(
                'exam_screening.id as exam_screening_id',
                'exam_screening.exam_id as exam_id',
                'exam_draft.station_id as station_id'
            )
            ->get();
    }

}