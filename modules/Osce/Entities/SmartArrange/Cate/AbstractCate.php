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

    protected $exam;

    protected $_S;

    protected $_S_W;

    protected $serialnumber;

    function __construct($params)
    {
        $this->_S = $params['total'];
        $this->_S_W = $params['wait'];
        $this->serialnumber = $params['serialnumber'];
        $this->exam = $params['exam'];
    }



    protected function beginStudents($entity) {
        /*
         * 将考站所需的考生直接返回
         * 将返回的考生从侯考区里干掉
         */
        $students = [];
        for ($i = 0; $i < $entity->needNum; $i++) {
            //将学生弹出
            $students[] = array_shift($this->_S_W);
            $a = $this->_S->shift();
            //将考生从考生池弹进侯考区
            if (is_null($a)) {
                continue;
            }
            $this->_S_W[] = $a;
        }

        return $students;
    }

    protected function testingStudents($exam, $screen, $testStudents) {
        $tempTestStudent = [];
        foreach ($testStudents as $key => $student) {
            if (is_null($student)) {
                continue;
            }

            //获取该考生已经考过的流程
            $studentSerialnumber = $this->getStudentSerialnumber($exam, $screen, $student);

            if (is_array($testStudents)) {
                $tempStudent = array_pull($testStudents, $key);
            } else {
                $tempStudent = $testStudents->pull($key);
            }

            if (is_null($tempStudent)) {
                continue;
            }

            if (count($this->serialnumber) != count($studentSerialnumber)) {
                $tempTestStudent[] = $tempStudent;
            }
        }
        return $tempTestStudent;
    }

    protected function studentNum($entity, $screen, $testStudents, $result)
    {

        foreach ($testStudents as $testStudent) {
            if (is_object($testStudent)) {
                $serialnumber = $this->getStudentSerialnumber($this->exam, $screen, $testStudent);

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
        return $result;
    }

    /**
     * 返回总的学生集合
     * @return object
     * @author Jiangzhiheng
     * @time 2016-04-12 15:15
     */
    public function getTotalStudent()
    {
        return $this->_S;
    }

    /**
     * 返回侯考区的学生集合
     * @return object
     * @author Jiangzhiheng
     * @time 2016-04-12 15:15
     */
    public function getWaitStudent()
    {
        return $this->_S_W;
    }
    
}