<?php
/**
 * Created by PhpStorm.
 * User: j5110
 * Date: 2016/5/2
 * Time: 10:17
 */

namespace Modules\Osce\Entities\Drawlots;


interface StudentInterface
{
    public function getStudent($examId, $nfc);
}