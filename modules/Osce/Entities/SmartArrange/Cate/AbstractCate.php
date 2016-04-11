<?php
/**
 * Created by PhpStorm.
 * User: j5110
 * Date: 2016/4/11
 * Time: 15:20
 */

namespace Modules\Osce\Entities\SmartArrange\Cate;


use Modules\Osce\Entities\SmartArrange\Traits\SQLTraits;
use Modules\Osce\Entities\SmartArrange\Traits\SundryTraits;
use Modules\Osce\Entities\Student;

abstract class AbstractCate
{
    use SQLTraits, SundryTraits;
    protected function randomTestStudent($entity, $screen, $params)
    {
        $testingStudents = $this->randomBeginStudent($screen);

        $waitingStudents = $this->waitingStudentSql($screen);

        $arrays = [];
        foreach ($waitingStudents as $waitingStudent) {
            $arrays = $waitingStudent->student;
        }

        if (count($testingStudents) == 0) {
            list($arrays, $params) = $this->beginStudents($entity, $params);
        }

        return $this->testingStudents($arrays, $params);
    }

    protected function beginStudents($entity, $params) {
        /*
         * 将考站所需的考生直接返回
         * 将返回的考生从侯考区里干掉
         */
        $students = [];
        for ($i = 0; $i < $entity->needNum; $i++) {
            //将学生弹出
            $students[] = array_shift($params['wait']);
            $a = $params['total']->shift();
            //将考生从考生池弹进侯考区
            if (is_null($a)) {
                continue;
            }
            $params['wait'][] = $a;
        }

        return [$students, $params];
    }

    protected function testingStudents($testStudents, $params = []) {
        $tempTestStudent = [];

        foreach ($testStudents as $key => $student) {
            if (is_null($student)) {
                continue;
            }

            //获取该考生已经考过的流程
            $studentSerialnumber = $this->getStudentSerialnumber($student);

            if (is_array($testStudents)) {
                $tempStudent = array_pull($testStudents, $key);
            } else {
                $tempStudent = $testStudents->pull($key);
            }

            if (is_null($tempStudent)) {
                continue;
            }

            if (count($params['serialnumber']) != count($studentSerialnumber)) {
                $tempTestStudent[] = $tempStudent;
            }
        }
        return [$tempTestStudent, $params];
    }

    protected function studentNum($entity, $testStudents, $result)
    {
        foreach ($testStudents as $testStudent) {
            if (is_object($testStudent)) {
                $serialnumber = $this->getStudentSerialnumber($testStudent);

                if (in_array($entity->serialnumber, $serialnumber->toArray())) {
                    continue;
                }


            }

            $result[] = $testStudent;

            //如果考生的人数等于考试实体需要的人数，就打断循环，输出这个值
            if (count($result) == $entity->needNum) {
                break;
            }
        }
        return [$result, $testStudents[1]];
    }

    /**
     * 寻找循序模式需要的考生
     * @param $entity
     * @param $screen
     * @return array
     * @throws \Exception
     * @author Jiangzhiheng
     * @time 2016-04-11 11:30
     */
    protected function orderTestStudent($entity, $screen)
    {
        /**
         * 需要查当前的实例是不是第一个
         * 如果等于1，就说明是第一个，直接从侯考区取人
         * 如果不是，就说明前面有流程了
         */
        if ($entity->serialnumber != 1) {
            $tempArrays = $this->orderBeginStudent($screen, $entity->serialnumber);
            if (count($tempArrays) != 0) {
                return Student::whereIn('id', $tempArrays)->get();
            } else {
                return collect([]);
            }
        } else {
            return collect([]);
        }
    }

    /**
     * 返回轮询所需要的学生
     * @param $entity
     * @param $screen
     * @param $params
     * @return array
     * @throws \Exception
     * @author Jiangzhiheng
     * @time 2016-04-11 15:33
     */
    protected function pollTestStudents($entity, $screen, $params)
    {
        $tempArrays = $this->pollBeginStudent($entity, $screen);

        $num = $this->waitingStudentSql($screen);

        $arrays = [];
        foreach ($num as $item) {
            $arrays[] = $item->student;
        }

        if (count($tempArrays) == 0) {
            list($arrays, $params) = $this->beginStudents($screen, $params);
        }

        return $this->testingStudents($arrays, $params);
    }
    
}