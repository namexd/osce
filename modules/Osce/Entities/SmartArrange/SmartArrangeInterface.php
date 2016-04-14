<?php
/**
 * Created by PhpStorm.
 * User: j5110
 * Date: 2016/4/7
 * Time: 11:23
 */

namespace Modules\Osce\Entities\SmartArrange;


interface SmartArrangeInterface
{
    function plan($exam);
    
    function output($exam);
}