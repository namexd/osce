<?php
/**
 * Created by PhpStorm.
 * User: j5110
 * Date: 2016/4/7
 * Time: 16:49
 */

namespace Modules\Osce\Entities\SmartArrange\Cate;


use Modules\Osce\Entities\SmartArrange\Traits\SQLTraits;
use Modules\Osce\Entities\SmartArrange\Traits\SundryTraits;

class Poll extends AbstractCate implements CateInterface
{
    use SQLTraits, SundryTraits;
    function needStudents($entity, $screen, $exam)
    {
        // TODO: Implement needStudents() method.
        $testStudnts = $this->pollTestStudents($entity, $screen);
//        dump($testStudnts);
        //申明数组
        $result = [];

        /*
         * 获取当前实体需要几个考生 $station->needNum
         * 从正在考的学生里找到对应个数的考生
         * 如果该考生已经考过了这个流程，就忽略掉
         */
        $result = $this->studentNum($entity, $testStudnts, $result);

        if (count($result) < $entity->needNum) {
            $hasStudent = $entity->needNum - count($result);
            for ($i = 0; $i <= $hasStudent; $i++) {
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
            return $result;
        }

        return $result;
    }
}