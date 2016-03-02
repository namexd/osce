<?php
/**
 * Created by PhpStorm.
 * User: j5110
 * Date: 2016/1/5
 * Time: 16:44
 */

namespace Modules\Osce\Http\Controllers\Admin;

use Illuminate\Support\Facades\Auth;
use Modules\Osce\Entities\ExamRoom;
use Modules\Osce\Entities\ExamScreening;
use Modules\Osce\Entities\ExamSpTeacher;
use Modules\Osce\Entities\Teacher;
use Modules\Osce\Http\Controllers\CommonController;
use Illuminate\Http\Request;
use DB;
use Url;

class SpteacherController extends CommonController
{
    /**
     * ajax获取SP老师名单
     * @api       GET /osce/admin/spteacher/show
     * @access    public
     * @param Request $request get请求<br><br> 具体参数如下
     * int $caseId:病例id
     * array $spteacherId
     * int $teacherType
     * @param Station $model
     * @return view
     * @version   1.0
     * @author    jiangzhiheng <jiangzhiheng@misrobot.com>
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function getShow(Request $request, Teacher $model)
    {
        try {
        //验证略
        //得到请求的病例id和已经选择的sp老师id
        $stationId = $request->input('station_id', '');
        $spteacherId = $request->input('spteacher_id', []);

        //得到老师的列表
        $stationIds[] = $stationId;

//        $spteacherIds[] =$spteacherId;
        $spteacherIds=array_unique($spteacherId);
        $data = $model->showTeacherData($stationIds, $spteacherIds);
//dd($data);
        return  response()->json($this->success_rows(1,'获取成功',count($data),count($data),1,$data->toArray()));
        } catch (\Exception $ex) {
            return response()->json($this->fail($ex));
        }
    }

    /**
     *  sp邀请页面数据
     * @method GET
     * @url /osce/admin/spteacher/invitation-index
     * @access public
     * @param Request $request get请求<br><br>
     * <b>get请求字段：</b>
     * * string        id       考试id(必须的)
     *
     * @return view
     *
     * @version 1.0
     * @author zhouqiang <zhouqiang@misrobot.com>
     * @date
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function getInvitationIndex(Request $request)
    {

//          $this->validate($request,[
//              'exam_id'    =>'required|integer'
//          ],[
////              'exam_id.required'   => '没有考试ID'
//          ]);
        $examId = $request->input('id');

        $inviteModel = new ExamSpTeacher();
        $inviteData = $inviteModel->where('exam_screening_id', '=', $examId)->get()->keyBy('teacher_id');

//        dd($inviteData);
        if ($examId) {
            $ExamModel = new ExamRoom();
            $TeacherSp = $ExamModel->getStationList($examId);
//             dump($TeacherSp->toArray());
            $stationTeacher = [];
            foreach ($TeacherSp as $data) {
                $stationData = [];
                if (isset($stationTeacher[$data['station_id']])) {
                    $stationData = $stationTeacher[$data['station_id']];
//                    dd($stationData);
                    $stationData['techs'][$data['id']] = [
                        'name' => $data['name'],
                        'id' => $data['id'],
                        'status' => -1
                    ];
                } else {
                    $stationData = [
                        'station_id' => $data['station_id'],
                        'station_name' => $data['station_name'],
                        'techs' => [
                            $data['id'] => [
                                'name' => $data['name'],
                                'id' => $data['id'],
                                'status' => -1 // < 0  没有邀请
                            ]
                        ]
                    ];
                }
                $stationData['invited'] = [];
                // handle invite teacher
                if (isset($inviteData[$data['id']])) {
                    $stationData['techs'][$data['id']]['status'] = $inviteData[$data['id']]['status'];
                    $stationData['invited'][] = [
                        'name' => $data['name'],
                        'id' => $data['id'],
                        'status' => $inviteData[$data['id']]['status']
                    ];
                }
                $stationTeacher[$data['station_id']] = $stationData;
            }

//             dd($stationTeacher);
            dump($stationTeacher);

            return view('osce::admin.examManage.sp_invitation', [
                'id' => $request->input('id'),
                'data' => $stationTeacher,

            ]);
        }
        return redirect()->route('osce::admin.examManage.sp_invitation');//还不确定。

    }


    /**
     *  sp邀请保存
     * @method GET
     * @url /osce/admin/spteacher/invitation-add
     * @access public
     * @param Request $request get请求<br><br>
     * <b>get请求字段：</b>
     * * string        id       考试id(必须的)
     *
     * @return view
     *
     * @version 1.0
     * @author zhouqiang <zhouqiang@misrobot.com>
     * @date
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */


    public function  getInvitationAdd(Request $request)
    {
//        dd(1111111111);

        $this->validate($request, [
            'station_id' => 'required|integer',
            'teacher_id' => 'required|integer',
            'case_id' => 'required|integer',
            'status' => 'required|integer',
        ]);
        $Invitation = [];
        $req = $request->all();
        $user = Auth::user();
        if (is_array($req['user_id'])) {
            foreach ($req['user_id'] as $v) {
                $arr = explode(",", $v);
                $Invitations['station_id'] = $req['station_id'];
                $Invitations['case_id'] = $req['case_id'];
                $Invitations['status'] = $req['status'];
//                $LabDevices['user_id'] = $arr[0];
                $LabDevices['created_user_id'] = $user->id;
                $Invitation [] = $LabDevices;
            }
        }
        $return = DB::connection('osce_mis')->table('invite')->insert($Invitation);

        if ($return) {
            return redirect()->back()->withInput()->withErrors('保存成功');
        } else {
            return redirect()->back()->withInput()->withErrors('保存失败');
        }

    }

}