<?php
/**
 * Created by PhpStorm.
 * User: j5110
 * Date: 2016/4/7
 * Time: 11:25
 */

namespace Modules\Osce\Entities\SmartArrange;


use Modules\Osce\Entities\ExamPlanRecord;
use Modules\Osce\Entities\ExamScreening;
use Modules\Osce\Entities\ExamStationStatus;
use Modules\Osce\Entities\SmartArrange\Cate\AbstractCate;
use Modules\Osce\Entities\SmartArrange\Cate\CateInterface;
use Modules\Osce\Entities\SmartArrange\Cate\Order;
use Modules\Osce\Entities\SmartArrange\Cate\Poll;
use Modules\Osce\Entities\SmartArrange\Cate\Random;
use Modules\Osce\Entities\SmartArrange\Entity\AbstractEntity;
use Modules\Osce\Entities\SmartArrange\Entity\EntityInterface;
use Modules\Osce\Entities\SmartArrange\Entity\RoomMode;
use Modules\Osce\Entities\SmartArrange\Entity\StationMode;
use Modules\Osce\Entities\SmartArrange\Student\StudentInterface;
use Modules\Osce\Entities\SmartArrange\Traits\SQLTraits;
use Modules\Osce\Entities\SmartArrange\Traits\SundryTraits;
use Modules\Osce\Entities\Student;
use Modules\Osce\Entities\ExamOrder;
use Modules\Osce\Entities\ExamPlan;
use Modules\Osce\Entities\ExamDraft;
use Modules\Osce\Repositories\Common;

/**
 * @property Order|Random|Poll|AbstractCate|CateInterface $cate
 * @property RoomMode|StationMode $mode
 * Class SmartArrange
 * @package Modules\Osce\Entities\SmartArrange
 */
class SmartArrange
{
    use SQLTraits, SundryTraits;
    //当前考试实体，为对象
    private $exam;

    //获取学生的实例
    private $student;

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

    public function __construct(StudentInterface $student)
    {
        return $this->student = $student;
    }

    /**
     * 设置考试实例
     * @param $exam
     * @author ZouYuChao
     * @time 2016-04-28 15:27
     */
    public function setExam($exam)
    {
        return $this->exam = $exam;
    }

    /**
     * @param $cate
     * @return $this
     * @author ZouYuChao
     * @time 2016-04-08 11:42
     */
    function setCate($cate)
    {
        $this->cate = $cate;
    }

    /**
     * 设置考生
     * @param StudentInterface $student
     * @author ZouYuChao
     * @time 2016-04-08 11:10
     */
    public function setStudents()
    {
        $this->_S = $this->student->get($this->exam);
        $this->_S = $this->upset($this->_S);
        $this->_S_Count = count($this->_S);
        return $this->_S_Count;
    }

    /**
     * 打乱学生顺序
     * @param $students
     * @author ZouYuChao
     * @time 2016-04-08 16:40
     */
    public function upset($students)
    {
        return $students->shuffle();
    }

    /**
     * 获得考生
     * @return mixed
     * @author ZouYuChao
     * @time 2016-04-08 11:12
     */
    public function getStudents()
    {
        return $this->_S;
    }

    /**
     * 获取等待区的学生
     * @return mixed
     * @author ZouYuChao
     * @time 2016-04-08 11：15
     */
    public function getWaitStudents()
    {
        return $this->_S_W;
    }

    /**
     * 获得实体
     * @return mixed
     * @author ZouYuChao
     * @time 2016-04-08 11:13
     */
    public function getEntity()
    {
        return $this->_E;
    }


    /**
     * @param $exam
     * @throws \Exception
     * @author ZouYuChao
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
        if (($endDt - $beginDt - $flowTime * 60) < 0) {
            throw new \Exception('开始时间为:' . ExamScreening::find($screen->id)->begin_dt . '的场次时间太短，当前方案不可行！');
        }

        //将考生放入侯考区
        $this->_S_W = $this->waitExamQueue();
        //获得考试实体的最大公约数
        $mixCommonDivisors = [];
        foreach ($this->_E as $item) {
            $mixCommonDivisors[] = $item->mins + config('osce.sys_param.mins');
        }

        $mixCommonDivisor = Common::mixCommonDivisor($mixCommonDivisors);
        $this->doorStatus = $this->stationCount; //将当前所需人数作为开关门的初始值
        //初始化数据
        $i = $beginDt;
        $step = $mixCommonDivisor * 60; //为考试实体考试时间的秒数

        $noEndPlanRecords = [];
        $planRecords = [];
        $endCount = 0;
        $planSerialRecords = [];
        $noEndPlanSerialRecords = [];
        //开始计时器
        while ($i <= $endDt) {
            //开门动作
            foreach ($this->_E as &$entity) {
                if ($this->doorStatus > 0) {
                    $tempBool = $this->newCheckStatus($entity, $noEndPlanRecords);
                } else {
                    $tempBool = true;
                }
                if ($tempBool) { //反之，则是关门状态
                    if (($entity->timer >= $entity->mins * 60 + config('osce.sys_param.mins') * 60)) {
                        $entity->timer = 0;
                        $tempValues = $this->getPlanRecord($entity, $noEndPlanRecords, true);
                        //将结束时间写在表内
                        foreach ($tempValues as $tempValue) {
//                            if (isset($tempValue['end_dt']) && ) {
//                                continue;
//                            }
                            $tempValue['end_dt'] = date('Y-m-d H:i:s', $i);
                            $planRecords[] = $tempValue;
                            if (!isset($planSerialRecords[$tempValue['serialnumber']])) {
                                $planSerialRecords[$tempValue['serialnumber']] = [];
                            }
                            $planSerialRecords[$tempValue['serialnumber']][] = $tempValue;
                            $this->doorStatus++;
                            $endCount ++;
                        }
                    } else {
                        $entity->timer += $step;
                    }
                }
            }
            //关门动作
            foreach ($this->_E as &$entity) {
                if ($this->doorStatus > 0) {
                    $tempBool = $this->newCheckStatus($entity, $noEndPlanRecords);
                } else {
                    $tempBool = true;
                }
                if (!$tempBool) {
                    //将总考池和侯考区考生打包进数组
                    $params = [
                        'total' => $this->_S,
                        'wait' => $this->_S_W,
                        'serialnumber' => $serialnumber,
                    ];
                    //将参数注入
                    $this->cate->setParams($params);

                    $students = $this->cate->needStudents($entity, $screen, $this->exam, $planSerialRecords, $noEndPlanSerialRecords);
                    $this->_S = $this->cate->getTotalStudent();
                    $this->_S_W = $this->cate->getWaitStudent();
                    if (count($students) == 0) {
                        continue;
                    }

//                    dump($students, date('Y-m-d H:i:s', $i), $entity->name);

                    //变更学生的状态(写记录)
                    $insertData = [];
                    foreach ($students as $student) {
                        $data = $this->mode->dataBuilder($this->exam, $screen, $student, $entity, $i);
                        $data['created_at'] = date('Y-m-d H:i:s');
                        $data['updated_at'] = date('Y-m-d H:i:s');

                        $insertData [] = $data;
                        if ($data['station_id']) {
                            if (!isset($noEndPlanRecords['stations'][$data['station_id']])) {
                                $noEndPlanRecords['stations'][$data['station_id']] = [];
                            }
                            $noEndPlanRecords['stations'][$data['station_id']][] = $data;
                        } else {
                            if (!isset($noEndPlanRecords['rooms'][$data['room_id']])) {
                                $noEndPlanRecords['rooms'][$data['room_id']] = [];
                            }
                            $noEndPlanRecords['rooms'][$data['room_id']][] = $data;
                        }
                        if (!isset($noEndPlanSerialRecords[$data['serialnumber']])) {
                            $noEndPlanSerialRecords[$data['serialnumber']] = [];
                        }
                        $noEndPlanSerialRecords[$data['serialnumber']][] = $data;
                    }

//                    if ($result = ExamPlanRecord::insert($insertData)) {
                    $this->doorStatus -= count($insertData);
                    $entity->timer += $step;
//                    } else {
//                        throw new \Exception('关门失败！', -11);
//                    }
                }
            }

            $i += $step;

            //TODO 排完后终止循环的操作，待施工
            if ($endCount == $this->_S_Count * $this->flowNum) {
                break;
            }
        }
        ExamPlanRecord::insert($planRecords);

        //获取未走完流程的考生
//        $studentList = $this->testingStudentList($this->exam, $screen, $this->flowNum);

        //未考完的学生实例数组
        $undoneStudents = [];
        foreach ($noEndPlanRecords as $types) {
            foreach ($types as $records) {
                foreach ($records as $record) {
                    $undoneStudents[] = $record['student_id'];
                }
            }
        }
        //获取候考区学生清单,并将未考完的考生还入总清单
        $this->_S = $this->_S->merge($this->_S_W);
        $this->_S = $this->_S->merge(array_unique($undoneStudents));
    }


    public function newCheckStatus($entity, $data)
    {
        $examPlanRecord = $this->getPlanRecord($entity, $data);
        //如果有，说明是关门状态
        if (!count($examPlanRecord)) {
            return false;  //开门状态
        } else {
            return true;   //关门状态
        }
    }

    /**
     * @param $entity
     * @param $data
     * @param bool $remove 获取完之后是否删除数据
     * @return array
     * @throws \Exception
     */
    public function getPlanRecord($entity, &$data, $remove = false)
    {
        if ($entity->type == 2) {
            if (isset($data['stations'][$entity->station_id])) {
                $records = $data['stations'][$entity->station_id];
                if ($remove) {
                    $data['stations'][$entity->station_id] = [];
                }
                return $records;
            } else {
                return [];
            }
        } elseif ($entity->type == 1) {
            if (isset($data['rooms'][$entity->room_id])) {
                $records = $data['rooms'][$entity->room_id];
                if ($remove) {
                    $data['rooms'][$entity->room_id] = [];
                }
                return $records;
            } else {
                return [];
            }
        } else {
            throw new \Exception('没有选定的考试模式！', -2);
        }
    }
}