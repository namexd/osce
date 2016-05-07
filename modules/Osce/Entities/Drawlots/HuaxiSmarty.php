<?php
/**
 * Created by PhpStorm.
 * User: j5110
 * Date: 2016/5/2
 * Time: 10:50
 */

namespace Modules\Osce\Entities\Drawlots;


use Modules\Osce\Entities\ExamQueue;
use Modules\Osce\Repositories\Common;

class HuaxiSmarty
{
    private $stationId;

    /**
     * 随机抽签给学生
     * @access public
     * @param $stations
     * @return mixed
     * @throws \Exception
     * @version 3.6
     * @author JiangZhiheng <JiangZhiheng@misrobot.com>
     * @time 2016-05-02
     * @copyright 2013-2016 MIS misrobot.com Inc. All Rights Reserved
     */
    public function ramdonId($stations)
    {
        if ($stations->isEmpty()) {
            throw new \Exception('当前数据有问题', -60);
        }

        return $this->stationId = $stations->random();
    }

    /**
     * 将信息写入exam_queue
     * @access public
     * @param $obj ExamQueue's object
     * @return mixed
     * @throws \Exception
     * @version 3.6
     * @author JiangZhiheng <JiangZhiheng@misrobot.com>
     * @time 2016-05-02
     * @copyright 2013-2016 MIS misrobot.com Inc. All Rights Reserved
     */
    public function writeExamQueue($obj)
    {
        $obj->station_id = $this->stationId;
        $obj->status = 1;
        return $obj->save();
    }

    /**
     * 拼装返回成功字符串
     * @access public
     * @param $name
     * @return string
     * @version 3.6
     * @author JiangZhiheng <JiangZhiheng@misrobot.com>
     * @time 2016-05-02
     * @copyright 2013-2016 MIS misrobot.com Inc. All Rights Reserved
     */
    public function assembly($name)
    {
        return ['抽签成功，请到' . $name . '考站进行考试'];
    }

    /**
     * 检查是否抽签
     * @access public
     * @param $studentId
     * @return mixed
     * @version
     * @author JiangZhiheng <JiangZhiheng@misrobot.com>
     * @time 2016-05-02
     * @copyright 2013-2016 MIS misrobot.com Inc. All Rights Reserved
     */
    public function isDraw($studentId)
    {
        return ExamQueue::whereStudentId($studentId)
            ->whereIn('status', [1, 2])
            ->orderBy('begin_dt', 'asc')
            ->first();
    }

    /**
     * 获取学生在数据库里的实例
     * @access public
     * @param $studentId
     * @param $screenId
     * @return mixed
     * @version
     * @author JiangZhiheng <JiangZhiheng@misrobot.com>
     * @time 2016-05-02
     * @copyright 2013-2016 MIS misrobot.com Inc. All Rights Reserved
     */
    public function getObj($studentId, $screenId)
    {
        //蒋同学，未见此处定义，如果是内置方法，请给我一个合理的解释
        return ExamQueue::whereStudentId($studentId)
            ->whereExamScreeningId($screenId)
            ->orderBy('begin_dt', 'asc')
            ->first();
    }
}