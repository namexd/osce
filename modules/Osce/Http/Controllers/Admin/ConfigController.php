<?php
/**
 * Created by PhpStorm.
 * User: j5110
 * Date: 2016/1/8
 * Time: 18:48
 */

namespace modules\Osce\Http\Controllers\Admin;


use Illuminate\Http\Request;
use Modules\Osce\Http\Controllers\CommonController;
use File;

class ConfigController extends CommonController
{
    public function index()
    {
        return view('');
    }

    public function store(Request $request)
    {

        $array = [
            'SHARE' => 'weixin',
            'SMS_GATE' => '1111',
            'SMS_GATE_IP' => '1111',
            'SETTING' => '222222'
        ];

        $tempString = '';

        foreach ($array as $key=> $item) {
            $tempString .= $key . '=>' . $item . ',' . "\n";
        }
        File::append()
    }
}