<?php
/**
 * Created by PhpStorm.
 * User: fengyell <Luohaihua@misrobot.com>
 * Date: 2015/11/19
 * Time: 16:02
 */

namespace App\Http\Controllers;

use App\Http\Controllers\Controller as BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Extensions\OAuth\PasswordGrantVerifier;
class IndexController extends BaseController
{
    public function index(){
        $user = Auth::user();
        if(empty($user->id)){
            //TODO: zhoufuxiang 2016-3-15
            return view('index');
//            return redirect()->intended('/admin/login');
        }else{
            //TODO: zhoufuxiang 2016-3-15
            return redirect()->intended('/osce/admin/index');
//            return view('index');
        }
    }
    public function login(){

        return view('login');
    }
    //��¼����
    public function loginOp(Request $request,PasswordGrantVerifier $passwordGrantVerifier){
        $requests = $request->all();
        $rew = $passwordGrantVerifier->verify($requests['username'],$requests['password']);
        if($rew){
            return redirect()->intended('/admin/index');
        }else{
            return view('login');
        }
    }
}