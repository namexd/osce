<?php
/**
 * Created by PhpStorm.
 * User: j5110
 * Date: 2016/4/28
 * Time: 9:27
 */

namespace Modules\Osce\Entities\SmartArrange\Student;



use Modules\Osce\Entities\Student;

class StudentFromDB implements StudentInterface
{
    public function get($exam)
    {
        return Student::where('exam_id', $exam->id)->lists('id');
    }
}