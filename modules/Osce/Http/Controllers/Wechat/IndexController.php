<?php
/**
 * Created by PhpStorm.
 * User: fengyell <Zouyuchao@sulida.com>
 * Date: 2016/1/9
 * Time: 17:14
 */

namespace Modules\Osce\Http\Controllers\Wechat;

use Modules\Osce\Http\Controllers\CommonController;
use Illuminate\Support\Facades\Auth;

class IndexController extends CommonController
{

    public function getIndex(){
        $user = Auth::user();
        if (empty($user)) {
            return  redirect()   ->route('osce.wechat.user.getLogin');
            //throw new \Exception('未找到当前操作人信息');
        }
        return view('osce::wechat.index.index');
    }
}