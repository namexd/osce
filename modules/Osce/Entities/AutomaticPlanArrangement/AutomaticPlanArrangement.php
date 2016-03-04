<?php
/**
 * Created by PhpStorm.
 * User: j5110
 * Date: 2016/2/16
 * Time: 13:58
 */

namespace Modules\Osce\Entities\AutomaticPlanArrangement;


use Modules\Osce\Entities\Exam;
use Modules\Osce\Entities\ExamFlow;
use Modules\Osce\Entities\ExamFlowStation;
use Modules\Osce\Entities\ExamPlanRecord;
use Modules\Osce\Entities\Student;
use Modules\Osce\Entities\ExamFlowRoom;

class AutomaticPlanArrangement
{
    //考试学生数量
    protected $_S_Count = 0;

    //考站数量
    protected $_T_Count = 0;

    //考站数组
    protected $_T = [];

    //考生数组
    protected $_S = [];

    //考试顺序规则
    protected $_TS = [];

    //侯考区队列
    protected $_S_ING = [];

    //已经考完的考生
    protected $_S_END = [];

    //考站队列
    protected $_Q_STA = [];

    /*
     * 该考试对应的考试场次
     */
    protected $_Screen = '';

    /*
     * 考试实例
     */
    protected $_Exam = '';

    /*
     * 考试模式（是考站模式还是考场模式）
     */
    protected $sequenceMode = '';

    protected $tempPlan = [];

    /*
     * 计时器，用来保存考站的考试时间
     */
    protected $timer = 0;

    protected $exam_id = 0;


    /**
     * AutomaticPlanArrangement constructor.
     * @param $examId 考试id
     * @param ExamPlaceEntityInterface $examPlaceEntity ExamPlaceEntityInterface的实现
     * @param ExamInterface $exam ExamInterface的实现
     */
    function __construct($examId, ExamPlaceEntityInterface $examPlaceEntity, ExamInterface $exam)
    {
        try {
            /*
             * 初始化属性
             */
            $this->_Exam = Exam::findOrFail($examId);
            $this->_T_Count = count($examPlaceEntity->stationTotal($examId));
            $this->_T = $examPlaceEntity->stationTotal($examId);
            $this->_S_Count = count(Student::examStudent($examId));
            $this->_S = Student::examStudent($examId)->shuffle();
            $this->screen = $exam->screenList($examId);
            $this->sequenceMode = $this->_Exam->sequence_mode;
            $this->sequenceCate = $this->_Exam->sequence_cate;
            $this->exam_id = $examId;

            /*
             * 设置考试实体的状态为true
             */
            foreach ($this->_T as &$item) {
                $item['status'] = true;
                $item['timer'] = 0;
            }

            foreach ($this->_S as &$s) {
                $s['serialnumber'] = [];
            }


            /*
             * 将考试实体进行分组
             */
            $this->_TS = collect($this->_T)->groupBy('serialnumber');
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    /**
     * 智能排考
     * @author Jiangzhiheng
     * @time
     * @param $examId
     * @return array
     * @throws \Exception
     */
    function plan($examId)
    {
        try {
            if (count($this->_S) == 0) {
                throw new \Exception('当前考试没有安排学生！', -705);
            }

            /*
             * 排考的时候删除原先的所有数据
             */
            if (ExamPlanRecord::where('exam_id', $examId)->count()) {
                if (!ExamPlanRecord::where('exam_id', $examId)->delete()) {
                    throw new \Exception('清空所有数据失败！');
                };
            }

            /*
             * 依靠场次清单来遍历
             */
            foreach ($this->screen as $item) {
                $this->screenPlan($examId, $item);
                //判断是否还有必要进行下场排考
                $examPlanNull = ExamPlanRecord::whereNull('end_dt')->first();  //通过查询数据表中是否有没有写入end_dt的数据
                if (count($this->_S_ING) == 0 && count($this->_S) == 0 && is_null($examPlanNull)) {
                    return $this->output($examId);
                }
            }
//            dump($this->_S);
//            dump($this->_S_ING);
//            dump($examPlanNull);
//            echo '==============================';
            throw new \Exception('人数太多，所设时间无法完成考试', -888);
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    /**
     * @param $examId
     * @param $screen
     * @throws \Exception
     * @author Jiangzhiheng
     * @time
     */
    private function screenPlan($examId, $screen)
    {
        $this->resetStationTime();

        /*
         * 获得场次的开始和结束时间
         */
        $beginDt = strtotime($screen->begin_dt);
        $endDt = strtotime($screen->end_dt);
        /*
         * 得到完整流程所需的时间
         * 先初始化流程总时间
         */
        $flowTime = $this->flowTime();
        /*
         * 判断场次是否够一个完整的流程
         */
        if (($endDt - $beginDt - $flowTime) < 0) {
            throw new \Exception('这场考试安排的场次时间太短，无法考完预定科目！');
        }

        //将学生由总清单放入侯考区队列
        $this->_S_ING = $this->waitExamQueue();
        //开始计时器
        for ($i = $beginDt; $i <= $endDt; $i += 60) {
            foreach ($this->_T as &$station) {
                /*
                 * 考试实体状态判断,使用exam_plan_record来判断状态
                 * 如果为false，就说明是开门状态
                 */
                $tempBool = $this->ckeckStatus($station, $screen);

                if (!$tempBool) {
                    //获取实体所需要的学生清单
                    $students = $this->needStudents($station, $screen, $examId);
                    if (count($students) == 0) {
                        continue;
                    }

                    //变更学生的状态(写记录)
                    foreach ($students as &$student) {
                        //拼装数据
                        $data = $this->dataBuilder($examId, $screen, $student, $station, $i);
                        $result = ExamPlanRecord::create($data);
                        if (!$result) {
                            throw new \Exception('关门失败！', -11);
                        }

                        $this->tempPlan[] = $result;
                    }
                    $station->timer += 60;
                    //反之，则是关门状态
                } else {
                    $tempValues = $this->examPlanRecordIsOpenDoor($station, $screen);
                    if ($station->timer >= $station->mins * 60 + config('osce.begin_dt_buffer') * 60) {
                        $station->timer = 0;
                        //将结束时间写在表内
                        foreach ($tempValues as $tempValue) {
                            $tempValue->end_dt = date('Y-m-d H:i:s', $i - 1);
                            if (!$tempValue->save()) {
                                throw new \Exception('开门失败！', -10);
                            }
                        }
                    } else {
                        $station->timer += 60;
                    }
                }
            }
        }

        //获取未走完流程的考生
        $ExamFlowModel = new ExamFlow();
        $flowsNum = $ExamFlowModel->studentExamSum($examId);
        //SELECT count(`id`) as total,`student_id` FROM `exam_plan_record` where`exam_id` = 25 Group by `student_id` Having total <> 2
        $studentList = ExamPlanRecord::  where('exam_id', '=', $examId)
            ->whereNotNull('end_dt')
            ->groupBy('student_id')
            ->select(\DB::raw(
                implode(',',
                    [
                        'count(`id`) as flowsNum',
                        'id',
                        'student_id',
                    ]
                )
            ))
            ->Having('flowsNum', '<', $flowsNum)
            ->get();
//        $undoneStudents = [];
        if (count($studentList)) {
            $studentNotOvers = $studentList->pluck('student_id');
//            foreach ($studentNotOvers as $studentNotOver) {
//                $undoneStudents[] = Student::find($studentNotOver);
//            }

            //删除未走完流程的考生
            if (!ExamPlanRecord::whereIn('student_id', $studentNotOvers->toArray())->delete()) {
                throw new \Exception('考试未完成学生移动失败', -2100);
            }
        }
        //找到未考完的考生
        $undoneStudents = [];
        $examPlanEntity = ExamPlanRecord::whereNull('end_dt')->get();
        $undoneStudentsIds = $examPlanEntity->pluck('student_id');
        foreach ($undoneStudentsIds as $undoneStudentsId) {
            $undoneStudents[] = Student::findOrFail($undoneStudentsId);
        }
//
//        //删除未考完学生记录
        if (!$undoneStudentsIds->isEmpty()) {
            if (!ExamPlanRecord::whereIn('student_id', $undoneStudentsIds)->delete()) {
                throw new \Exception('删除未考完考生记录失败！', -2101);
            }
        }

        //获取候考区学生清单,并将未考完的考生还入总清单
        $this->_S = $this->_S->merge($this->_S_ING);
        $this->_S = $this->_S->merge($undoneStudents);
    }

    /**
     * 获取流程时间
     * @return int
     * @author Jiangzhiheng
     * @time 2016-02-17 10:24
     */
    private function flowTime()
    {
        $flowTime = 0;
        foreach ($this->_TS as $v) {
            //如果是数组，先将时间字符串变成时间戳，然后排序，并取最后（最大的数）;
            if (is_array($v->all())) {
                $flowTime += $v->pluck('mins')->sort()->pop();
                //否则就直接加上这个值
            } else {
                $flowTime += $v->mins;
            }
        }
        return $flowTime;
    }

    /**
     * 将学生从总清单中放入侯考区
     * @author Jiangzhiheng
     * @time
     */
    private function waitExamQueue()
    {
        $temp = [];
        //依据考试实体数量乘上系数为总数，进行循环
        for ($i = 0; $i < ($this->_T_Count) * config('osce.wait_student_num'); ++$i) {
            //将最后的学生弹出，放入到侯考区属性里
            if (count($this->_S) != 0) {
                $temp[] = $this->_S->pop();
            }
        }

        return $temp;
    }

    /**
     * @param $examId
     * @param $screen
     * @param $student
     * @param $station
     * @param $i
     * @return array
     * @throws \Exception
     * @author Jiangzhiheng
     * @time
     */
    private function dataBuilder($examId, $screen, $student, $station, $i)
    {
        if ($this->sequenceMode == 1) {
            $data = [
                'student_id' => is_null($student->id) ? $student->student_id : $student->id,
                'room_id' => $station->id,
                'exam_id' => $examId,
                'exam_screening_id' => $screen->id,
                'begin_dt' => date('Y-m-d H:i:s', $i),
                'serialnumber' => $station->serialnumber,
            ];
        } elseif ($this->sequenceMode == 2) {
            $data = [
                'student_id' => is_null($student->id) ? $student->student_id : $student->id,
                'room_id' => $station->room_id,
                'station_id' => $station->id,
                'exam_id' => $examId,
                'exam_screening_id' => $screen->id,
                'begin_dt' => date('Y-m-d H:i:s', $i),
                'serialnumber' => $station->serialnumber,
            ];
        } else {
            throw new \Exception('系统错误，请重试！', -5);
        }
        return $data;
    }

    /**
     * 将符合条件的学生从侯考区放入正在考试，从大的学生池里将学生进行补位
     * @param $station 考试实体
     * @param $screen 考试场次实例
     * @param $examId
     * @return array|mixed
     * @throws \Exception
     * @author Jiangzhiheng
     * @time
     */
    private function needStudents($station, $screen, $examId)
    {
        //获取正在考的考生
        switch ($this->sequenceCate) {
            case 1:
                $result = $this->randomMode($station, $screen, $examId);
                break;
            case 2:
                $result = $this->orderMode($station, $screen);
                break;
            case 3:
                $result = $this->pollMode($station, $screen, $examId);
                break;
            default:
                throw new \Exception('没有对应的考试排序模式！');
        }

        return $result;
    }

    /**
     * 获取正在考试的学生，并且把已经考完了的学生写进属性
     * @param $testStudents
     * @return mixed
     * @author Jiangzhiheng
     * @time 2016-02-19 15：36
     */
    private function testingStudents($testStudents)
    {
        //获取当前考试的流程数组
        foreach ($this->_T as $item) {
            $serialnumber[] = $item->serialnumber;
        }

        $serialnumber = array_unique($serialnumber);

        //将两个数组进行比对，把已经考完的考生踢出队列
        $testedStudents = [];
        $tempTestStudent = [];
        foreach ($testStudents as $key => $testingStudent) {
            if (is_null($testingStudent)) {
                continue;
            }
            //获取该考生已经考过的流程
            $studentSerialnumber = $this->getStudentSerialnumber($testingStudent);

            if (is_array($testStudents)) {
                $student = array_pull($testStudents, $key);
            } else {
                $student = $testStudents->pull($key);  //就把这个值从学生数组里弹出来
            }

            if (is_null($student)) {
                continue;
            }
            if (count($serialnumber) == count($studentSerialnumber)) { //如果流程数量等于考生已经考过的流程数
                $testedStudents[] = $student;  //就把这个值从学生数组里弹出来
            } else {
                $tempTestStudent[] = $student;  //就把这个值从学生数组里弹出来
            }
        }

        //写进属性
        $this->_S_END = $testedStudents;
        //返回数组
        return $tempTestStudent;

    }

    /**
     * 拿到已经考过了的考生和正在考的考生
     * @param $station 考站实例
     * @param $screen 考试场次实例
     * @return array 已经考过了的考生及其考试流程
     * @throws \Exception
     * @author Jiangzhiheng
     * @time
     */
    private function randomTestStudents($station, $screen)
    {
        /*
         * 找到当前场次的所有的记录
         * 将其按考生id分组,拿到分组后的流程编号
         */
        $tempArrays = ExamPlanRecord::randomBeginStudent($screen);

        $num = $this->waitingStudentSql($screen);

        $arrays = [];
        foreach ($num as $item) {
            $arrays[] = $item->student;
        }

        dump();
        if (count($tempArrays) == 0) {
            $arrays = $this->beginStudents($station);
        }
        return $this->testingStudents($arrays);
    }

    /**
     * 排考方式：轮询的正在考试和已考完的学生
     * @param $station
     * @param $screen
     * @return mixed
     * @throws \Exception
     * @author Jiangzhiheng
     * @time
     */
    private function pollTestStudents($station, $screen)
    {
        $tempArrays = ExamPlanRecord::pollBeginStudent($station, $screen, $this->sequenceMode);

        $num = $this->waitingStudentSql($screen);

        $arrays = [];
        foreach ($num as $item) {
            $arrays[] = $item->student;
        }


        if (count($tempArrays) == 0) {
            $arrays = $this->beginStudents($station);
        }

        return $this->testingStudents($arrays);
    }

    private function orderTestStudent($station, $screen)
    {
        /**
         * 需要查当前的实例是不是第一个
         * 如果等于1，就说明是第一个，直接从侯考区取人
         * 如果不是，就说明前面有流程了
         */
        $tempArrays = [];
        if ($station->serialnumber != 1) {
            $tempArrays = ExamPlanRecord::orderBeginStudent($screen, $station->serialnumber,
                $this->sequenceMode);
            if (count($tempArrays) != 0) {
                return Student::whereIn('id', $tempArrays)->get();
            } else {
                return collect([]);
            }

        } else {
            return [];
        }
    }

    /**
     * 刚刚进入考试的时候，需要调用此方法返回学生
     * @param $station 考站实例
     * @return array
     * @author Jiangzhiheng
     * @time 2016-02-18 17:18
     */
    private function beginStudents($station)
    {
        /*
         * 将考站所需的考生直接返回
         * 将返回的考生从侯考区里干掉
         */
        for ($i = 0; $i < $station->needNum; $i++) {
            //将学生弹出
            $students[] = array_shift($this->_S_ING);
            $a = $this->_S->shift();
            //将考生从考生池弹进侯考区
            if (is_null($a)) {
                continue;
            }
            $this->_S_ING[] = $a;
        }
        return $students;

    }

    /**
     * 将结果展示在屏幕上
     * @author Jiangzhiheng
     * @time 2016-02-22 17：52
     * @param $examId 考试id
     * @return array 拼装完成的数组
     */
    public function output($examId)
    {
        $result = ExamPlanRecord::where('exam_id', $examId)
            ->get();
//        dd($result);
        $exam = Exam::findOrFail($examId);

        $arrays = [];
        foreach ($result as $record) {
            //$arrays = $screen->groupBy('station_id');
            $screeningId = $record->exam_screening_id;
            $station_id = $record->station_id;
            //$station        =   $record->station;
            $screeningId = $record->exam_screening_id;
            if ($exam->sequence_mode == 1) //考场模式
            {
                $arrays[$screeningId][$record->room_id][strtotime($record->begin_dt)][] = $record;
            } else //考站模式
            {
                $arrays[$screeningId][$record->room_id . '-' . $record->station_id][strtotime($record->begin_dt)][] = $record;
            }
        }

        $timeData = [];
        foreach ($arrays as $screeningId => $screening) {
            foreach ($screening as $entityId => $timeList) {
                foreach ($timeList as $batch => $recordList) {
                    foreach ($recordList as $record) {
                        if ($exam->sequence_mode == 1) //考场模式
                        {
                            $name = $record->room->name;
                        } else //考站模式
                        {
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
     *获取实体需要的时间
     * @param $station
     * @param $testStudents
     * @param $result
     * @return array
     * @author Jiangzhiheng
     * @time
     */
    private function studentNum($station, $testStudents, $result, $examId)
    {
        foreach ($testStudents as $testStudent) {
            if (is_object($testStudent)) {
                $serialnumber = $this->getStudentSerialnumber($testStudent);

                if (in_array($station->serialnumber, $serialnumber->toArray())) {
                    continue;
                }
            }

            $result[] = $testStudent;

            //如果考生的人数等于考试实体需要的人数，就打断循环，输出这个值
            if (count($result) == $station->needNum) {
                break;
            }
        }
        return $result;
    }

    /**
     * 检查考试实体的状态
     * @param $station
     * @param $screen
     * @return bool
     * @author Jiangzhiheng
     * @time
     */
    private function ckeckStatus($station, $screen)
    {
        $examPlanRecord = $this->examPlanRecordIsOpenDoor($station, $screen);

        //如果有，说明是关门状态
        if ($examPlanRecord->isEmpty()) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * @param $screen
     * @author Jiangzhiheng
     * @time
     */
    private function testingStudentSql($screen)
    {
        return ExamPlanRecord::where('exam_screening_id', $screen->id)
            ->groupBy('student_id')
            ->select(\DB::raw('(count(end_dt) <> count(begin_dt)) as num,student_id'))
            ->where('exam_screening_id', $screen->id)
            ->havingRaw('num > ?', [0])
            ->get();
    }

    /**
     * 查询到等待的学生信息
     * @param $screen
     * @return mixed
     * @author Jiangzhiheng
     * @time 2016-02-24 17:35
     */
    private function waitingStudentSql($screen)
    {
        return ExamPlanRecord::where('exam_screening_id', $screen->id)
            ->groupBy('student_id')
            ->select(\DB::raw('(count(end_dt) = count(begin_dt)) as num,student_id,count(`station_id`) as flows_num'))
            ->where('exam_screening_id', $screen->id)
            ->havingRaw('num > ?', [0])
            ->havingRaw('flows_num < ?', [count($this->_TS)])
            ->get();
    }

    /**
     * @param $station
     * @param $screen
     * @return mixed
     * @throws \Exception
     * @author Jiangzhiheng
     * @time
     */
    private function examPlanRecordIsOpenDoor($station, $screen)
    {
        if ($this->sequenceMode == 2) {
            return ExamPlanRecord::where('station_id', '=', $station->id)
                ->where('exam_screening_id', '=', $screen->id)
                ->whereNull('end_dt')
                ->get();
        } elseif ($this->sequenceMode == 1) {
            return ExamPlanRecord::where('room_id', '=', $station->id)
                ->where('exam_screening_id', '=', $screen->id)
                ->whereNull('end_dt')
                ->get();
        } else {
            throw new \Exception('没有选定的考试模式！', -706);
        }

    }

    /**
     * 获取学生序号
     * @param $testingStudent
     * @return mixed
     * @author Jiangzhiheng
     * @time
     */
    private function getStudentSerialnumber($testingStudent)
    {
        $studentSerialnumber = ExamPlanRecord::where('student_id', $testingStudent->id)
            ->where('exam_id', $this->exam_id)->get()
            ->pluck('serialnumber');
        return $studentSerialnumber;
    }

    /**
     * 重置考站时间
     * @return mixed
     * @author Jiangzhiheng
     * @time
     */
    private function resetStationTime()
    {
        foreach ($this->_T as &$station) {
            $station->timer = 0;
        }
    }

    /**
     * 随机模式的学生选择
     * @param $station
     * @param $screen
     * @param $examId
     * @return array
     * @author Jiangzhiheng
     * @time 2016-03-04 21:20
     */
    private function randomMode($station, $screen, $examId)
    {
        $testStudents = $this->randomTestStudents($station, $screen);
        //申明数组
        $result = [];
        /*
         * 获取当前实体需要几个考生 $station->needNum
         * 从正在考的学生里找到对应个数的考生
         * 如果该考生已经考过了这个流程，就忽略掉
         */
        $result = $this->studentNum($station, $testStudents, $result, $examId);

        /*
         * 如果$result中保存的人数少于考站需要的人数，就从侯考区里面补上，并将这些人从侯考区踢掉
         * 再将人从学生池里抽人进入侯考区
         * 直接使用array_shift函数
         */
        if (count($result) < $station->needNum) {
            for ($i = 0; $i <= $station->needNum - count($result); $i++) {
                if (count($this->_S_ING) > 0) {
                    $thisStudent = array_shift($this->_S_ING);
                    if (!is_null($thisStudent)) {
                        $result[] = $thisStudent;
                    }
                    if (count($this->_S) > 0) {
                        if (is_array($this->_S)) {
                            $this->_S_ING[] = array_shift($this->_S);
                        } else {
                            $this->_S_ING[] = $this->_S->shift();
                        }

                    }
                }
            }
        }
        return $result;
    }

    /**
     * 顺序模式的学生选择
     * @param $station
     * @param $screen
     * @return array|\Illuminate\Support\Collection
     * @author Jiangzhiheng
     * @time 2016-03-04 21:23
     */
    private function orderMode($station, $screen)
    {
        $result = [];
        $testStudents = $this->orderTestStudent($station, $screen);
        if ($station->serialnumber == 1) {
            for ($i = 0; $i < $station->needNum; $i++) {
                if (count($this->_S_ING) > 0) {
                    $thisStudent = array_shift($this->_S_ING);
                    if (!is_null($thisStudent)) {
                        $result[] = $thisStudent;
                    }
                    if (count($this->_S) > 0) {
                        if (is_array($this->_S)) {
                            $this->_S_ING[] = array_shift($this->_S);
                        } else {
                            $this->_S_ING[] = $this->_S->shift();
                        }
                    }
                }
            }
            return $result;
        } else {
            if (count($testStudents) <= $station->needNum) {
                $result = $testStudents;
                return $result;
            } elseif (count($testStudents) > $station->needNum) {
                for ($i = 0; $i < $station->needNum; $i++) {
                    $result[] = $testStudents->shift();
                }
                return $result;
            }
            return $result;
        }
    }

    /**
     * @param $station
     * @param $screen
     * @param $examId
     * @return array
     * @author Jiangzhiheng
     * @time
     */
    private function pollMode($station, $screen, $examId)
    {
        $testStudents = $this->pollTestStudents($station, $screen);
        //申明数组
        $result = [];
        /*
         * 获取当前实体需要几个考生 $station->needNum
         * 从正在考的学生里找到对应个数的考生
         * 如果该考生已经考过了这个流程，就忽略掉
         */
        $result = $this->studentNum($station, $testStudents, $result, $examId);

        /*
         * 如果$result中保存的人数少于考站需要的人数，就从侯考区里面补上，并将这些人从侯考区踢掉
         * 再将人从学生池里抽人进入侯考区
         * 直接使用array_shift函数
         */
        if (count($result) < $station->needNum) {
            for ($i = 0; $i <= $station->needNum - count($result); $i++) {
                if (count($this->_S_ING) > 0) {
                    $thisStudent = array_shift($this->_S_ING);
                    if (!is_null($thisStudent)) {
                        $result[] = $thisStudent;
                    }
                    if (count($this->_S) > 0) {
                        if (is_array($this->_S)) {
                            $this->_S_ING[] = array_shift($this->_S);
                        } else {
                            $this->_S_ING[] = $this->_S->shift();
                        }
                    }
                }
            }
            return $result;
        }
        return $result;
    }
}