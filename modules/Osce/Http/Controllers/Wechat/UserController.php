<?php
/**
 * Created by PhpStorm.
 * User: fengyell <Luohaihua@misrobot.com>
 * Date: 2016/1/9
 * Time: 13:15
 */

namespace Modules\Osce\Http\Controllers\Wechat;

use Illuminate\Http\Request;
use Modules\Osce\Http\Controllers\CommonController;

class UserController  extends CommonController
{

    public function getRegister(){
        //return view();
    }

    public function postRegister(Request $request){
        $data   =   [
            'username'  =>  'ceshi',
            'name'      =>  'ceshi',
            'mobile'    =>  'ceshi',
            'password'  =>  'ceshi',
            'nickname'  =>  'ceshi',
            'gender'    =>  'ceshi',
            'idcard'    =>  'ceshi',
        ];


    }
    public function getLogin(){

    }

    public function postLogin(){

    }
}