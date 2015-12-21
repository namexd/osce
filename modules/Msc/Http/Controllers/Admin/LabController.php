<?php
/**
 * 开放实验室控制器
 * Created by PhpStorm.
 * User: wangjiang
 * Date: 2015/12/3 0003
 * Time: 15:17
 */

namespace Modules\Msc\Http\Controllers\Admin;

use App\Repositories\Common;
use Modules\Msc\Entities\Courses;
use Modules\Msc\Entities\ResourcesClassroom;
use Modules\Msc\Entities\ResourcesClassroomApply;
use Modules\Msc\Entities\ResourcesClassroomCourses;
use Modules\Msc\Entities\ResourcesClassroomPlan;
use Modules\Msc\Entities\ResourcesOpenLabApply;
use Modules\Msc\Entities\ResourcesOpenLabPlan;
use Modules\Msc\Http\Controllers\MscController;
use Illuminate\Http\Request;
use Modules\Msc\Entities\ResourcesLabHistory;
use Modules\Msc\Entities\ResourcesLabApply;
use Illuminate\Support\Facades\Auth;
use Modules\Msc\Repositories\Common as MscCommon;
use Illuminate\Support\Facades\Input;
use DB;
use Modules\Msc\Entities\ResourcesLabCalendar;
use Modules\Msc\Entities\ResourcesOpenLabCalendar;
class LabController extends MscController
{
    /**
     * 现有开放实验室
     * @api get /msc/admin/lab/had-open-lab-list
     * @access public
     *
     * @param Request $request post请求<br><br>
     * <b>post请求字段：</b>
     * * string        id           申请ID(必须的)
     * * string        reject       拒绝理由(必须的)
     *
     * @return object
     *
     * @version 1.0
     * @author whg <weihuiguo@misrobot.com>
     * @date 2015年12月18日15:47:31
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     *
     */

    public  function getHadOpenLabList(ResourcesClassroom $ResourcesClassroom){
        // 筛选条件处理
        $where = [];
        //$where = Input::get();
        $where['opened'] = Input::get('opened');
        $where['status'] = Input::get('status');
        $where['manager'] = Input::get('manager');
        $where['keyword'] = Input::get('keyword');
        // 获取列表
        $pagination = $ResourcesClassroom->getPcList($where);
        //获取负责人
        $arr = array();
        foreach($pagination as $v){
            if(!in_array($v['manager_name'],$arr)){
                $arr[] = $v['manager_name'];
            }
        }
        $data = [
            'pagination' => $pagination,
            'manager_name' => $arr,
            'keyword' => Input::get('keyword'),
            'status' => Input::get('status'),
            'manager' => Input::get('manager'),
            'opened' => Input::get('opened'),
        ];
        return view('msc::admin.openlab.lab-exist-list',$data);
    }


    /**
     * 现有开放实验室详情页
     * @api get /msc/admin/lab/had-open-lab-detail
     * @access public
     *
     * @param Request $request post请求<br><br>
     * <b>post请求字段：</b>
     * * string        id           申请ID(必须的)
     * * string        reject       拒绝理由(必须的)
     *
     * @return object
     *
     * @version 1.0
     * @author whg <weihuiguo@misrobot.com>
     * @date 2015年12月18日15:47:31
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     *
     */

    public  function getHadOpenLabDetail(ResourcesClassroom $ResourcesClassroom){
        $id = Input::get('id');
        $openLabDetail = ResourcesClassroom::find($id);
        $data = [
            'openLabDetail' => $openLabDetail
        ];
        return view('msc::admin.openlab.lab-exist-detail',$data);
    }

    /**
     * 现有开放实验室编辑页
     * @api get /msc/admin/lab/had-open-lab-edit
     * @access public
     *
     * @param Request $request post请求<br><br>
     * <b>post请求字段：</b>
     * * string        id           申请ID(必须的)
     * * string        reject       拒绝理由(必须的)
     *
     * @return object
     *
     * @version 1.0
     * @author whg <weihuiguo@misrobot.com>
     * @date 2015年12月18日15:47:31
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     *
     */

    public  function getHadOpenLabEdit(ResourcesClassroom $ResourcesClassroom){
        $id = Input::get('id');
        $openLabDetail = ResourcesClassroom::find($id);
        $data = [
            'openLabDetail' => $openLabDetail
        ];
        return view('msc::admin.openlab.lab-add',$data);
    }

    /**
     * 现有开放实验室新增页
     * @api get /msc/admin/lab/had-open-lab-add
     * @access public
     *
     * @param Request $request post请求<br><br>
     * <b>post请求字段：</b>
     * * string        id           申请ID(必须的)
     * * string        reject       拒绝理由(必须的)
     *
     * @return object
     *
     * @version 1.0
     * @author whg <weihuiguo@misrobot.com>
     * @date 2015年12月18日15:47:31
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     *
     */

    public  function getHadOpenLabAdd(ResourcesClassroom $ResourcesClassroom){
        return view('msc::admin.openlab.lab-add');
    }
    /**
     * 现有开放实验室编辑和新增操作
     * @api get /msc/admin/lab/had-open-lab-edit
     * @access public
     *
     * @param Request $request post请求<br><br>
     * <b>post请求字段：</b>
     * * string        id           申请ID(必须的)
     * * string        reject       拒绝理由(必须的)
     *
     * @return object
     *
     * @version 1.0
     * @author whg <weihuiguo@misrobot.com>
     * @date 2015年12月18日15:47:31
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     *
     */

    public  function postHadOpenLabToAdd(Request $request){
        DB::connection('msc_mis')->beginTransaction();
        $id = Input::get('id');
        $data = [
            'name' => Input::get('name'),
            'code' => Input::get('code'),
            'location' => Input::get('location'),
            'begintime' => Input::get('begintime'),
            'endtime' => Input::get('endtime'),
            'opened' => Input::get('opened'),
            'manager_id' => Input::get('manager_id')?Input::get('manager_id'):1,
            'manager_name' => Input::get('manager_name'),
            'manager_mobile' => Input::get('manager_mobile'),
            'detail' => Input::get('detail'),
            'status' => Input::get('status'),
            'person_total' => Input::get('person_total'),
        ];
        if($id){

           // dd('eqq');
            //修改实验室
            $add = DB::connection('msc_mis')->table('resources_lab')->where('id','=',$id)->update($data);
            if(!$add){
                DB::connection('msc_mis')->rollBack();
                return redirect()->back()->withErrors('系统异常');
            }
            $arr = [
                'begintime' => Input::get('begintime'),
                'endtime' => Input::get('endtime'),
            ];
            if(Input::get('opened') > 0){
                $addcleader = DB::connection('msc_mis')->table('resources_openlab_calendar')->where('resources_lab_id','=',$id)->update($arr);
            }else{
                $addcleader = DB::connection('msc_mis')->table('resources_lab_calendar')->where('resources_lab_id','=',$id)->update($arr);
            }
            if(!$addcleader){
                DB::connection('msc_mis')->rollBack();
                return redirect()->back()->withErrors('系统异常');
            }
        }else{
            //新增实验室
            $add = ResourcesClassroom::create($data);
            //dd($add);
            if(!$add){
                DB::connection('msc_mis')->rollBack();
                return redirect()->back()->withErrors('系统异常');
            }
            $arr = [
                'resources_lab_id' => $add['id'],
                'week' => '1,2,3,4,5,6,7',
                'begintime' => Input::get('begintime'),
                'endtime' => Input::get('endtime'),
            ];
            if(Input::get('opened') > 0){
                $addcleader = ResourcesOpenLabCalendar::create($arr);
            }else{
                $addcleader = ResourcesLabCalendar::create($arr);
            }
            if(!$addcleader){
                DB::connection('msc_mis')->rollBack();
                return redirect()->back()->withErrors('系统异常');
            }

        }
        DB::connection('msc_mis')->commit();
        return redirect()->intended('/msc/admin/lab/had-open-lab-list');
    }

    /**
     * 获得开放实验室使用历史列表
     * @method GET /msc/admin/lab/open-lab-history-list
     * @access public
     *
     * @param Request $request get请求<br><br>
     * <b>get请求字段：</b>
     * * string        date            搜索日期(没筛选-传'')
     * * string        keywords        关键字(没筛选-传'')
     * * string        order_name      排序字段名
     * * string        order_type      排序方式 枚举 e.g:desc,asc
     * * int           page            页码
     *
     * @return view
     *
     * @version 0.6
     * @author wangjiang <wangjiang@misrobot.com>
     * @date 2015-12-3 15:45
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function getOpenLabHistoryList(Request $request)
    {
        $this->validate($request, [
            'date'       => 'sometimes|date_format:Y/m/d',
            'keyword'    => 'sometimes', // TODO 搜索关键字长度限制
            'order_name' => 'sometimes|max:50',
            'order_type' => 'sometimes|in:0,1',
        ]);

        $searchDate = $request->input('date');
        $keyword    = urldecode(e($request->input('keyword')));
        $orderName  = e($request->input('order_name'));
        $orderType  = $request->input('order_type');

        // 排序处理
        if (!empty($orderName)) {
            if ($orderType) {
                $order = [$orderName, 'desc'];
            } else {
                $order = [$orderName, 'asc'];
            }
        } else {
            $order = ['id', 'desc']; // 默认按照ID降序排列
        }

        // 筛选条件处理
        $where = [];
        if ($searchDate) {
            $where['date'] = $searchDate;
        }
        if ($keyword) {
            $where['keyword'] = $keyword;
        }

        // 获取列表
        $labHis     = new ResourcesLabHistory();
        $pagination = $labHis->getPcList($where, $order);

        foreach ($pagination as $key => $item) {
            $pagination[$key]['user'] = $item->applyUserInfo ? $item->applyUserInfo->name : ''; // 预约人名字
        }

        return view('msc::admin.openlab.lab-history', ['pagination' => $pagination]);
    }

    /**
     * 获得开放实验室使用历史一条记录
     * @method GET /msc/admin/lab/openlab-history-item
     * @access public
     *
     * @param Request $request get请求<br><br>
     * <b>get请求字段：</b>
     * * int        $id        开放实验室使用历史编号
     *
     * @return view
     *
     * @version 0.6
     * @author wangjiang <wangjiang@misrobot.com>
     * @date 2015-12-3 18:13
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function getOpenlabHistoryItem($id)
    {
        $id = intval($id);

        $labHis = new ResourcesLabHistory();
        $data = $labHis->getPcItem($id);

        dd($data);
        //return view('msc::admin.resourcemanage.Existing', ['pagination'=>$pagination]);
    }

    /**
     * 开放实验室待审核列表
     * @api GET /msc/admin/lab/open-lab-apply-list
     * @access public
     *
     * @param Request $request get请求<br><br>
     * <b>get请求字段：</b>
     * * date           date        查询起始日期(必须的)
     * * string         keyword     搜索关键字(必须的)
     * * string         order       排序字段(必须的)
     * * string         orderby     排序方式(必须的) 枚举 asc 、desc
     *
     * @return view  字段说明{ 'id':'申请ID'  1,'name' :  '实验室名称', 'original_begin_datetime' :  '预约开始使用日期时间', 'original_end_datetime'     :  '预约结束使用日期时间','code' :  教室编码,'group':  '-','applyer_name':  '申请人','detail':  '申请说明','status':  '教室状态',}
     *
     * @version 1.0
     * @author Luohaihua <Luohaihua@misrobot.com>
     * @date 2015-12-03 15:48
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     *
     */
    public function getOpenLabApplyList(Request $request)
    {
        $this->validate($request, [
            'date' => 'sometimes|date_format:Y-m-d',
            'keyword' => 'sometimes',
            'orderType' => 'sometimes',
            'orderName' => 'sometimes',
        ]);
        $keyword    	=   e(urldecode($request->get('keyword')));
        $date       	=   $request->get('date');
        $orderName      =   e($request->get('order_name'));
        $orderType    	=   e($request->get('order_type'));
        $orderType    	=   empty($orderType)?    'desc':$orderType;
        $orderName      =   empty($orderName)?      '5'    :   $orderName;
        $date           =   empty($date)?      date('Y-m-d')    :   $date;
        //使用数组保存需要会显的数值
        $rollMsg = ['',''];
        $rollMsg[0] = $date;
        if ($keyword !== '') {
            $rollMsg[1] = $keyword;
        }

        //处理排序
        switch ($orderName) {
            case '1':
                $orderName = ['student.name','teacher.name'];
                break;
            case '2':
                $orderName = ['resources_lab.status','resources_lab.status'];
                break;
            default:
                $orderName = ['resources_lab_apply.created_at','resources_lab_apply.created_at'];
        }
        $order = [$orderName, $orderType];

        $ResourcesOpenLabApply  =   new ResourcesOpenLabApply();
        $list = $ResourcesOpenLabApply->getWaitExamineList($keyword, $date, $order);
        $statusValues   =   $ResourcesOpenLabApply    ->getStatusValues();

        return view('msc::admin.openlab.openaudit', ['pagination' => $list,'rollmsg' => $rollMsg,'statusValues'=>$statusValues]);
    }

    /**
     * 审核通过/拒绝开放实验室的申请
     * @api POST /msc/admin/lab/change-open-lab-apply-status
     * @access public
     *
     * @param Request $request post请求<br><br>
     * <b>post请求字段：</b>
     * * int            id              申请ID(必须的)
     * * string         status          要变更的状态(必须的)1=已通过 2=不通过
     * * string         reject          拒接描述(必须的)
     *
     * @return json {'id':'变更的成功的申请ID'}
     *
     * @version 1.0
     * @author Luohaihua <Luohaihua@misrobot.com>
     * @date 2015-12-03 16:08
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     *
     */
    public function postChangeOpenLabApplyStatus(Request $request)
    {
        $this->validate($request, [
            'id' => 'required|integer',
            'status' => 'required|in:1,2',
            'reject' => 'sometimes',
        ]);
        $id = $request->get('id');
        $status = $request->get('status');
        $reject = $request->get('reject');
        $ResourcesOpenLabApply  =   new ResourcesOpenLabApply();

        try {
            $result = $ResourcesOpenLabApply->dealApply($id, $status, $reject);
            if ($result) {
                return response()->json(
                    $this->success_data(['id' => $result->id])
                );
            } else {
                return response()->json($this->fail(new \Exception('审核失败')));
            }
        } catch (\Exception $ex) {
            return response()->json($this->fail($ex));
        }
    }

    /**
     * 开放实验室已审核列表
     * @api GET /msc/admin/lab/open-lab-apply-examined-list
     * @access public
     *
     * @param Request $request get请求<br><br>
     * <b>get请求字段：</b>
     * * string        date            搜索日期
     * * string        keywords        关键字
     * * string        order_name      排序字段名
     * * string        order_type      排序方式 枚举 e.g:desc,asc
     * * int           page            页码
     *
     * @return view
     *
     * @version 1.0
     * @author jiangzhiheng <jiangzhiheng@misrobot.com>
     * @date 2015-12-05 17:31
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     *
     */
    public function getOpenLabApplyExaminedList(Request $request)
    {
        $this->validate($request, [
            'date' => 'sometimes|date_format:Y-m-d',
            'keyword' => 'sometimes',
            'orderName' => 'sometimes',
            'orderType' => 'sometimes',
        ]);
        $keyword = e(urldecode($request->get('keyword')));
        $date = $request->get('date');
        $orderName = e($request->get('order_name'));
        $orderType = e($request->get('order_type'));
        $orderType = empty($orderType) ? 'desc' : $orderType;
        $orderName = empty($orderName) ? '5' : $orderName;
        $date = empty($date) ? date('Y-m-d') : $date;
        //处理排序
        switch ($orderName) {
            case '1':
                $orderName = ['student.name','teacher.name'];
                break;
            case '2':
                $orderName = ['resources_lab.status','resources_lab.status'];
                break;
            default:
                $orderName = ['resources_openlab_apply.created_at','resources_openlab_apply.created_at'];
        }
        $order = [$orderName, $orderType];

        //使用数组保存需要会显的数值
        $rollMsg = ['',''];
        $rollMsg[0] = $date;
        if ($keyword !== '') {
            $rollMsg[1] = $keyword;
        }

        $ResourcesOpenLabApply = new ResourcesOpenLabApply();
        $list = $ResourcesOpenLabApply  ->  getExaminedList($keyword, $date, $order);
        $statusValues   =   $ResourcesOpenLabApply         ->  getStatusValues();
        $groupNames =[];
        foreach($list as $item)
        {
            $groupList  =   $item->labApplyGroups();
            if(count($groupList))
            {
                $group  =   $groupList  ->  first();
                if(!is_null($group))
                {
                    $name   =   $group      ->  name;
                }
                $name       =   '-';
            }
            else
            {
                $name   =   '-';
            }
            $groupNames[$item->id]=$name;
        }

        return view('msc::admin.openlab.openaudited', ['pagination' => $list , 'rollmsg'=>$rollMsg,'groupNames'=>$groupNames,'statusValues'=>$statusValues]);
    }

    /**
     * 获取开放实验室分析记录(有筛选)-页面
     * @method GET /msc/admin/lab/openlab-history-analyze-form
     * @access public
     *
     * @param Request $request get请求<br><br>
     * <b>get请求字段：</b>
     *
     * @return view
     *
     * @version 0.6
     * @author wangjiang <wangjiang@misrobot.com>
     * @date 2015-12-4 11:03
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function getOpenlabHistoryAnalyzeForm(Request $request)
    {
        return view('msc::admin.openlab.lab-analyse');
    }

    /**
     * 获取开放实验室分析记录(有筛选)
     * @method GET /msc/admin/lab/openlab-history-analyze
     * @access public
     *
     * @param Request $request get请求<br><br>
     * <b>get请求字段：</b>
     * * date        $date         筛选日期(若不筛序-传'')
     * * int         $grade        年级(对学生而言,若不筛选-传0)
     * * int         $profession   专业编号(对学生而言,若不筛选-传0)
     * * int         $result_init  复位状态(1-良好 2-损坏 3-严重损坏,若不筛选-传0)
     *
     * @return view
     *
     * @version 0.6
     * @author wangjiang <wangjiang@misrobot.com>
     * @date 2015-12-4 11:03
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function getOpenlabHistoryAnalyze(Request $request)
    {
        $this->validate($request, [
            'date' 			=> 	'sometimes|date_format:Y/m/d',
            'grade' 		=> 	'sometimes|integer',  // TODO 学生年级都是如 2015 2011 ....
            'profession'    =>  'sometimes|integer',
            'result_init'   =>  'sometimes|in:0,1,2,3',
        ]);

        $searchDate  = $request->input('date');
        $grade       = $request->input('grade');
        $profession  = $request->input('profession');
        $result_init = $request->input('result_init');

        $where = [];
        if ($searchDate) {
            $where['date'] = $searchDate;
        }
        if ($grade) {
            $where['grade'] = $grade;
        }
        if ($profession) {
            $where['profession'] = $profession;
        }
        if ($result_init) {
            $where['result_init'] = $result_init;
        }

        $labHis = new ResourcesLabHistory();
        $data = $labHis->getPcAnalyze($where);
	
        return response()->json($data);
        //dd($data);
        //return view('msc::admin.openlab.lab-analyse', ['pagination'=>$data]);
    }

    /**
     * 拒绝开放实验室的突发预约
     * @method POST /msc/admin/lab/reject-urgent-apply
     * @access public
     *
     * @param Request $request post请求<br><br>
     * <b>post请求字段：</b>
     * * int        $id        紧急预约的编号
     * * string     $reject    拒绝理由
     * @return bool
     *
     * @version 0.6
     * @author wangjiang <wangjiang@misrobot.com>
     * @date 2015-12-4 18:26
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function postRejectUrgentApply(Request $request)
    {
        $this->validate($request, [
            'id'     => 'required|integer',
            'reject' => 'sometimes|string|max:255', // TODO 拒绝理由是否必填
        ]);

        $id     = $request->input('id');
        $reject = urldecode(e($request->input('reject')));

        $user = Auth::user(); // 操作人

        $apply = ResourcesOpenLabApply::findOrFail($id);

        $apply->status       = 2; // 不通过
        $apply->reject       = $reject;
        $apply->opeation_uid = $user->id;

        $result = $apply->save();

        return response()->json(
            $result ? true : false
        );
    }

    /**
     * 接受开放实验室的突发预约
     * @method GET /msc/admin/lab/accept-urgent-apply
     * @access public
     *
     * @param Request $request post请求<br><br>
     * <b>post请求字段：</b>
     * * int        $id          紧急预约的编号
     *
     * @return Json
     *
     * @version 0.6
     * @author wangjiang <wangjiang@misrobot.com>
     * @date 2015-12-6 15:38
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function postAcceptUrgentApply(Request $request)
    {
        $this->validate($request, [
            'id'  => 'required|integer',
        ]);

        $id = $request->input('id');

        // 获得申请信息
        $resourcesOpenLabApply = new ResourcesOpenLabApply();
        $applyInfo             = $resourcesOpenLabApply->getApplyInfo($id);

        $labId      = $applyInfo->resources_lab_id;
        $courseId   = $applyInfo->course_id;
        $teacherId  = $applyInfo->apply_uid;
        $date       = $applyInfo->apply_date;
        $beginTime  = $applyInfo->begintime;
        $endTime    = $applyInfo->endtime;
        $calendarId = $applyInfo->resources_lab_calendar_id;

        // 检查突发事件申请是否和已预约信息冲突
        $resourcesOpenLabPlan = new ResourcesOpenLabPlan();
        $conflicts = $resourcesOpenLabPlan->checkConflicts($labId, $date, $beginTime, $endTime);

        // 通过申请
        $user = Auth::user(); // 操作人

        $apply = ResourcesOpenLabApply::findOrFail($id);
        $apply->status       = 1; // 通过
        $apply->opeation_uid = $user->id;
        $apply->save();

        // 写入plan表
        $data = [
            'resources_openlab_id'          => $labId,
            'resources_openlab_calendar_id' => $calendarId,
            'course_id'                     => $courseId,
            'currentdate'                   => $date,
            'begintime'                     => $beginTime,
            'endtime'                       => $endTime,
            'type'                          => 3, // 临时占用
            'status'                        => 0, // 已预约未使用
            'apply_person_total'            => 0,
            'resorces_lab_person_total'     => 0,
        ];
        $resourcesOpenLabPlan->addTeacherPlan($data, $teacherId);

        return response()->json(
            $this->success_data($conflicts)
        );
    }

    /**
     * 取消和紧急预约冲突的课程安排
     * @method POST /msc/admin/lab/cancel-planned
     * @access public
     *
     * @param Request $request post请求<br><br>
     * <b>post请求字段：</b>
     * * string        $ids       预约id数组(必须的)
     *
     * @return bool
     *
     * @version 0.6
     * @author wangjiang <wangjiang@misrobot.com>
     * @date 2015-12-7 11:22
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function postCancelPlanned(Request $request)
    {
        $this->validate($request, [
            'ids' => 'required|array',
        ]);

        $ids = $request->input('ids');

        $resourcesOpenLabPlan = new ResourcesOpenLabPlan();
        $result = $resourcesOpenLabPlan->whereIn('id', $ids)->update(['status' => -1]);

        return response()->json(
            $result ? true : false
        );
    }

    /**
     * 根据实验室id和课程时间获得该实验室的空闲时间
     * @method GET /msc/admin/lab/lab-emptytime
     * @access public
     *
     * @param Request $request get请求<br><br>
     * <b>get请求字段：</b>
     * * string        $id        实验室id
     * * string        $time      一节课需要的时间(s)
     *
     * @return Json
     *
     * @version 0.6
     * @author wangjiang <wangjiang@misrobot.com>
     * @date 2015-12-7 12:03
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function getLabEmptytime ($id, $time)
    {
        $id   = intval($id);
        $time = intval($time);

        return MscCommon::classroomEmptyTime($id, $time);
    }

    /**
     * 延期和紧急预约冲突的课程安排
     * @method POST /msc/admin/lab/cancel-planned
     * @access public
     *
     * @param Request $request post请求<br><br>
     * <b>post请求字段：</b>
     * * string        $array       延期数组('id'=>1,已预约课程编号  'time'=>'2015/11/11 12:00-14:00',延期后的时间)(必须的)
     *
     * @return bool
     *
     * @version 0.6
     * @author wangjiang <wangjiang@misrobot.com>
     * @date 2015-12-7 12:07
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function postDelayPlanned (Request $request)
    {
        $this->validate($request, [
            'array' => 'required|array',
        ]);

        $newPlans = $request->input('array');

        $resourcesOpenLabPlan = new ResourcesOpenLabPlan();
        $result = $resourcesOpenLabPlan->chgPlans($newPlans);

        return response()->json($result);
    }

    /**
     * 向用户发送紧急通知
     * @api POST /msc/admin/lab/lab/open-lab-urgent-notice
     * @access public
     *
     * @param Request $request post请求<br><br>
     * <b>post请求字段：</b>
     * * int            id              申请ID(必须的)
     * * string         reject          拒接描述(必须的)
     *
     * @return json {'id':'变更的成功的申请ID'}
     * @author jiangzhiheng<jiangzhiheng@misrobot.com>
     * @version 1.0
     * @date 2015-12-07 10:08
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     *
     */
    public function postOpenLabUrgentNotice (Request $request) {
        $this->validate($request,[
            'id'        => 'required|numeric',
            'reject'    => 'required',
        ]);
        //获取经过AJAX传递过来的值
        $id = $request  ->  get('id');
        $reject = e($request->get('reject'));
        try {
            //获取openid
            $apply=ResourcesClassroomApply::find($id);
            $openID = $apply    ->    applyer    ->    openid;
            //发送微信消息
            $result = $this->sendMsg2($reject,$openID);
            //判断是否成功
            if ($result === false) {
                throw new \Exception('消息发送失败!');
            }   else {
                return response()   ->    json(
                    $this->success_data(['id'=>$id])
                );
            }
        }   catch (\Exception $ex){
            return response()   ->  json($this  ->  fail($ex));
        }
    }

    /**
     * 开放实验室设备未审核列表
     * @api GET /msc/admin/lab/open-lab-tools-apply-list
     * @access public
     *
     * @param Request $request get请求<br><br>
     * <b>get请求字段：</b>
     * * string        date            搜索日期
     * * string        keywords        关键字
     * * string        order_name      排序字段名
     * * string        order_type      排序方式(1 ：Desc 0:asc)
     * * int           page            页码
     *
     * @return view
     *
     * @version 1.0
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     *
     */
    public function getOpenLabToolsApplyList(Request $request) {
        $data = [
            [
                'id'                        =>  1,
                'name'                      =>  '设备A',
                'date'                      =>  '2015/9/20',
                'time'                      =>  '08:00-10:00',
                'code'                      =>  1024,
                'applyer_name'              =>  '李老师',
                'detail'                    =>  '开膛破肚',
                'extend'                    =>  '是',
                'status'                    =>  '空闲',
            ],
            [
                'id'                        =>  2,
                'name'                      =>  '设备B',
                'date'                      =>  '2015/9/25',
                'time'                      =>  '08:00-10:00',
                'code'                      =>  1024,
                'applyer_name'              =>  '刘老师',
                'detail'                    =>  '开膛破肚',
                'extend'                    =>  '否',
                'status'                    =>  '空闲',
            ],
            [
                'id'                        =>  3,
                'name'                      =>  '设备C',
                'date'                      =>  '2015/9/30',
                'time'                      =>  '08:00-10:00',
                'code'                      =>  1024,
                'applyer_name'              =>  '张老师',
                'detail'                    =>  '开膛破肚',
                'extend'                    =>  '否',
                'status'                    =>  '空闲',
            ],
        ];
        return view('msc::admin.open_equipment_manage.openequ_audit',['data'=>$data]);
    }


    //向微信用户发送普通文本消息
    private function sendMsg2($msg,$openid) {
        if (empty($openid)) {
            throw new \Exception('没有找到用户的微信OpenID');
        }

        $userService = new \Overtrue\Wechat\Staff(config('wechat.app_id'),config('wechat.secret'));
        return $userService->send($msg)->to($openid);
    }

    /**
     *  突发事件列表
     * @api GET /msc/admin/lab/urgent-apply-list
     * @access public
     *
     * @param Request $request get请求<br><br>
     * <b>get请求字段：</b>
     * * string        date         查询日期 e.g:2015-12-08
     * * string        keyword      实验室名称e.g：测试教室001
     * * string        order        排序字段 枚举： courses_name
     * * string        orderby      排序方式
     *
     * @return view
     *
     * @version 1.0
     * @author Luohaihua <Luohaihua@misrobot.com>
     * @date  2015-12-07 18:00
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     *
     */
    public function getUrgentApplyList(Request $request){
        $this->validate($request,[
            'date'      => 'sometimes|date_format:Y-m-d',
            'keyword'   => 'sometimes',
            'order'     => 'sometimes',
            'orderby'   => 'sometimes',
        ]);
        $date       =   e($request->get('date'));
        $keyword    =   e($request->get('keyword'));
        $order      =   e($request->get('order'));
        $orderby    =   e($request->get('orderby'));

        $order      =   [$order,$orderby];
        $resourcesOpenlabApply  =   new ResourcesOpenLabApply();
        $pagination             =   $resourcesOpenlabApply  -> getUrgentApplyList($date,$keyword,$order);
        $resourcesClassroomModel=   new ResourcesClassroom();
        $statusAttrNames        =   $resourcesClassroomModel->  getstatusAttrName();
        return view('msc::admin.emergencymanage.emergencybase',['pagination'=>$pagination,'statusAttrNames'=>$statusAttrNames]);
    }
    /**
     * 突发事件-审核通过 时 选择 操作 的页面
     * @api GET /msc/admin/lab/agree-emergency-apply
     * @access public
     *
     * @param Request $request GET请求<br><br>
     * <b>GET请求字段：</b>
     * * string        id        突发事件申请ID(必须的)
     *
     * @return view
     *
     * @version 1.0
     * @author Luohaihua <Luohaihua@misrobot.com>
     * @date
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     *
     */
    public function getAgreeEmergencyApply(Request $request){
        $this->validate($request,[
            'id'        => 'required|integer'
        ]);
        $id     =   $request    ->  get('id');
        //已有的 冲突课程记录

        $apply  =   ResourcesOpenLabApply::find($id);
        $ResourcesOpenLabPlan    =   new ResourcesOpenLabPlan();
        $data   =   $ResourcesOpenLabPlan ->getConflicts(
            $apply->resources_lab_id,
            $apply->apply_date,
            $apply->OpenLabCalendar->begintime,
            $apply->OpenLabCalendar->endtime
        );
        $list   =   [];
        foreach($data as $item){
            $teachers   =   $item    ->  teachers;
            $teacher    =   count($teachers)>0? $teachers ->  first() :   '';
            $itemData    =   [
                'name'              =>  $item    ->  course  ->  name,
                'teacher_name'      =>  empty($teacher)? '-':$teacher   ->teacher   ->name,
                'lan_name'          =>  $item    ->  lab ->name,
                'currentdate'       =>  date('Y/m/d',strtotime($item    ->  currentdate)),
                'time'              =>  date('H:i',strtotime($item    ->  begintime)).'-'.date('H:i',strtotime($item    ->  endtime)),
            ];
            $list[]     =   $itemData;
        }
        //陈老师 课程A 开发实验室A 2015/09/18 08:00-15:00
        return response()->json(
            $this->success_rows(1, '获取成功', 1, count($list),1,$list )
        );
    }

    /**
     * 处理突发事件申请 通过 数据
     * @api POST /msc/admin/lab/agree-emergency-apply
     * @access public
     *
     * @param Request $request post请求<br><br>
     * <b>post请求字段：</b>
     * * int            id          申请ID(必须的)
     * * string        notice       取消预约通知文本(必须的)
     *
     * @return object
     *
     * @version 1.0
     * @author Luohaihua <Luohaihua@misrobot.com>
     * @date 2015-12-04
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     *
     */
    public function postAgreeEmergencyApply(Request $request){
        $this->validate($request,[
            'id'        => 'required|integer'
        ]);

        $id     =   $request    ->  get('id');
        $notice =   $request    ->  get('notice');
        //已有的 冲突课程记录
        $ResourcesOpenLabPlan   =   new ResourcesOpenLabPlan();
        try{
            $result =   $ResourcesOpenLabPlan   ->  cancelOldPlan($id,$notice);
            if($result)
            {
                //成功回跳到列表
                return response()   ->    json(
                    $this->success_data(['id'=>$id])
                );
            }
            else
            {
                throw new \Exception('操作失败');
            }
        }
        catch(\Exception $ex)
        {
            return response()   ->    json(
                $this->fail($ex)
            );
        }

    }
    /**
     * 处理突发事件申请 不通过 数据
     * @api POST /msc/admin/lab/refund-emergency-apply
     * @access public
     *
     * @param Request $request post请求<br><br>
     * <b>post请求字段：</b>
     * * string        id           申请ID(必须的)
     * * string        reject       拒绝理由(必须的)
     *
     * @return object
     *
     * @version 1.0
     * @author Luohaihua <Luohaihua@misrobot.com>
     * @date 2015-12-04
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     *
     */
    public function postRefundEmergencyApply(Request $request){
        $this->validate($request,[
            'id'        => 'required|integer'
        ]);
        $id     =   $request    ->  get('id');
        $reject =   $request    ->  get('reject');
        $resourcesOpenlabApplyModel   =   new ResourcesOpenLabApply();
        try{
            $result =   $resourcesOpenlabApplyModel   ->  refund($id,$reject);
            if($result)
            {
                //成功回跳到列表
                return response()   ->    json(
                    $this->success_data(['id'=>$id])
                );
            }
            else
            {
                throw new \Exception('保存失败');
            }
        }
        catch(\Exception $ex)
        {
            return response()   ->    json(
                $this->fail($ex)
            );
        }
    }
    public function postImportLab(Request $request){
        try{
            $data=Common::getExclData($request,'lab');
            $coursesList= array_shift($data);
            //将中文表头 按照配置 翻译成 英文字段名
            $data=Common::arrayChTOEn($coursesList,'msc.importForCnToEn.lab_import');
            //已经存在的数据
            $dataHaven=[];
            //添加失败的数据
            $dataFalse=[];
            $dataNew    =[];
            foreach($data as $coursesData)
            {
                if(is_numeric($coursesData['code']))
                {
                    $coursesData['code']   = str_replace(',','',strval(number_format($coursesData['code']))) ;
                }
                $input  =   [
                    'name'          =>  $coursesData['name'],
                    'code'          =>  $coursesData['code'],
                    'location'      =>  '新八教'.$coursesData['floor'],
                    'begintime'     =>  '08:00:00',
                    'endtime'       =>  '22:00:00',
                    'opened'        =>  strrpos('开放',$coursesData['name'])>=1? 1:0,
                    'manager_name'  =>  $coursesData['manager_name'],
                    'manager_mobile'=>  '',
                    'detail'        =>  '',
                ];
                $dataNew[]=$input;
            }
            var_export($dataNew);
            exit();
            return response()->json(
                $this->success_data(['result'=>true,'dataFalse'=>$dataFalse,'dataHaven'=>$dataHaven])
            );
        }
        catch(\Exception $ex)
        {
            return response()->json($this->fail($ex));
        }
    }
    /**
     * 添加实验室
     * @api POST /msc/admin/lab/add-lab
     * @access public
     *
     * @param Request $request post请求<br><br>
     * <b>post请求字段：</b>
     * * string        id           申请ID(必须的)
     * * string        reject       拒绝理由(必须的)
     *
     * @return object
     *
     * @version 1.0
     * @author tangjun <tangjun@misrobot.com>
     * @date 2015-12-18
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     *
     */
    public function postAddLab(Request $Request,ResourcesClassroom $ResourcesClassroom){

        $data = [
            'name'=>'测试教室',
            'code'=>'1354562',
            'location'=>'测试地址',
            'begintime'=>'08:00:00',
            'endtime'=>'18:00:00',
            'opened'=>'0',
            'manager_id'=>'1',
            'manager_name'=>'唐俊',
            'manager_mobile'=>'15928785615',
            'detail'=>'测试',
            'status'=>1,
            'person_total'=>'30'
        ];
        $rew = $ResourcesClassroom->firstOrCreate($data);

        dd($rew);
    }
}