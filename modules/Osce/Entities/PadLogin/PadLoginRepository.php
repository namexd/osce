<?php
/**
 * Created by PhpStorm.
 * User: j5110
 * Date: 2016/5/3
 * Time: 13:56
 */

namespace Modules\Osce\Entities\PadLogin;


class PadLoginRepository
{
    private $padLogin = null;

    public function __construct(PadLogin $padLogin)
    {
        $this->padLogin = $padLogin;
    }

    /**
     * 获取按时间排序的当前考试列表
     * @access public
     * @param TimeInterface $time
     * @return mixed
     * @version 3.6
     * @author JiangZhiheng <JiangZhiheng@misrobot.com>
     * @time 2016-05-03
     * @copyright 2013-2017 sulida.com Inc. All Rights Reserved
     */
    public function examList(TimeInterface $time)
    {
        //获取服务器今天的开始时间
        $beginTime = $time->beginTime();


        //获取服务器今天的几首时间
        $endTime = $time->endTime();

//        //获取集合
//        $collection = $this->padLogin->screenBegin($beginTime, $endTime);
//
//        //去重
//        $collection = $this->padLogin->wipeEqual($collection);
        
        //处理数据，去掉时间，只保留id和name
        return $this->cleanFields($this->padLogin->screenBegin($beginTime, $endTime)->unique('exam_id'));
    }

    /**
     * 返回清除了多余字段的对象
     * @access public
     * @param $time
     * @param array $fileds
     * @return mixed
     * @version 3.6
     * @author JiangZhiheng <JiangZhiheng@misrobot.com>
     * @time 2016-05-03
     * @copyright 2013-2017 sulida.com Inc. All Rights Reserved
     */
    public function cleanFields($collection, array $fields = ['begin_dt', 'end_dt'])
    {
        foreach ($collection as &$item) {
            foreach ($fields as $filed) {
                unset($item->$filed);
            }
        }

        return $collection->toArray();
    }

    /**
     * 返回需要获取的字段的数组
     * @access public
     * @param $collection
     * @param array $fields
     * @return array
     * @version 3.6
     * @author JiangZhiheng <JiangZhiheng@misrobot.com>
     * @time 2016-05-02
     * @copyright 2013-2017 sulida.com Inc. All Rights Reserved
     */
    public function getFields($collection, array $fields = [])
    {

        $array = [];
        foreach ($collection as $key => $item) {
            foreach ($fields as $field) {
                $array[$key][$field] = $item->$field;
            }
        }
        return $array;
    }

    /**
     * 返回考场的实例或者是id
     * @access public
     * @param $examId
     * @param bool $status
     * @return mixed
     * @version 3.6
     * @author JiangZhiheng <JiangZhiheng@misrobot.com>
     * @time 2016-05-02
     * @copyright 2013-2017 sulida.com Inc. All Rights Reserved
     */
    public function roomList($examId, $status = false)
    {
        $room = [];
        if ($status) {
            return $this->padLogin->roomList($examId);
        } else {
            foreach ($this->padLogin->roomList($examId) as $item) {
                $room[] = $item->room;
            }
            
            return $this->getFields(collect($room), ['id', 'name']);
        }
    }
}