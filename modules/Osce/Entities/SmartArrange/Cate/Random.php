<?php
/**
 * Created by PhpStorm.
 * User: j5110
 * Date: 2016/4/7
 * Time: 16:52
 */

namespace Modules\Osce\Entities\SmartArrange\Cate;


use Modules\Osce\Entities\SmartArrange\Post;
use Modules\Osce\Entities\SmartArrange\Traits\SQLTraits;
use Modules\Osce\Entities\SmartArrange\Traits\SundryTraits;

class Random implements CateInterface
{
    use SQLTraits;

    function needStudents($entity, $screen, $exam, $wait)
    {
        // TODO: Implement needStudents() method.
        $testStudent = $this->randomTestStudent($entity, $screen, $wait);
    }

    private function randomTestStudent($entity, $screen, $wait)
    {
        $testingStudents = $this->randomBeginStudent($screen);
        $waitingStudents = $this->waitingStudentSql($screen);

        $arrays = [];
        foreach ($waitingStudents as $waitingStudent) {
            $arrays[] = $waitingStudent->student;
        }

        if (count($testingStudents) == 0) {
            $arrays = $this->beginStudents($entity, $wait);
        }

        return $this->testingStudents($arrays);
    }

    private function beginStudents($entity, $wait) {
        /*
         * 将考站所需的考生直接返回
         * 将返回的考生从侯考区里干掉
         */
        $students = [];
        for ($i = 0; $i < $entity->needNum; $i++) {
            //将学生弹出
            $students[] = array_shift($wait);
            $a = $wait->shift();
            //将考生从考生池弹进侯考区
            if (is_null($a)) {
                continue;
            }
            $post->getWait();
        }
        
        $post->setNeedStudents($students);
        return $post;
    }
    
    private function testingStudents($arrays) {
        return
    }
}