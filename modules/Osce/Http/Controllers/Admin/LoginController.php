<?php
/**
 * Created by PhpStorm.
 * User: fengyell <Luohaihua@misrobot.com>
 * Date: 2016/1/5
 * Time: 13:56
 */

namespace Modules\Osce\Http\Controllers\Admin;


use App\Entities\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Modules\Osce\Http\Controllers\CommonController;

class LoginController extends  CommonController
{
    public function getIndex(){
        return view('osce::admin.login');
    }
    public function postIndex(Request $request){
        $this   ->validate($request,[
            'username'  =>  'required',
            'password'  =>  'required',
        ]);
        $username   =   $request    ->  get('username');
        $password   =   $request    ->  get('password');
        $user=Auth::attempt(['username' => $username, 'password' => $password]);
        if ($user)
        {
            return redirect()->route('osce.admin.index');
        }
        else
        {
            
            return redirect()->back()->withErrors('账号密码错误');
        }
    }
}