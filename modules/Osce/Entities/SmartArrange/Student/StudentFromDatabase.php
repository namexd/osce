<?php
/**
 * Created by PhpStorm.
 * User: j5110
 * Date: 2016/4/7
 * Time: 19:26
 */

namespace Modules\Osce\Entities\SmartArrange\Student;


use Modules\Osce\Entities\Student;

class StudentFromDatabase implements StudentInterface
{
    function get($exam)
    {
        // TODO: Implement get() method.
        return Student::where('exam_id', $exam->id)
            ->get();
    }
}