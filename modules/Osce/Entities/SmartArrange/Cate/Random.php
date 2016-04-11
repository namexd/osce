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
    function needStudents($entity, $screen, $exam, $params)
    {
        // TODO: Implement needStudents() method.
        $testStudents = $this->randomTestStudent($entity, $screen, $params);
        //申明数组
        $result = [];
        /*
         * 获取当前实体需要几个考生 $station->needNum
         * 从正在考的学生里找到对应个数的考生
         * 如果该考生已经考过了这个流程，就忽略掉
         */
        list($result, $params) = $this->studentNum($entity, $testStudents, $result);

        /*
         * 如果$result中保存的人数少于考站需要的人数，就从侯考区里面补上，并将这些人从侯考区踢掉
         * 再将人从学生池里抽人进入侯考区
         * 直接使用array_shift函数
         */
        if (count($result) < $entity->needNum) {
            for ($i = 0; $i <= $entity->needNum - count($result); $i++) {
                if (count($params['wait']) > 0) {
                    $thisStudent = array_shift($params['wait']);
                    if (!is_null($thisStudent)) {
                        $result[] = $thisStudent;
                    }
                    if (count($params['total']) > 0) {
                        if (is_array($params['total'])) {
                            $params['wait'][] = array_shift($params['total']);
                        } else {
                            $params['wait'][] = $params['total']->shift();
                        }

                    }
                }
            }
        }
        return [$result, $params];
    }

    
}