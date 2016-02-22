<?php
/**
 * Created by PhpStorm.
 * User: j5110
 * Date: 2016/2/16
 * Time: 13:58
 */

namespace Modules\Osce\Entities\AutomaticPlanArrangement;


use Modules\Osce\Entities\Exam;
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


    /**
     * AutomaticPlanArrangement constructor.
     * @param $examId 考试id
     * @param ExamPlaceEntityInterface $examPlaceEntity ExamPlaceEntityInterface的实现
     * @param ExamInterface $exam ExamInterface的实现
     */
    function __construct($examId, ExamPlaceEntityInterface $examPlaceEntity, ExamInterface $exam)
    {
        /*
         * 初始化属性
         */
        $this->_Exam = Exam::findOrFail($examId);
        $this->_T_Count = count($examPlaceEntity->stationTotal($examId));
        $this->_T = $examPlaceEntity->stationTotal($examId);
        $this->_S_Count = count(Student::examStudent($examId));
        $this->_S = Student::examStudent($examId);
        $this->screen = $exam->screenList($examId);
        $this->sequenceMode = $this->_Exam->sequence_mode;

        /*
         * 设置考试实体的状态为true
         */
        foreach ($this->_T as &$item) {
            $item['status'] = true;
        }

        foreach ($this->_S as &$s) {
            $s['serialnumber'] = [];
        }


        /*
         * 将考试实体进行分组
         */
        $this->_TS = collect($this->_T)->groupBy('serialnumber');
    }

    /**
     * 智能排考
     * @author Jiangzhiheng
     * @time
     */
    function plan($examId)
    {
        /*
         * 依靠场次清单来遍历
         */
        foreach ($this->screen as $item) {
            $this->screenPlan($examId, $item);
        }

        //判断是否还有必要进行下场排考
        if (count($this->_S_ING) == 0 && count($this->_S) == 0) {
            return $this->output();
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
                 * 如果为true，就说明是开门状态
                 */
                if (!$this->ckeckStatus($station, $screen)) {
                    //获取实体所需要的学生清单
                    $students = $this->needStudents($station, $screen);

                    //变更学生的状态(写记录)
                    foreach ($students as &$student) {
                        //拼装数据
                        $data = $this->dataBuilder($examId, $screen, $student, $station, $i);

                        $result = ExamPlanRecord::create($data);
                        if (!$result) {
                            throw new \Exception('开门失败！', -10);
                        }

                        $this->tempPlan[] = $result;


                    }

                    //反之，则是关门状态
                } else {
                    //判断是否要开门
                    foreach ($this->tempPlan as &$value) {
                        if ($i >= strtotime($value->begin_dt) + config('osce.begin_dt_buffer') * 60) {
                            //将结束时间写在表内
                            $tempValue = ExamPlanRecord::findOrFail($value->id);
                            $tempValue->end_dt = date('Y-m-d H:i:s',$i);
                            if (!$tempValue->save()) {
                                throw new \Exception('关门失败！', -11);
                            }

                        }
                    }

                }
            }
        }


        dd(1111);
        //找到未考完的考生
        $examPlanEntity = ExamPlanRecord::whereNull('end_dt')->get();
        $undoneStudentsIds = $examPlanEntity->pluck('student_id');
        foreach ($undoneStudentsIds as $undoneStudentsId) {
            $undoneStudents[] = Student::findOrFail($undoneStudentsId);
        }

        //删除未考完学生记录
        if (!ExamPlanRecord::whereIn('student_id', $undoneStudentsIds)->delete()) {
            throw new \Exception('删除未考完考生记录失败！');
        }

        //获取候考区学生清单,并将未考完的考生还入总清单
        $this->_S = $this->_S->merge($this->_S_ING);
        $this->_S = $this->_S->merge($undoneStudents);
    }

    /**
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
                $v->transform(function ($v1, $k1) {
                    return strtotime($v1->mins);
                });
                $flowTime += $v->pluck('mins')->sort()->pop();
                //否则就直接加上这个值
            } else {
                $flowTime += strtotime($v->mins);
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
        //依据考试实体数量乘上系数为总数，进行循环
        for ($i = 0; $i < ($this->_T_Count) * config('osce.wait_student_num'); ++$i) {
            //将最后的学生弹出，放入到侯考区属性里
            $temp[] = $this->_S->pop();
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
     * @author Jiangzhiheng
     * @time
     * @return array|mixed
     */
    private function needStudents($station, $screen)
    {
        //获取正在考的考生
        $testStudents = $this->testStudents($station, $screen);

        //申明数组
        $result = [];

        /*
         * 获取当前实体需要几个考生 $station->needNum
         * 从正在考的学生里找到对应个数的考生
         * 如果该考生已经考过了这个流程，就忽略掉
         */
        $result = $this->studentNum($station, $testStudents, $result);

        /*
         * 如果$result中保存的人数少于考站需要的人数，就从侯考区里面补上，并将这些人从侯考区踢掉
         * 再将人从学生池里抽人进入侯考区
         * 直接使用shift函数
         */
        if (count($result) < $station->needNum) {
            for ($i = 0; $i < $station->needNum - count($result); $i++) {
                if (count($this->_S_ING) > 0) {
                    $result = array_shift($this->_S_ING);
                    if (count($this->_S) > 0) {
                        $this->_S_ING[] = array_shift($this->_S);
                    }
                }
            }
        }

        return $result;
    }

    /**
     * 获取正在考试的学生，并且把已经考完了的学生写进属性
     * @param $testStudents
     * @return mixed
     * @author Jiangzhiheng
     * @time
     */
    private function testingStudents($testStudents)
    {
        //获取当前考试的流程数组
        foreach ($this->_T as $item) {
            $serialnumber[] = $item->serialnumber;
        }

        //将两个数组进行比对，把已经考完的考生提出队列
        $testedStudents = [];
        foreach ($testStudents as $key => &$testingStudent) {
            if (count($serialnumber) == count($testingStudent)) { //如果流程数量等于考生已经考过的流程数
                $testedStudents[] = $testStudents->pull($key);  //就把这个值从学生数组里弹出来
            }
        }

        //写进属性
        $this->_S_END = $testedStudents;
        //返回数组
        return $testStudents;
    }

    /**
     * 拿到已经考过了的考生和正在考的考生
     * @param $screen
     * @return array 已经考过了的考生及其考试流程
     * @author Jiangzhiheng
     * @time
     */
    private function testStudents($station, $screen)
    {
        /*
         * 找到当前场次的所有的记录
         * 将其按考生id分组,拿到分组后的流程编号
         */
        $arrays = ExamPlanRecord::where('exam_screening_id', $screen->id)
            ->select('student_id', 'serialnumber')
            ->groupBy('student_id')
            ->get();

        if (count($arrays) == 0) {
            $arrays = $this->beginStudents($station);
        }
        /*
         * 处理数组，将二维数组重新组装
         * 组装完成后的$arrays为有哪些学生已经考过了，并且他们考了哪些流程
         */
        $tempArray = [];
        foreach ($arrays as $array) {
            $tempArray[$array->student_id][] = $array->serialnumber;
        }
        return $this->testingStudents($arrays);
    }

    /**
     * 刚刚进入考试的时候，需要调用此方法返回学生
     * @param $station
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
            //将考生从考生池弹进侯考区
            $this->_S_ING[] = $this->_S->shift();
        }
        return $students;

    }

    /**
     * 将结果展示在屏幕上
     * @author Jiangzhiheng
     * @time
     */
    private function output($examId)
    {
        $result = ExamPlanRecord::where('exam_id',$examId)
            ->get();

        $result = $result->groupBy('exam_screening_id');
        dd('output');
        $data = [];

//        foreach ($result as $key => $item) {
//            //获取该学生的实例
//            $student = Student::findOrFail($item->student_id);
//            //获取该考试实体
//            if (is_null($item->station)) {
//
//            }
//        }
    }

    /**
     * @param $station
     * @param $testStudents
     * @param $result
     * @return array
     * @author Jiangzhiheng
     * @time
     */
    private function studentNum($station, $testStudents, $result)
    {
        foreach ($testStudents as $testStudent) {
            if (!is_object($testStudent)) {
                if (in_array($station->serialnumber, $testStudent)) {
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
    private function ckeckStatus($station, $screen) {
        $examPlanRecord = ExamPlanRecord::where('station_id',$station->id)
            ->where('exam_screening_id',$screen->id)
            ->whereNull('end_dt')
            ->first();

        //如果有，说明是关门状态
        if (is_null($examPlanRecord)) {
            return false;
        } else {
            return true;
        }
    }
}