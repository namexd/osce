<?php
/**
 * Created by PhpStorm.
 * User: fengyell <Zouyuchao@sulida.com>
 * Date: 2016/1/9
 * Time: 17:14
 */

namespace Modules\Osce\Http\Controllers\Wechat;

use Modules\Osce\Http\Controllers\CommonController;

class IndexController extends CommonController
{

    public function getIndex(){
        return view('osce::wechat.index.index');
    }
}