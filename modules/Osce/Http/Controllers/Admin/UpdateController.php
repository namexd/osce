<?php
/**
 * Created by PhpStorm.
 * User: gaodapeng
 * Date: 2016/5/6 
 * Time: 9:22
 */
namespace Modules\Osce\Http\Controllers\Admin;

use App\Entities\User;
use Modules\Osce\Http\Controllers\CommonController;
use Illuminate\Http\Request;
use Modules\Osce\Repositories\Common;

class UpdateController extends CommonController{

    public function getIndex(){
        return view('osce::admin.update');
    }

    public function postIndex(Request $request){

        $this->validate($request,[
            'username'  =>  'required',
            'password'  =>  'required',
        ]);
        $username   =   trim($request    ->  get('username'));
        $password   =   trim($request    ->  get('password'));

        try{
            $userData = User::whereUsername($username)->first();
            Common::valueIsNull($userData, -1, '当前手机号没有被注册');
            $userData->password = bcrypt($password);
            if (!$userData->save()) {
                throw new \Exception('保存密码失败！');
            }
            return "修改密码成功";
//                $update = ['password' => bcrypt($password)];
//                $result = User::whereUsername($username)->updated($update);
//                if (!$result) {
//                    throw new \Exception('保存密码失败！');
//                }
            }
        catch (\Exception $ex){
           return redirect()->back()->withErrors($ex->getMessage());
        }
    }
}
