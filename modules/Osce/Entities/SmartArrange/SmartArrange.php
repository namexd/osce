<?php
/**
 * Created by PhpStorm.
 * User: j5110
 * Date: 2016/4/7
 * Time: 11:25
 */

namespace Modules\Osce\Entities\SmartArrange;


use Modules\Osce\Entities\ExamPlanRecord;
use Modules\Osce\Entities\SmartArrange\Student\StudentInterface;
use Modules\Osce\Entities\SmartArrange\Traits\SQLTraits;
use Modules\Osce\Entities\SmartArrange\Traits\SundryTraits;
use Modules\Osce\Entities\Student;
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

    //总的考试实体，为对象集合
    protected $_E;

    //经过流程分组的考试实体，为对象集合
    protected $_E_F;

    //开关门的状态
    protected $doorStatus = 0;

    //具体的模式包的实例
    protected $cate;

    //考站或考场的模式包
    protected $mode;

    /**
     * @param $cate
     * @return $this
     * @author Jiangzhiheng
     * @time 2016-04-08 11:42
     */
    function setCate($cate)
    {
        $this->cate = $cate;
        return $this;
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
    }


    public function screenPlan($screen)
    {
        //重置考试实体计数器
        $this->resetStationTime();

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
        $this->doorStatus = count($this->_E); //将当前实体的个数作为开关门的初始值

        //初始化数据
        $i = $beginDt;
        $k = 3;
        $step = $mixCommonDivisor * 60; //为考试实体考试时间的秒数

        //开始计时器
        while ($i <= $endDt) {
            foreach ($this->_E as $entity) {
                if ($this->doorStatus > 0) {
                    $tempBool = $this->checkStatus($entity, $screen);
                } else {
                    $tempBool = true;
                }


                if (!$tempBool) {
                    //将总考池和侯考区考生打包进数组
                    $params = ['total' => $this->_S, 'wait' => $this->_S_W, 'serialnumber' => $serialnumber];
                    list($students, $params) = $this->cate->needStudents($entity, $screen, $this->exam, $params);
                    $this->_S = $params['total'];
                    $this->_S_W = $params['wait'];

                    if (count($students) == 0) {
                        continue;
                    }

                    //变更学生的状态(写记录)
                    foreach ($students as $student) {
                        $data = $this->mode->dataBuilder($this->exam, $screen, $student, $entity, $i);
                        if (ExamPlanRecord::create($data)) {
                            $this->doorStatus--;
                        } else {
                            throw new \Exception('关门失败！', -11);
                        };
                    }

                    $entity->timer += $step;
                } else { //反之，则是关门状态
                    $tempValues = $this->examPlanRecordIsOpenDoor($entity, $screen);
                    if ($entity->timer >= $entity->mins * 60 + config('osce.begin_dt_buffer') * 60) {
                        $entity->timer = 0;
                        //将结束时间写在表内


                        foreach ($tempValues as $tempValue) {
                            if (!empty($tempValue->end_dt)) {
                                continue;
                            }
                            $tempValue->end_dt = date('Y-m-d H:i:s', $i);
                            if (!$tempValue->save()) {
                                throw new \Exception('开门失败！', -10);
                            } else {
                                $k = 1;
                                $this->doorStatus++;
                            }
                        }
                    } else {
                        $entity->timer += $step;
                    }
                }
            }

            if ($k === 3) {
                $step = $mixCommonDivisor * 60;
                $i += $step;
            }
            if ($k === 2) {
                $step = $mixCommonDivisor * 60;
                $k = 3;
                $i += $step;
            }
            if ($k === 1) {
                $k = 2;
            }
        }
        //TODO 排玩后终止循环的操作，待施工

        //获取当前考试场次的流程个数
        $flowsNum = $this->flowNum($this->exam, $screen);
        
        //获取未走完流程的考生
        $studentList = $this->testingStudentList($this->exam, $flowsNum);

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
}