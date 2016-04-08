<?php
/**
 * Created by PhpStorm.
 * User: j5110
 * Date: 2016/4/7
 * Time: 11:23
 */

namespace Modules\Osce\Entities\SmartArrange;


use Modules\Osce\Entities\Exam;

interface SmartArrangeInterface
{
    function plan();
    
    function output();
}