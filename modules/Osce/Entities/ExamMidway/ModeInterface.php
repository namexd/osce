<?php
/**
 * Created by PhpStorm.
 * User: j5110
 * Date: 2016/3/29
 * Time: 9:34
 */

namespace Modules\Osce\Entities\ExamMidway;


interface ModeInterface
{
    function getFlow();

    function getExaminee(array $serialnumber);

    function getNextExaminee(array $serialnumber);
}