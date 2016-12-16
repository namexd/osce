<?php
/**
 * Created by PhpStorm.
 * User: j5110
 * Date: 2016/5/3
 * Time: 10:54
 */

namespace Modules\Osce\Entities\Drawlots\Validator;


trait CheckTraits
{
    /**
     * 判断对象的某个字段是否为指定值
     * @access public
     * @param $obj
     * @param int $num
     * @throws \Exception
     * @version 3.6
     * @author ZouYuChao <ZouYuChao@sulida.com>
     * @time 2016-05-03
     * @copyright 2013-2017 sulida.com Inc. All Rights Reserved
     */
    public function fieldValidator($obj, $num = 0, $field = 'status')
    {
        if ($obj->$field != $num) {
            throw new \Exception('当前学生的抽签结果有误', -20);
        }
    }
}