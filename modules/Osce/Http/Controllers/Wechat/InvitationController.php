<?php
/**
 * Created by PhpStorm.
 * User: j5110
 * Date: 2016/1/6
 * Time: 17:05
 */

namespace Modules\Osce\Http\Controllers\Wechat;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Modules\Osce\Entities\Exam;
use Modules\Osce\Entities\Invite;
use Modules\Osce\Entities\Teacher;
use Modules\Osce\Entities\ExamScreening;
use Modules\Osce\Entities\ExamScreeningStudent;
use Modules\Osce\Entities\Station;
use Modules\Osce\Http\Controllers\CommonController;
use DB;
use url;

class InvitationController extends CommonController
{

    /**
     * 已发布邀请列表
     * @api GET /osce/wechat/invitation/invitation-list
     * @access public
     *
     * @param Request $request post请求<br><br>
     * <b>post请求字段：</b>
     * * string        参数英文名        参数中文名(必须的)
     * @return view
     ** @version 1.0
     * @author zhouqiang <zhouqiang@misrobot.com>
     * @date
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */

    public function getInvitationList(Request $request)
    {
        //验证略
        $this->validate($request, [
            'teacher_id' => 'required|integer',
            'exam_id' => 'required|integer',
//            'station_id' => 'required|integer',
        ], [
            'teacher_id.required' => '邀请编号必须',
            'teacher_id.integer' => '邀请编号必须是数字',
        ]);
        $teacher_id = $request->get('teacher_id');
        $exam_id = $request->get('exam_id');
//        $station_id =   $request    -> get('station_id');
        $teacher = new Teacher();
        $data = $teacher->invitationContent($teacher_id);
//        dd($data);
        $ExamModel = new Exam();
        $ExamList = $ExamModel->where('id', $exam_id)->select('name', 'begin_dt', 'end_dt')->first()->toArray();
        $data['exam_name'] = $ExamList['name'];
        $data['begin_dt'] = $ExamList['begin_dt'];
        $data['end_dt'] = $ExamList['end_dt'];
        $data['exam_id'] = $exam_id;
        $InviteModel = new Invite();
        if ($InviteModel->addInvite($data)) {
//            dd(11111);
            return redirect()->route('osce.wechat.invitation.getList');
        } else {
            throw new \Exception('邀请创建失败');
        }


    }

    /**
     * 已发布邀请列表
     * @api GET /osce/wechat/invitation/list
     * @access public
     *
     * @param Request $request post请求<br><br>
     * <b>post请求字段：</b>
     * * string        参数英文名        参数中文名(必须的)
     * @return view
     ** @version 1.0
     * @author zhouqiang <zhouqiang@misrobot.com>
     * @date
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */

    public function getList()
    {
        dd('邀请已发送');
        $notice = new Invite();

        $list = $notice->get();

        return view('osce::admin.exammanage.sp_invitation',['list'=>$list]);//这里页面应该为列表页面
    }


    /**
     *sp邀请用户的还回结果
     * @api GET /osce/wechat/invitation/invitation-respond
     * @access public
     *
     * @param Request $request post请求<br><br>
     * <b>post请求字段：</b>
     * * string        id        教师id(必须的)
     * @return   ruset
     ** @version 1.0
     * @author zhouqiang <zhouqiang@misrobot.com>
     * @date
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */


    public function getInvitationRespond(Request $request, Teacher $teacher)
    {
//        dd(1111);
        $this->validate($request, [
            'status' => 'required|integer',
            'id' => 'required|integer'
        ]);
        $status = $request->get('status');
        $teacher_id = $request->get('status');

        $result = $teacher->where('id', '=', $teacher_id)->update('status', '=', 3-$status);

        if ($result) {

            throw new \Exception('操作成功');
        } else {
            throw new \Exception('操作失败');
        }
    }


    //邀请详情页面
    public function getMsg()
    {
        return view('osce::wechat.exammanage.sp_invitation_detail', [

            'id' => $id = urlencode(e(Input::get('id')))
        ]);
    }


}