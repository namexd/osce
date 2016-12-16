<?php
/**
 * Created by PhpStorm.
 * User: j5110
 * Date: 2016/5/3
 * Time: 10:25
 */

namespace Modules\Osce\Entities\Drawlots;

use Modules\Osce\Entities\ExamScreening;
class Screening
{
    /**
     * 获取当前的场次
     * @access public
     * @param $examId
     * @return mixed
     * @version
     * @author JiangZhiheng <JiangZhiheng@misrobot.com>
     * @time 2016-05-02
     * @copyright 2013-2017 sulida.com Inc. All Rights Reserved
     */
    public function screening($examId)
    {
        try {
            
            return ExamScreening::join('exam_order', 'exam_order.exam_screening_id', '=', 'exam_screening.id')
                ->where('exam_screening.exam_id', $examId)
                ->where('exam_screening.status', 1)//等候考试
                ->orderBy('exam_screening.begin_dt', 'asc')
                ->select(
                    'exam_screening.id as id'
                )
                ->first();

        } catch (\Exception $ex) {
            throw $ex;
        }
    }
}