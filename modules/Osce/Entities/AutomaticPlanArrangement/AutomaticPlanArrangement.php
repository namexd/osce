<?php
/**
 * Created by PhpStorm.
 * User: j5110
 * Date: 2016/2/16
 * Time: 13:58
 */

namespace Modules\Osce\Entities\AutomaticPlanArrangement;


use Modules\Osce\Entities\Exam;
use Modules\Osce\Entities\ExamPlanRecord;
use Modules\Osce\Entities\Student;

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
         * 设置考试实体的状态为false
         */
        foreach ($this->_T as &$item) {
            $item['status'] = false;
        }


        /*
         * 将考试实体进行分组
         */
        $this->_TS = $this->_T->groupBy('serialnumber');
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
    }

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
                if ($station->status) {
                    //获取实体所需要的学生清单
                    $students = $this->needStudents($this->_S, $this->_S_ING);
                    //变更学生的状态(写记录)
                    foreach ($students as &$student) {
                        //拼装数据
                        $data = $this->dataBuilder($examId, $screen, $student, $station, $i);

                        $result = ExamPlanRecord::create($data);
                        if (!$result) {
                            throw new \Exception('开门失败！', -10);
                        }

                        $tempPlan[] = $result;

                    }

                    //变更考试实体状态
                    $station->status = false;

                    //反之，则是关门状态
                } else {
                    //判断是否要开门
                    foreach ($tempPlan as $value) {
                        if ($i >= strtotime($value->begin_dt) + config('osce.begin_dt_buffer') * 60) {
                            //将结束时间写在表内
                            $value->end_dt = $i;
                            if (!$value->save()) {
                                throw new \Exception('关门失败！',-11);
                            }

                            //将考试实体的状态变成true
                            $station->status = true;
                        }
                    }

                }
            }

            //找到未考完的考生
            $examPlanEntity = ExamPlanRecord::whereNull('end_dt')->get();

            $undoneStudentsId = $examPlanEntity->pluck('student_id');

            //删除未考完学生记录
            if (!ExamPlanRecord::whereIn('student_id',$undoneStudentsId)->delete()) {
                throw new \Exception('删除未考完考生记录失败！');
            }

            //获取候考区学生清单,并将未考完的考生还入总清单
            $this->_S = $this->_S->merge($this->_S_ING);

        }

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
            if (is_array($v)) {
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
                'student_id' => $student->id,
                'room_id' => $station->id,
                'exam_id' => $examId,
                'exam_screening_id' => $screen->id,
                'begin_dt' => date('Y-m-d H:i:s', $i),
                'serialnumber' => $station->serialnumber,
            ];
        } elseif ($this->sequenceMode == 2) {
            $data = [
                'student_id' => $student->id,
                'room_id' => $station->room_id,
                'station_id' => $station->id,
                'exam_id' => $examId,
                'exam_screening_id' => $screen->id,
                'begin_dt' => date('Y-m-d H:i:s', $i),
                'serialnumber' => $station->serialnumber,
            ];
        } else {
            throw new \Exception('系统错误，请重试！',-5);
        }
        return $data;
    }
}