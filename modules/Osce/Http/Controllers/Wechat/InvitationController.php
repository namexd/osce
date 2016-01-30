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
//            'station_id' => 'required|integer',
        ], [
            'teacher_id.required' => '邀请编号必须',
            'exam_id.required'=>'考试编号必须'
        ]);
        $teacher_id = $request->get('teacher_id');
        $exam_id = $request->get('exam_id');
//        $exam_id = 56;
        $teacher = new Teacher();
        $data = $teacher->invitationContent($teacher_id);
        $ExamModel = new Exam();
        $ExamList = $ExamModel->where('id', $exam_id)->select('name', 'begin_dt', 'end_dt')->first()->toArray();
        $examscreening = ExamScreening::where('exam_id','=',$exam_id)->select('id')->first();
            foreach($data as $key=>$v){
                $data[$key]['exam_name'] = $ExamList['name'];
                $data[$key]['begin_dt'] = $ExamList['begin_dt'];
                $data[$key]['end_dt'] = $ExamList['end_dt'];
                $data[$key]['exam_id'] = $exam_id;
                $data[$key]['exam_screening_id']= $examscreening->id;
            }
        $InviteModel = new Invite();
        try{
            if ($InviteModel->addInvite($data)) {
                return response()->json(
                    $this->success_data()
                );
            } else {
                throw new \Exception('邀请失败');
            }
        }
        catch(\Exception $ex)
        {
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

        $user= Auth::user();
        if(empty($user))
        {
            throw new \Exception('未找到当前操作人信息');
        }
        $userId =$user->id;
        //根据用户id查出该用户对邀请的处理
        $status = Teacher::where('id','=',$userId)->select('status')->first();
        $notice = new Invite();
//        $list = $notice->get();
        $list = $notice-> where('id','=',$userId)->get();
//        dd($list);
        return view('osce::wechat.exammanage.sp_invitation',['list'=>$list],['status'=>$status]);//这里页面应该为列表页面
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

        $this->validate($request, [
            'status' => 'required|integer',
            'id' => 'required|integer'
        ]);
        $status = $request->get('status');
        $teacher_id = $request->get('id');

        $result = $teacher->where('id', '=', $teacher_id)->where('type','=',2)->update(['status'=>$status]);
//        echo json_decode(11111);die;
        if ($result) {
            return response()->json(
                $this->success_data($result,1,'操作成功')
            );
        } else {
            return response()->json(
                $this->success_data(0,'操作失败')
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
        $id = intval(Input::get('id'));//老师的id
        $inviteModel =Invite::where('id','=',$id)->select('name','begin_dt','end_dt')->first();
          if($inviteModel){
              $caseId =ExamSpTeacher::where('teacher_id','=',$id)->select('case_id')->first()->case_id;
              if(!$caseId){
                  throw new \Exception('没有找到相关病例');
              }else{
                  $caseModel =CaseModel:: where('id','=',$caseId)->select('name')->first()->name;
              }
        }else{
              throw new \Exception('请检查登陆稍后再试!');
          }
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