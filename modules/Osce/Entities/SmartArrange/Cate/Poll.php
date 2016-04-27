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

    function needStudents($entity, $screen, $exam)
    {
        // TODO: Implement needStudents() method.
        $testStudents = $this->pollTestStudents($entity, $screen);
        //申明数组
//        $result = [];

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
                for ($i = count($testStudents); $i < $entity->needNum; $i++) {
                    if (count($this->_S_W) > 0) {
                        $thisStudent = array_shift($this->_S_W);
                        if (!is_null($thisStudent)) {
                            $testStudents[] = $thisStudent;
                        }
                        if (count($this->_S) > 0) {
                            if (is_array($this->_S)) {
                                $this->_S_W[] = array_shift($this->_S);
                            } else {
                                $this->_S_W[] = $this->_S->shift();
                            }
                        }
                    }
                }
            }
        }

        return $testStudents;
    }

    /**
     * 返回轮询所需要的学生
     * @param $entity
     * @param $screen
     * @return array
     * @throws \Exception
     * @author Jiangzhiheng
     * @time 2016-04-11 15:33
     */
    protected function pollTestStudents($entity, $screen)
    {
//        $tempArrays = $this->pollBeginStudent($entity, $screen);
//
//        $num = $this->waitingPollStudentSql($screen, $entity);
//
//        $arrays = [];
//        foreach ($num as $item) {
//            $arrays[] = $item->student;
//        }
//
//        if (count($tempArrays) == 0) {
//            $arrays = $this->beginStudents($entity);
//        }
        //声明两个变量
        $arrays = [];
        $tempStudents = [];
//        $tempStudents = $this->prevSerial($screen, $entity->serialnumber);
//
//        $thisStudents = $this->thisSerial($screen, $entity->serialnumber);

        //如果当前的流程号不等于实体的流程号，就将学生属性置为空
        if ($this->serNum != $entity->serialnumber) {
            $this->students = null;
        }

        //如果学生属性置为空，那么就分配考生
        if (is_null($this->students)) {
            $this->students = $this->pollStudents($screen, $entity, count($this->serialnumber));
        }

        //循环，找到合适的学生
        for ($i = 0; $i < 100; $i++) {
            //直接将学生踢出来
            $a = array_shift($this->students);
            if (!is_null($a)) {
                $tempStudents[] = $a;
            }

            if (count($tempStudents) == $entity->needNum) {
                break;
            }
        }

        //如果有数据，就将查找到的id转为学生实体
        if (count($tempStudents) != 0) {
            foreach ($tempStudents as $tempStudent) {
                $arrays[] = Student::find($tempStudent);
                if (count($arrays) == $entity->needNum) {
                    $this->serNum = $entity->serialnumber; //将serNum置为当前实体的
                    return $arrays;
                }
            }
        }

        //将serNum置为当前实体的
        $this->serNum = $entity->serialnumber;
        //如果没找到，就直接返回空数组
        return $arrays;

//        return $this->testingStudents($this->exam, $screen, $arrays);
    }
}