<?php
/**
 * Created by PhpStorm.
 * User: j5110
 * Date: 2016/1/6
 * Time: 17:05
 */

namespace Modules\Osce\Http\Controllers\Wechat;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
            'teacher_id' => 'required',
            'exam_id' => 'required|integer',
            'station_id' => 'required',
        ], [
            'teacher_id.required' => '请确认老师信息是否正确',
            'exam_id.required' => '请确认考试信息是否正确',
            'station_id.required' => '请选请确认考站是否正确',
        ]);
        $teacher_id = $request->get('teacher_id');
        $exam_id = $request->get('exam_id');
        $stationId = $request->get('station_id');
        //根据老师id查询老师的信息和openid
        $teacher = new Teacher();
        $data = $teacher->invitationContent($teacher_id);

        //根据考试id查询出考试相关信息
        $ExamModel = new Exam();
//        $ExamList = $ExamModel->where('id', $exam_id)->select('name', 'begin_dt', 'end_dt')->first()->toArray();
        $ExamList = $ExamModel->find($exam_id);
        //根据考试id查询出场次id
        //$examscreening = ExamScreening::where('exam_id','=',$exam_id)->select('id')->first();
        $examscreening = $ExamList->examScreening->first();
        foreach ($data as $key => $v) {
            $data[$key]['exam_name'] = $ExamList['name'];
            $data[$key]['begin_dt'] = $ExamList['begin_dt'];
            $data[$key]['end_dt'] = $ExamList['end_dt'];
            $data[$key]['exam_id'] = $exam_id;
            $data[$key]['exam_screening_id'] = $examscreening->id;
            $data[$key]['station_id'] = $stationId;
        }

        $InviteModel = new Invite();
        try {
            if ($InviteModel->addInvite($data)) {
                return response()->json(
                    $this->success_data()
                );
            } else {
                throw new \Exception('邀请失败');
            }
        } catch (\Exception $ex) {
            return response()->json(
                $this->fail($ex)
            );
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

        $user = Auth::user();
        if (empty($user)) {
            throw new \Exception('未找到当前操作人信息');
        }
        $userId = $user->id;

        $notice = new Invite();
//        $list = $notice->get();
        $list = $notice->where('user_id', '=', $userId)->get();
        return view('osce::wechat.exammanage.sp_invitation', ['list' => $list]);//这里页面应该为列表页面
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


    public function getInvitationRespond(Request $request, Invite $Invite)
    {

        $this->validate($request, [
            'status' => 'required|integer',
            'id' => 'required|integer'
        ]);
        $status = $request->get('status');
        $id = $request->get('id');

        $result = $Invite->where('id', '=', $id)->update(['status' => $status]);
//        echo json_decode(11111);die;
        if ($result) {
            return response()->json(
                $this->success_data($result, 1, '操作成功')
            );
        } else {
            return response()->json(
                $this->success_data([],0, '操作失败')
            );
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

        $id = intval(Input::get('id'));//邀请id
        $inviteModel = Invite::where('id', '=', $id)->select('name', 'begin_dt', 'end_dt', 'status', 'user_id')->first();
        if ($inviteModel) {
            $caseId = ExamSpTeacher::where('teacher_id', '=', $inviteModel->user_id)->select('case_id')->first();
            if (!$caseId) {
                throw new \Exception('没有找到相关病例');
            } else {
                $caseModel = CaseModel:: where('id', '=', $caseId->case_id)->select('name')->first()->name;
                $teacher =Teacher:: find($inviteModel->user_id);
            }
        } else {
            throw new \Exception('请检查登陆稍后再试!');
        }
        $list = [
            'exam_name' => $inviteModel->name,
            'begin_dt' => $inviteModel->begin_dt,
            'end_dt' => $inviteModel->end_dt,
            'case_name' => $caseModel,
            'status' => $inviteModel->status,
            'teacher_name' =>$teacher->name,
        ];
//          dd($list);
        return view('osce::wechat.exammanage.sp_invitation_detail', [
            'id' => $id,
            'list' => $list
        ]);
    }


}