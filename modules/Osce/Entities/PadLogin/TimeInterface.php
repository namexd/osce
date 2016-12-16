<?php
/**
 * Created by PhpStorm.
 * User: j5110
 * Date: 2016/5/3
 * Time: 15:43
 */

namespace Modules\Osce\Entities\PadLogin;


interface TimeInterface
{
    /**
     * 服务器今天的开始时间
     * @access public
     * @param $currentTime
     * @return mixed
     * @version 3.6
     * @author JiangZhiheng <JiangZhiheng@misrobot.com>
     * @time 2016-05-02
     * @copyright 2013-2017 sulida.com Inc. All Rights Reserved
     */
    public function beginTime();

    /**
     * 服务器今天的结束时间
     * @access public
     * @param $currentTime
     * @return mixed
     * @version
     * @author JiangZhiheng <JiangZhiheng@misrobot.com>
     * @time 2016-05-02
     * @copyright 2013-2017 sulida.com Inc. All Rights Reserved
     */
    public function endTime();
}