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

class Random extends AbstractCate implements CateInterface
{
    use SQLTraits;

    /**
     * 获取考试实体需要的学生
     * @param $entity
     * @param $screen
     * @param $exam
     * @param $params
     * @return array
     * @author Jiangzhiheng
     * @time 2016-04-11 10:18
     */
    function needStudents($entity, $screen, $exam)
    {
        // TODO: Implement needStudents() method.
        //拿到已经考过了的考生和正在考的考生
        $testStudents = $this->randomTestStudent($entity, $screen);
        //申明数组
        $result = [];
        /*
         * 获取当前实体需要几个考生 $station->needNum
         * 从正在考的学生里找到对应个数的考生
         * 如果该考生已经考过了这个流程，就忽略掉
         */

        $result = $this->studentNum($entity, $screen, $testStudents, $result);



        /*
         * 如果$result中保存的人数少于考站需要的人数，就从侯考区里面补上，并将这些人从侯考区踢掉
         * 再将人从学生池里抽人进入侯考区
         * 直接使用array_shift函数
         */
        if (count($result) < $entity->needNum) {
            for ($i = 0; $i <= $entity->needNum - count($result); $i++) {
                if (count($this->_S_W) > 0) {
                    $thisStudent = array_shift($this->_S_W);
                    if (!is_null($thisStudent)) {
                        $result[] = $thisStudent;
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
        return $result;
    }

    /**
     * 拿到已经考过了的考生和正在考的考生
     * @param $entity
     * @param $screen
     * @return array 已经考过了的考生及其考试流程
     * @throws \Exception
     * @author Jiangzhiheng
     * @time 2016-04-12 15:26
     */
    protected function randomTestStudent($entity, $screen)
    {
        $testingStudents = $this->randomBeginStudent($screen);

        $waitingStudents = $this->waitingStudentSql($screen);

        $arrays = [];
        foreach ($waitingStudents as $waitingStudent) {
            $arrays[] = $waitingStudent->student;
        }

        if (count($testingStudents) == 0) {
            $arrays = $this->beginStudents($entity);
        }

        return $this->testingStudents($this->exam, $screen, $arrays);
    }

    public function checkDoor()
    {
        return false;
    }
}