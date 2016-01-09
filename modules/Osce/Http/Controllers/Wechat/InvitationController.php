<?php
/**
 * Created by PhpStorm.
 * User: j5110
 * Date: 2016/1/6
 * Time: 17:05
 */

namespace Modules\Osce\Http\Controllers\Wechat;


use Illuminate\Http\Request;
use Modules\Msc\Entities\Teacher;
use Modules\Osce\Entities\ExamScreening;
use Modules\Osce\Entities\ExamScreeningStudent;
use Modules\Osce\Entities\Station;
use Modules\Osce\Http\Controllers\CommonController;

class InvitationController extends CommonController
{


    //获取要发送给sp老师的数据
    public function getInvitation(Request $request)
    {
        //验证略
        $this->validate($request,[
            'tid' => 'required|integer',
            'sid' => 'required|integer'
        ],[
            'tid.required'   =>  '邀请编号必须',
            'tid.integer'    =>  '邀请编号必须是数字',

        ]);

        $teacher_id =   $request    -> get('tid');
        $station_id =   $request    -> get('sid');

        $TeacherList= Teacher::find($teacher_id)->get();
        $StationList = Station::find($station_id)->get();

          $Invitationdata= [



          ];




    }
}