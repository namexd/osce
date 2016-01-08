<?php
/**
 * Created by PhpStorm.
 * User: j5110
 * Date: 2016/1/5
 * Time: 16:44
 */

namespace Modules\Osce\Http\Controllers\Admin;

use Modules\Osce\Entities\ExamScreening;
use Modules\Osce\Entities\Teacher;
use Modules\Osce\Http\Controllers\CommonController;
use Illuminate\Http\Request;

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
        //验证略

        //得到请求的病例id和已经选择的sp老师id
        $stationId = $request->input('station_id', '');
        $spteacherId = $request->input('spteacher_id', '');

        //得到老师的列表
        $data = $model->showTeacherData($stationId, $spteacherId);

        return $this->success_data($data);
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
    public function getInvitationIndex(Request $request){
          $this->validate($request,[
              'exam_id'    =>'required|integer'
          ],[
              'exam_id.required'   => '没有考试ID'
          ]);

           $examId = $request->input('exam_id');
            $ScreeningModel=new ExamScreening();
         $Station = $ScreeningModel->getStationList($examId);
        $data=[
               'station_id' =>$Station['station_id'],
               'station_name' =>$Station['station_name'],
               'teacher_name' =>$Station['teacher_name'],
               'teacher_id' =>$Station['teacher_id'],
        ];

        return view('Osce::admin.exammanage.sp_invitation',[
            'data'    => $data,
        ]);
    }



}