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
     * @author ZouYuChao
     * @time 2016-04-11 10:18
     */
    function needStudents($entity, $screen, $exam)
    {
        // TODO: Implement needStudents() method.
        //拿到已经考过了的考生和正在考的考生
        $testStudents = $this->randomTestStudent($entity, $screen);

        /*
         * 如果$result中保存的人数少于考站需要的人数，就从侯考区里面补上，并将这些人从侯考区踢掉
         * 再将人从学生池里抽人进入侯考区
         * 直接使用array_shift函数
         */
        if (count($testStudents) < $entity->needNum) {
            for ($i = 0; $i < 50; $i++) {
                if (count($this->_S_W) > 0) {
                    $thisStudent = array_shift($this->_S_W);
                    if (!is_null($thisStudent)) {
                        $testStudents[] = $thisStudent;
                    }
                    if (!$this->_S->isEmpty()) {
                        $this->_S_W[] = $this->_S->shift();
                    }

                    if (count($testStudents) == $entity->needNum) {
                        return $testStudents;
                    }
                }
            }
        }


        return $testStudents;
    }

    /**
     * 拿到已经考过了的考生和正在考的考生
     * @param $entity
     * @param $screen
     * @return array 已经考过了的考生及其考试流程
     * @throws \Exception
     * @author ZouYuChao
     * @time 2016-04-12 15:26
     */
    protected function randomTestStudent($entity, $screen)
    {
        //获取当前正在考试区的学生
        $testingStudents = $this->randomBeginStudent($screen, $entity->serialnumber);
        $testStudents = $this->thisNotSerial($screen, $entity->serialnumber);
        $temp = $testingStudents->diff($testStudents);

        /*
         * 获取本考试实体需要的考生
         * 也就是没有考过自己序号的考生
         */
        $tempStudents = $this->randomNeedStudent($entity, $temp);

        return $tempStudents;
    }
}