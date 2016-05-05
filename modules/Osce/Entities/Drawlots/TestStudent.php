<?php
/**
 * Created by PhpStorm.
 * User: j5110
 * Date: 2016/5/3
 * Time: 19:51
 */

namespace Modules\Osce\Entities\Drawlots;


use Modules\Osce\Entities\ExamQueue;

class TestStudent implements StudentInterface
{
    public function getStudent($nfc)
    {
        return ExamQueue::whereStudentId(5560)->first();
    }
}