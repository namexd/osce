<?php
/**
 * Created by PhpStorm.
 * User: j5110
 * Date: 2016/5/4
 * Time: 19:50
 */

namespace Modules\Osce\Http\Controllers\Billboard;


use Illuminate\Http\Request;
use Modules\Osce\Http\Controllers\CommonController;

class BillboardController extends CommonController
{
    public function getIndex(Request $request)
    {
        return view('osce::admin.billboard.index', ['exam_id' => $request->input('exam_id')]);
    }
}