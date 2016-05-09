<?php
/**
 * Created by PhpStorm.
 * User: CoffeeKizoku
 * Date: 2016/5/9
 * Time: 22:52
 */

namespace Modules\Osce\Entities\SmartArrange\Export;


use Modules\Osce\Entities\ExamPlan;

class DengStudent
{
    private $examPlan;

    public function __construct(ExamPlan $examPlan)
    {
        $this->examPlan = $examPlan;
    }

    public function getData($id)
    {
        return $this->examPlan
            ->screening()
            ->students()
            ->exam($id)
            ->select(
                'student.name',
                'student.code',
                'exam_screening.begin_dt',
                'exam_plan.student_id'
            )
            ->orderBy('exam_screening.begin_dt', 'asc')
            ->distinct()
            ->get()
            ->groupBy('begin_dt');
    }
}