<?php
/**
 * Created by PhpStorm.
 * User: j5110
 * Date: 2016/4/8
 * Time: 9:56
 */

namespace Modules\Osce\Entities\SmartArrange;

use Modules\Osce\Entities\SmartArrange\Traits\CheckTraits;
use Modules\Osce\Entities\SmartArrange\Traits\SQLTraits;
use Modules\Osce\Entities\SmartArrange\Student\StudentFromDatabase;
use Modules\Osce\Entities\ExamPlanRecord;

class SmartArrangeRepository
{
    use CheckTraits, SQLTraits;

    private $smartArrange;

    function __construct($smartArrange)
    {
        $this->smartArrange = $smartArrange;
    }

    /**
     * 开始排考
     * @param $exam
     * @param $this ->smartArrange
     * @throws \Exception
     * @author Jiangzhiheng
     * @time 2016-04-11 16:17
     */
    function plan($exam)
    {
        try {
            //将考试初始化进去
            $this->smartArrange->exam = $exam;

            //初始化学生
            $this->smartArrange->setStudents(new StudentFromDatabase());


            /*
             * 做排考的前期准备
             * 检查各项数据是否存在
             */
            $this->checkStudentIsZero($this->smartArrange->getStudents()); //检查当前考试是否有学生

            $this->checkDataBase($this->smartArrange->exam); //检查临时表中是否有数据，如果有，就删除之

            /*
             * 将阶段遍历，在每个阶段中进行排考
             */
            $gradations = $this->getGradations($exam);
            foreach ($gradations as $key => $gradation) {
                //$key就是order的值
                $screens = $this->getScreenByOrder($key, $exam);
                //循环遍历$screen，对每个时段进行排考
                foreach ($screens as $screen) {
                    //将考试实体初始化进去
                    $this->smartArrange->setEntity($exam, $screen);
                    $this->checkEntityIsZero($this->smartArrange->getEntity()); //检查当前考试是否安排了考试实体
                    $screen = $this->setFlowsnumToScreen($exam, $screen); //将该场次有多少流程写入场次对象
                    
                    $this->smartArrange->screenPlan($screen);

                    //判断是否需要下场排考
                    $examPlanNull = ExamPlanRecord::whereNull('end_dt')->where('exam_id',
                        $exam->id)->first();  //通过查询数据表中是否有没有写入end_dt的数据

                    if (count($this->smartArrange->getStudents()) == 0 && count($this->smartArrange->getWaitStudents()) == 0 && is_null($examPlanNull)) {
                        return $this->output($exam);
                    }
                }
                throw new \Exception('人数太多，所设时间无法完成考试', -99);
            }
            throw new \Exception('人数太多，所设阶段无法完成考试', -98);
        } catch (\Exception $ex) {
            if (ExamPlanRecord::where('exam_id', $exam->id)->count()) {
                if (!ExamPlanRecord::where('exam_id', $exam->id)->delete()) {
                    throw new \Exception('系统异常！', -500);
                }
            }
            throw $ex;
        }
    }

    /**
     * 将数据输出
     * @param $exam
     * @return array
     * @author Jiangzhiheng
     * @time 2016-04-11 16:10
     */
    function output($exam)
    {
        $result = ExamPlanRecord::where('exam_id', $exam->id)
            ->get();

        $arrays = [];
        foreach ($result as $record) {
            //$arrays = $screen->groupBy('station_id');
            $station_id = $record->station_id;
            //$station        =   $record->station;
            $screeningId = $record->exam_screening_id;
            if ($exam->sequence_mode == 1) //考场模式
            {
                $arrays[$screeningId][$record->room_id][strtotime($record->begin_dt)][] = $record;
            } else { //考站模式
                $arrays[$screeningId][$record->room_id . '-' . $record->station_id][strtotime($record->begin_dt)][] = $record;
            }
        }

        $timeData = [];
        foreach ($arrays as $screeningId => $screening) {
            foreach ($screening as $entityId => $timeList) {
                foreach ($timeList as $batch => $recordList) {
                    foreach ($recordList as $record) {
                        if ($exam->sequence_mode == 1) { //考场模式
                            $name = $record->room->name;
                        } elseif ($exam->sequence_mode == 2) { //考站模式
                            $name = $record->room->name . '-' . $record->station->name;
                        }

                        $student = $record->student;

                        $timeData[$screeningId][$entityId]['name'] = $name;
                        $timeData[$screeningId][$entityId]['child'][$batch]['start'] = strtotime($record->begin_dt);
                        $timeData[$screeningId][$entityId]['child'][$batch]['end'] = strtotime($record->end_dt);
                        $timeData[$screeningId][$entityId]['child'][$batch]['screening'] = $screeningId;
                        $timeData[$screeningId][$entityId]['child'][$batch]['items'][$student->id] = $student;

                    }
                }
            }
        }
        return $timeData;
    }
}