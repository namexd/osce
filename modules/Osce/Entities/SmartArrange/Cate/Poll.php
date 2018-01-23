<?php
/**
 * Created by PhpStorm.
 * User: j5110
 * Date: 2016/4/7
 * Time: 16:49
 */

namespace Modules\Osce\Entities\SmartArrange\Cate;


use Modules\Osce\Entities\ExamPlanRecord;
use Modules\Osce\Entities\SmartArrange\Traits\SQLTraits;
use Modules\Osce\Entities\SmartArrange\Traits\SundryTraits;
use Modules\Osce\Entities\Student;

class Poll extends AbstractCate implements CateInterface
{
    use SQLTraits, SundryTraits;

    private $students = null;

    private $serNum = 1;

    function needStudents($entity, $screen, $exam, $planSerialRecords = [], $noEndPlanSerialRecords = [])
    {
        // TODO: Implement needStudents() method.
        $testStudents = $this->pollTestStudents($entity, $planSerialRecords, $noEndPlanSerialRecords);


        /*
         * 获取当前实体需要几个考生 $station->needNum
         * 从正在考的学生里找到对应个数的考生
         * 如果该考生已经考过了这个流程，就忽略掉
         */
//        $result = $this->studentNum($entity, $screen,$testStudents, $result);


        /*
         * 如果$result中保存的人数少于考站需要的人数，就从侯考区里面补上，并将这些人从侯考区踢掉
         * 再将人从学生池里抽人进入侯考区
         * 直接使用array_shift函数
         */
        if (count($testStudents) < $entity->needNum) {
            if ($entity->min_serialnumber == true) {
                for ($i = 0; $i < 100; $i++) {
                    if (count($this->_S_W) > 0) {
                        $thisStudent = array_shift($this->_S_W);
                        if (!is_null($thisStudent)) {
                            $testStudents[] = $thisStudent;
                        }
                        if (!$this->_S->isEmpty()) {
                            $this->_S_W[] = $this->_S->shift();
                        }
                    }

                    if (count($testStudents) == $entity->needNum) {
                        break;
                    }
                }
            }
        }
        return $testStudents;
    }

    /**
     * 返回轮询所需要的学生
     * @param $entity
     * @return array
     * @throws \Exception
     * @author ZouYuChao
     * @time 2016-04-11 15:33
     */
    protected function pollTestStudents($entity, $planSerialRecords, $noEndPlanSerialRecords)
    {
        //声明变量
        $tempStudents = [];

        //如果当前的流程号不等于实体的流程号，就将学生属性置为空
        if ($this->serNum != $entity->serialnumber) {
            $this->students = null;
        }

        //如果学生属性置为空，那么就分配考生
        if (is_null($this->students)) {
            $this->students = $this->pollStudents($entity, count($this->serialnumber), $planSerialRecords, $noEndPlanSerialRecords);
        }

        //循环，找到合适的学生
        for ($i = 0; $i < 50; $i++) {
            //直接将学生踢出来
            $a = array_shift($this->students);
            if (!empty($a)) {
                $tempStudents[] = $a;
            }

            if (count($tempStudents) == $entity->needNum) {
                $this->serNum = $entity->serialnumber; //将serNum置为当前实体的
                return $tempStudents;
            }
        }

        //将serNum置为当前实体的
        $this->serNum = $entity->serialnumber;
        //如果没找到，就直接返回空数组
        return $tempStudents;
    }

}