<?php
/**
 * Created by PhpStorm.
 * User: j5110
 * Date: 2016/5/4
 * Time: 21:37
 */

namespace Modules\Osce\Entities\Billboard;


use Modules\Osce\Entities\Student;
use Modules\Osce\Repositories\Common;

class BillboardRepository
{
    private $billboard;

    public function __construct(Billboard $billboard)
    {
        $this->billboard = $billboard;
    }

    /**
     * 获取数据
     * @access public
     * @param $examId
     * @return mixed
     * @throws \Exception
     * @version
     * @author ZouYuChao <ZouYuChao@sulida.com>
     * @time 2016-05-04
     * @copyright 2013-2017 sulida.com Inc. All Rights Reserved
     */
    public function getData($examId)
    {
        $userId = \Auth::id();
        Common::valueIsNull($userId, -1, '当前操作者没有登陆');

        //利用老师id和考试id找出数据
        $data = $this->billboard->getData($examId, $userId);
        
        return $data;
    }

    /**
     * 获取考生数据
     * @access public
     * @param $examId
     * @param $stationId
     * @请求字段：
     * @return mixed
     * @version
     * @author ZouYuChao <ZouYuChao@sulida.com>
     * @time 2016-05-05
     * @copyright 2013-2017 sulida.com Inc. All Rights Reserved
     */
    public function getStudent($examId, $roomId)
    {
        return $this->billboard->get2Queue($examId, $roomId);
    }
    

    /**
     *获取下一考场
     * @access public
     * @param $examId
     * @param $stationId
     * @请求字段：
     * @return mixed
     * @version
     * @author ZouYuChao <ZouYuChao@sulida.com>
     * @time 2016-05-05
     * @copyright 2013-2017 sulida.com Inc. All Rights Reserved
     */
    public  function  getRoomData($examId,$studentId)
    {
        return $this->billboard->getNextRoom($examId,$studentId);
    }
}