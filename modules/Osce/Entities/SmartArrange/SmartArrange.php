<?php
/**
 * Created by PhpStorm.
 * User: j5110
 * Date: 2016/4/7
 * Time: 11:25
 */

namespace Modules\Osce\Entities\SmartArrange;


use Modules\Osce\Entities\ExamPlanRecord;
use Modules\Osce\Entities\ExamStationStatus;
use Modules\Osce\Entities\SmartArrange\Student\StudentInterface;
use Modules\Osce\Entities\SmartArrange\Traits\SQLTraits;
use Modules\Osce\Entities\SmartArrange\Traits\SundryTraits;
use Modules\Osce\Entities\Student;
use Modules\Osce\Entities\ExamOrder;
use Modules\Osce\Entities\ExamDraft;
use Modules\Osce\Repositories\Common;

class SmartArrange
{
    use SQLTraits, SundryTraits;
    //当前考试实体，为对象
    public $exam;

    //侯考区学生,为对象集合
    protected $_S_W;

    //总学生，为对象集合
    protected $_S;

    //总学生人数
    protected $_S_Count;

    //总的考试实体，为对象集合
    protected $_E;

    //经过流程分组的考试实体，为对象集合
    protected $_E_F;

    //当前考试考站总数
    protected $stationCount;

    //开关门的状态
    protected $doorStatus = 0;

    //具体的模式包的实例
    protected $cate;

    //考站或考场的模式包
    protected $mode;

    //当前考试场次的流程个数
    private $flowNum;

    /**
     * @param $cate
     * @return $this
     * @author Jiangzhiheng
     * @time 2016-04-08 11:42
     */
    function setCate($cate)
    {
        $this->cate = $cate;
    }

    /**
     * 设置考生
     * @param StudentInterface $student
     * @author Jiangzhiheng
     * @time 2016-04-08 11:10
     */
    public function setStudents(StudentInterface $student)
    {
        $this->_S = $student->get($this->exam);
        $this->_S = $this->upset($this->_S);
        $this->_S_Count = count($this->_S);
        return $this->_S_Count;
    }

    /**
     * 打乱学生顺序
     * @param $students
     * @author Jiangzhiheng
     * @time 2016-04-08 16:40
     */
    public function upset($students)
    {
        return $students->shuffle();
    }

    /**
     * 获得考生
     * @return mixed
     * @author Jiangzhiheng
     * @time 2016-04-08 11:12
     */
    public function getStudents()
    {
        return $this->_S;
    }

    public function getWaitStudents()
    {
        return $this->_S_W;
    }

    /**
     * 获得实体
     * @return mixed
     * @author Jiangzhiheng
     * @time 2016-04-08 11:13
     */
    public function getEntity()
    {
        return $this->_E;
    }


    /**
     * @param $exam
     * @throws \Exception
     * @author Jiangzhiheng
     * @time
     */
    public function setEntity($exam, $screen)
    {
        $this->mode = ModeFactory::getMode($exam);
        $this->_E = $this->mode->entity($this->exam, $screen);
        $this->_E_F = $this->_E->groupBy('serialnumber');
        $this->stationCount = $this->_E->sum('needNum');
    }


    public function screenPlan($screen)
    {
        //重置考试实体计数器
        $this->resetStationTime();

        //获取当前考试场次的流程个数
        $this->flowNum = $this->flowNum($this->_E);

        /*
         * 获得场次的开始和结束时间
         */
        $beginDt = strtotime($screen->begin_dt);
        $endDt = strtotime($screen->end_dt);

        //本次场次的流程
        $serialnumber = array_unique($this->_E->pluck('serialnumber')->toArray());
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

        //将考生放入侯考区
        $this->_S_W = $this->waitExamQueue();
        //获得考试实体的最大公约数
        $mixCommonDivisors = [];
        foreach ($this->_E as $item) {
            $mixCommonDivisors[] = $item->mins + config('osce.begin_dt_buffer');
        }

        $mixCommonDivisor = Common::mixCommonDivisor($mixCommonDivisors);
        $this->doorStatus = $this->stationCount; //将当前所需人数作为开关门的初始值
        //初始化数据
        $i = $beginDt;
        //$k 枚举 1   2  3  4
        $k = 3;
        $step = $mixCommonDivisor * 60; //为考试实体考试时间的秒数
        //开始计时器
        while ($i <= $endDt) {
            //开门动作
            foreach ($this->_E as &$entity) {
                if ($this->doorStatus > 0) {
                    $tempBool = $this->checkStatus($entity, $screen);
                } else {
                    $tempBool = true;
                }
                if ($tempBool) { //反之，则是关门状态
                    $tempValues = $this->examPlanRecordIsOpenDoor($entity, $screen);
                    if (($entity->timer >= $entity->mins * 60 + config('osce.begin_dt_buffer') * 60)) {
                        $entity->timer = 0;
                        //将结束时间写在表内
                        foreach ($tempValues as $tempValue) {
                            if (!is_null($tempValue->end_dt)) {
                                continue;
                            }

                            $tempValue->end_dt = date('Y-m-d H:i:s', $i);
                            if (!$tempValue->save()) {
                                throw new \Exception('开门失败！', -10);
                            } else {
                                $this->doorStatus++;
                            }
                        }
                    }
                    else
                    {
                        $entity->timer += $step;
                    }
                }

            }
            //关门动作
            foreach ($this->_E as &$entity) {
                if ($this->doorStatus > 0) {
                    $tempBool = $this->checkStatus($entity, $screen);
                } else {
                    $tempBool = true;
                }
                if (!$tempBool) {
                    //将总考池和侯考区考生打包进数组
                    $params = [
                        'total' => $this->_S,
                        'wait' => $this->_S_W,
                        'serialnumber' => $serialnumber,
                        'exam' => $this->exam
                    ];
                    //将排序模式注入
                    $this->setCate(CateFactory::getCate($this->exam, $params));

                    $students = $this->cate->needStudents($entity, $screen, $this->exam);
                    $this->_S = $this->cate->getTotalStudent();
                    $this->_S_W = $this->cate->getWaitStudent();

                    if (count($students) == 0) {
                        continue;
                    }
                    //变更学生的状态(写记录)
                    foreach ($students as &$student) {
                        $data = $this->mode->dataBuilder($this->exam, $screen, $student, $entity, $i);
                        if (ExamPlanRecord::create($data)) {
                            $this->doorStatus--;
                            $entity->timer += $step;
                        } else {
                            throw new \Exception('关门失败！', -11);
                        };
                    }
                }
            }

            $i+=$step;

            //TODO 排完后终止循环的操作，待施工
            if ($this->overStudentCount($screen) == $this->_S_Count * $this->flowNum) {
//                dd($this->overStudentCount($screen), $this->_S_Count, $this->flowNum);
                break;
            }
        }

        //获取未走完流程的考生
        $studentList = $this->testingStudentList($this->exam, $screen, $this->flowNum);

        //未考完的学生实例数组
        $undoneStudents = [];

        if (count($studentList)) {
            $studentNotOvers = $studentList->pluck('student_id');

            //删除未走完流程的考生
            if (!ExamPlanRecord::whereIn('student_id', $studentNotOvers->toArray())->delete()) {
                throw new \Exception('考试未完成学生移动失败', -21);
            }

            //将没有考完的考生放回到总的考生池里
            foreach ($studentNotOvers as $studentNotOver) {
                $undoneStudents[] = Student::findOrFail($studentNotOver);
            }
        }

        //找到未考完的考生
        $examPlanEntity = ExamPlanRecord::whereNull('end_dt')->get();
        $undoneStudentsIds = $examPlanEntity->pluck('student_id');
        foreach ($undoneStudentsIds as $undoneStudentsId) {
            $undoneStudents[] = Student::findOrFail($undoneStudentsId);
        }

        //删除未考完学生记录
        if (!$undoneStudentsIds->isEmpty()) {
            if (!ExamPlanRecord::whereIn('student_id', $undoneStudentsIds)->delete()) {
                throw new \Exception('删除未考完考生记录失败！', -2101);
            }
        }

        //获取候考区学生清单,并将未考完的考生还入总清单
        $this->_S = $this->_S->merge($this->_S_W);
        $this->_S = $this->_S->merge(array_unique($undoneStudents));
    }

    /**
     * 更改effect
     * @param $exam
     * @param array $attributes
     * @author Jiangzhiheng
     * @time 2016-04-14 15:06
     */
    public function changeEffect($exam, array $attributes = [])
    {
        if (count($attributes) == 0) {
            $result = ExamDraft::join('exam_draft_flow', 'exam_draft.exam_draft_flow_id', '=', 'exam_draft_flow.id')
                ->where('exam_draft_flow.exam_id', $exam->id)->get();
            foreach ($result as $item) {
                $item->effected = 0;
                if (!$item->save()) {
                    throw new \Exception('数据更新失败！');
                }
            }
            return true;
        } else {
            switch ($exam->sequence_mode) {
                case 1:
                    $rooms = collect($attributes)->pluck('room_id')->unique()->toArray();
                    $a = ExamDraft::join('exam_draft_flow', 'exam_draft.exam_draft_flow_id', '=', 'exam_draft_flow.id')
                        ->whereIn('exam_draft.room_id', $rooms)
                        ->where('exam_draft_flow.exam_id', $exam->id)
                        ->get();
                    foreach ($a as $item) {
                        $item->effected = 0;
                        if (!$item->save()) {
                            throw new \Exception('数据更新失败！');
                        }
                    }

                    break;
                case 2:
                    $stations = collect($attributes)->pluck('station_id')->unique()->toArray();
                    $a = ExamDraft::join('exam_draft_flow', 'exam_draft.exam_draft_flow_id', '=', 'exam_draft_flow.id')
                        ->whereIn('exam_draft.station_id', $stations)
                        ->where('exam_draft_flow.exam_id', $exam->id)
                        ->get();
                    foreach ($a as $item) {
                        $item->effected = 0;
                        if (!$item->save()) {
                            throw new \Exception('数据更新失败！');
                        }
                    }

                    break;
                default:
                    throw new \Exception('没有这种考试模式！', -987);
                    break;
            }

            return true;
        }
    }

    /**
     * 将数据保存入ExamStationStatus
     * @param $exam
     * @param array $attributes
     * @throws \Exception
     * @author Jiangzhiheng
     * @time 2016-04-14 15：35
     */
    public function stationStatus($exam, array $attributes)
    {
        $examStationStatus = ExamStationStatus::where('exam_id', $exam->id)->get();

        if (!$examStationStatus->isEmpty()) {
            ExamStationStatus::where('exam_id', $exam->id)->delete();
        }

        foreach ($attributes as $attribute) {
            if (!ExamStationStatus::create($attribute)) {
                throw new \Exception('保存数据失败！');
            };
        }

        return true;
    }

    /**
     * 将数据保存在order表中
     * @param $exam
     * @throws \Exception
     * @author Jiangzhiheng
     * @time 2016-04-14 16:10
     */
    public function saveStudentOrder($exam)
    {
        //$planList = ExamOrder::where('exam_id', '=', $exam->id)->orderBy('begin_dt', 'asc')->get();
        $planList = ExamPlanRecord::where('exam_id', '=', $exam->id)->orderBy('begin_dt', 'asc')->get();
        $studentOrderData = [];
        if (ExamOrder::where('exam_id', '=', $exam->id)->delete() === false) {
            throw new \Exception('弃用旧安排失败');
        }
//        try {
        foreach ($planList as $plan) {
            if (!array_key_exists($plan->student_id, $studentOrderData)) {
                $studentOrderData[$plan->student_id] = [
                    'exam_id' => $exam->id,
                    'exam_screening_id' => $plan->exam_screening_id,
                    'student_id' => $plan->student_id,
                    'begin_dt' => $plan->begin_dt,
                    'status' => 0,
                    'created_user_id' => \Auth::id(),
                ];
            }
        }
        foreach ($studentOrderData as $stduentOrder) {
            if (!ExamOrder::create($stduentOrder)) {
                throw new \Exception('保存学生考试顺序失败');
            }
        }
//        } catch (\Exception $ex) {
//            throw $ex;
//        }
    }
}