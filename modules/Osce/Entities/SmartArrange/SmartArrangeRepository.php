<?php
/**
 * Created by PhpStorm.
 * User: j5110
 * Date: 2016/4/8
 * Time: 9:56
 */

namespace Modules\Osce\Entities\SmartArrange;

use Modules\Osce\Entities\ExamPlan;
use Modules\Osce\Entities\SmartArrange\Student\StudentFromDB;
use Modules\Osce\Entities\SmartArrange\Traits\SundryTraits;
use Modules\Osce\Entities\ExamPlanRecord;

class SmartArrangeRepository extends AbstractSmartArrange
{
    use SundryTraits;

    private $_S_Count;

    public function __construct()
    {
        \App::bind('student', function () {
            return new StudentFromDB();
        });

        \App::bind('SmartArrange', function () {
            return new SmartArrange(\App::make('student'));
        });

        $this->model = \App::make('SmartArrange');
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
            $this->model->setExam($exam); //将考试实例注入
            $this->checkDataBase($exam); //检查临时表中是否有数据，如果有，就删除之
            /*
             * 将阶段遍历，在每个阶段中进行排考
             */
            $gradations = $this->getGradations($exam);
            if ($gradations->isEmpty()) {
                throw new \Exception('当前考试没有安排考场或考站!');
            }
            foreach ($gradations as $key => $gradation) {
                //获取当前考试的状态
                $type = is_null($gradation->sequence_cate) ? $exam->sequence_cate : $gradation->sequence_cate;

                //将排序模式注入
                $this->model->setCate(CateFactory::getCate($exam, $type));
                //初始化学生
                $this->_S_Count = $this->model->setStudents(new StudentFromDB());
                /*
                 * 做排考的前期准备
                 * 检查各项数据是否存在
                 */
                $this->checkStudentIsZero($this->model->getStudents()); //检查当前考试是否有学生
                //$key就是order的值
                $screens = $this->getScreenByOrder($key, $exam);
                //循环遍历$screen，对每个时段进行排考
                foreach ($screens as $screen) {
                    $studentsCount = ExamPlanRecord::where('exam_screening_id', $screen->id)
                        ->where('gradation_order', $key)
                        ->whereNotNull('end_dt')
                        ->groupBy('student_id')
                        ->count();
                    if ($this->_S_Count == $studentsCount) {
                        break;
                    }
                    //将考试实体初始化进去
                    $this->model->setEntity($exam, $screen);

                    $screen = $this->setFlowsnumToScreen($exam, $screen); //将该场次有多少流程写入场次对象
                    $screen->gradation_order = $key;
                    $this->model->screenPlan($screen);
                }
                if (count($this->model->getStudents()) != 0 || count($this->model->getWaitStudents()) != 0) {
//                    dd(count($this->model->getStudents()), count($this->model->getWaitStudents()), $key);
                    throw new \Exception('人数太多，所设时间无法完成考试', -99);
                }
                $this->checkUnnecessaryScreen($exam, $key);
            }
            return $this->output($exam);
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

    /**
     * 将数据保存
     * @param $exam
     * @throws \Exception
     * @author Jiangzhiheng
     * @time 2016-04-11 17:20
     */
    function store($exam)
    {
        // TODO: Implement store() method.
        $connection = \DB::connection('osce_mis');
        $connection->beginTransaction();
        try {
            $this->changeEffect($exam);

            $data = ExamPlanRecord::where('exam_id', $exam->id)->get();

            //循环插入数据
            $attributes = [];
            foreach ($data as $item) {
                $array = [
                    'exam_id' => $exam->id,
                    'exam_screening_id' => $item->exam_screening_id,
                    'student_id' => $item->student_id,
                    'station_id' => $item->station_id,
                    'room_id' => $item->room_id,
                    'begin_dt' => $item->begin_dt,
                    'end_dt' => $item->end_dt,
                    'status' => 0,
                    'group' => $item->group,
                    'flow_id' => $item->flow_id,
                    'serialnumber' => $item->serialnumber,
                    'created_user_id' => \Auth::id(),
                    'gradation_order' => $item->gradation_order
                ];
                $result = ExamPlan::create($array);

                if (!$result) {
                    throw new \Exception('保存失败，请重试！');
                } else {
                    $attributes[] = $result->toArray();
                }
            }

            //将考试使用了的实体的effected都变成1
            $this->changeEffect($exam, $attributes);

            //将数据写入stationStatus
            $this->stationStatus($exam);

            //将数据保存到examOrder
            $this->saveStudentOrder($exam);
            $connection->commit();
        } catch (\Exception $ex) {
            $connection->rollBack();
            throw $ex;
        }
    }

    /**
     * 将学生的排考情况导出
     * @param $export
     * @return mixed
     * @author Jiangzhiheng
     * @time 2016-05-01 09:45
     */
    public function export($id, $export, $arrange)
    {
        $data = $export->objToArray($arrange->getData($id));
        //\Log::debug($data);
        return $export->sheet('StudentList', function ($sheet) use ($data){
            $sheet->setWidth(config('osce.smart_arrange.width'));
            $sheet->rows($data);
        })->export('xlsx');
    }
}