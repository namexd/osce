<?php
/**
 * Created by PhpStorm.
 * User: j5110
 * Date: 2016/5/2
 * Time: 15:46
 */

namespace Modules\Osce\Entities\Drawlots;


interface DrawValidatorInterface
{
    public function validate($studentId, $screenId, $roomId, $examId);
}