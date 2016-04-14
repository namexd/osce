<?php
/**
 * Created by PhpStorm.
 * User: j5110
 * Date: 2016/4/8
 * Time: 9:56
 */

namespace Modules\Osce\Entities\SmartArrange;


class SmartArrangeRepository extends AbstractSmartArrange
{


    /**
     * 返回SmartArrange的类名
     * @return string
     * @author Jiangzhiheng
     * @time 2016-04-13 11:45
     */
    function model()
    {
        // TODO: Implement model() method.
        return 'Modules\Osce\Entities\SmartArrange\SmartArrange';
    }
}