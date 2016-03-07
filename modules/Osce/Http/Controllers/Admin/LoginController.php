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
use Modules\Osce\Http\Controllers\CommonController;
use Auth;

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
        try{

            $user=Auth::attempt(['username' => $username, 'password' => $password]);
            if ($user)
            {
                return redirect()->route('osce.admin.index');
            }
            else
            {
                throw new \Exception('账号密码错误');
            }
        }catch (\Exception $ex){
            return redirect()->back()->withErrors($ex->getMessage());
        }

    }
}