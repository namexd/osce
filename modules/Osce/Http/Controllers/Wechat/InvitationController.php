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
use Modules\Osce\Entities\CaseModel;
use Modules\Osce\Entities\Exam;
use Modules\Osce\Entities\ExamSpTeacher;
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
     *sp邀请
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
//        dd($ExamList);
            foreach($data as $k=>$v){
                $data[$k]['exam_name'] = $ExamList['name'];
                $data[$k]['begin_dt'] = $ExamList['begin_dt'];
                $data[$k]['end_dt'] = $ExamList['end_dt'];
                $data[$k]['exam_id'] = $exam_id;
            }

//        dd($data);
        $InviteModel = new Invite();
        if ($InviteModel->addInvite($data)) {
//            dd(11111);
            return view('osce::admin.exammanage.examroom_assignment');
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
        $notice = new Invite();

        $list = $notice->get();

        return view('osce::admin.exammanage.sp_invitation',['list'=>$list]);//这里页面应该为列表页面
    }


    /**
     *sp邀请用户的同意与拒绝操作
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
        $teacher_id = $request->get('id');

        $result = $teacher->where('id', '=', $teacher_id)->where('type','=',2)->update('status', '=', 4-$status);

        if ($result) {
            throw new \Exception('操作成功');
        } else {
            throw new \Exception('操作失败');
        }
    }

    /**
     *sp邀请详情页面
     * @api GET /osce/wechat/invitation/msg
     * @access public
     *
     * @param Request $request get请求<br><br>
     * <b>post请求字段：</b>
     * * string        id        教师id(必须的)
     * @return   view
     ** @version 1.0
     * @author zhouqiang <zhouqiang@misrobot.com>
     * @date
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function getMsg()
    {
        $id = intval(Input::get('id'));//老师的id
          $inviteModel =Invite::where('id','=',$id)->select('name','begin_dt','end_dt')->first();
         $caseId =ExamSpTeacher::where('teacher_id','=',$id)->select('case_id')->first()->case_id;
         $caseModel =CaseModel:: where('id','=',$caseId)->select('name')->first()->name;
//         dd($inviteModel->name);
        $list=[
             'exam_name' =>$inviteModel->name,
             'begin_dt' =>$inviteModel->begin_dt,
             'end_dt' =>$inviteModel->end_dt,
             'case_name' =>$caseModel,
        ];
//          dd($list);
        return view('osce::wechat.exammanage.sp_invitation_detail', [
            'id' => $id,
            'list'=>$list
        ]);
    }


}