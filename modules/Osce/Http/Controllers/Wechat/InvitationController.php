<?php
/**
 * Created by PhpStorm.
 * User: j5110
 * Date: 2016/1/6
 * Time: 17:05
 */

namespace Modules\Osce\Http\Controllers\Wechat;


use Illuminate\Http\Request;
use Modules\Osce\Entities\ExamScreening;
use Modules\Osce\Entities\ExamScreeningStudent;
use Modules\Osce\Http\Controllers\CommonController;

class InvitationController extends CommonController
{
    public function getInvitation(Request $request)
    {
        //验证略
        $this->validate($request,[
            'id' => 'required|integer'
        ],[
            'id.required'   =>  '邀请编号必须',
            'id.integer'    =>  '邀请编号必须是数字',
        ]);

        $id =   $request    -> get('id');


        //获取要发送给sp老师的数据

    }
}