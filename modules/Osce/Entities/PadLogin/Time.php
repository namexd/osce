<?php
/**
 * Created by PhpStorm.
 * User: j5110
 * Date: 2016/5/3
 * Time: 15:34
 */

namespace Modules\Osce\Entities\PadLogin;


use Carbon\Carbon;

class Time implements TimeInterface
{
    /**
     * 某一天的开始时间
     * @access public
     * @param $currentTime
     * @return mixed
     * @version 3.6
     * @author JiangZhiheng <JiangZhiheng@misrobot.com>
     * @time 2016-05-02
     * @copyright 2013-2016 MIS misrobot.com Inc. All Rights Reserved
     */
    public function beginTime()
    {
//        $temp = date('Y-m-d', $currentTime);
//        return date('Y-m-d H:i:s', strtotime($temp));
        return Carbon::today()->toDateTimeString();
    }

    /**
     * 某一天的结束时间
     * @access public
     * @param $currentTime
     * @version
     * @author JiangZhiheng <JiangZhiheng@misrobot.com>
     * @time 2016-05-02
     * @copyright 2013-2016 MIS misrobot.com Inc. All Rights Reserved
     */
    public function endTime()
    {
//        $temp = date('Y-m-d', $currentTime + 86400);
//        return date('Y-m-d H:i:s', strtotime($temp) - 1);
        return Carbon::tomorrow()->subSecond()->toDateTimeString();
    }
}