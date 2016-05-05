<?php
/**
 * Created by PhpStorm.
 * User: j5110
 * Date: 2016/5/1
 * Time: 10:10
 */

namespace Modules\Osce\Entities\SmartArrange\Export;


use Modules\Osce\Entities\ExamPlan;
use Modules\Osce\Entities\SmartArrange\Traits\CheckTraits;

class StudentArrange
{
    use CheckTraits;
    private $examPlan = null;

    public function __construct(ExamPlan $examPlan)
    {
        $this->examPlan = $examPlan;
    }

    /**
     * 获取学生的数据
     * @param $id
     * @author Jiangzhiheng
     * @time 2016-05-01 10:11
     */
    public function getData($id)
    {
        return $this->examPlan
            ->students()
            ->exam($id)
            ->select(
                'student.name',
                'student.mobile',
                'student.code',
                'student.grade_class',
                'exam_plan.begin_dt',
                'exam_plan.end_dt',
                'exam_plan.student_id'
            )
            ->orderBy('student.code', 'asc')
            ->get()
            ->groupBy('student_id');
    }
}