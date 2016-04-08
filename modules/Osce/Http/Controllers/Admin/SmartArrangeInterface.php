<?php
/**
 * Created by PhpStorm.
 * User: j5110
 * Date: 2016/4/7
 * Time: 11:18
 */

namespace Modules\Osce\Http\Controllers\Admin;


use Illuminate\Http\Request;
use Modules\Osce\Entities\ExamPlan;

interface SmartArrangeInterface
{
    function getIndex(Request $request);

    function postBegin(Request $request);

    function postStore(Request $request, ExamPlan $examPlan);
}