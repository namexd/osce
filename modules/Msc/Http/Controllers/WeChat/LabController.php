<?php
/**
 * 开放实验室控制器
 * Created by PhpStorm.
 * User: wangjiang
 * Date: 2015/12/3 0003
 * Time: 15:17
 */

namespace Modules\Msc\Http\Controllers\WeChat;

use Modules\Msc\Entities\ResourcesClassroomPlan;
use Illuminate\Http\Request;
use Modules\Msc\Entities\ResourcesLabHistory;
use Modules\Msc\Entities\ResourcesClassroomApply;
use Modules\Msc\Entities\ResourcesOpenLabApply;
use Modules\Msc\Entities\ResourcesOpenLabPlan;
use Modules\Msc\Http\Controllers\MscWeChatController;

class LabController extends MscWeChatController
{
    /**
     * 开放实验室待审核列表 (WX-Admin-001-信息管理_资源管理_开放实验室管理_预约申请管理)
     * @api GET /msc/wechat/lab/open-lab-apply-list
     * @access publico
     *
     * return view
     * @version 1.0
     * @author Luohaihua <Luohaihua@misrobot.com>
     * @date 2015-12-04 15:02
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     *
     */
    public function getOpenLabApplyList(){
        return view('msc::wechat.resource.openlab_apply');
    }
    /**
     * 开放实验室待审核列表数据获取(WX-Admin-001-信息管理_资源管理_开放实验室管理_预约申请管理)
     * @api GET /msc/wechat/lab/open-lab-apply-list-data
     * @access public
     *
     * @param Request $request get请求<br><br>
     * <b>get请求字段：</b>
     * * date           date        查询起始日期(必须的)
     * * string         keyword     搜索关键字(必须的)
     * * string         order       排序字段(必须的)
     * * string         orderby     排序方式(必须的)
     *
     * @return view  字段说明{ 'id':'申请ID'  1,'name' :  '实验室名称', 'original_begin_datetime' :  '预约开始使用日期时间', 'original_end_datetime'     :  '预约结束使用日期时间','code' :  教室编码,'group':  '-','applyer_name':  '申请人','detail':  '申请说明','status':  '教室状态',}
     *
     * @version 1.0
     * @author Luohaihua <Luohaihua@misrobot.com>
     * @date 2015-12-03 15:48
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     *
     */
    public function getOpenLabApplyListData(Request $request){

        $this->validate($request, [
            'date'      => 'sometimes|date_format:Y-m-d',
            'keyword'   => 'sometimes',
            'order'     => 'sometimes',
            'orderby'   => 'sometimes',
        ]);
        $keyword    =   e(urldecode($request->get('keyword')));
        $date       =   $request->get('date');
        $order      =   e($request->get('order'));
        $orderby    =   e($request->get('orderby'));
        $orderby    =   empty($orderby)?    'desc':$orderby;
        $order      =   empty($order)?      'created_at'    :   $orderby;
        $date       =   empty($date)?      date('Y-m-d')    :   $date;
        $order      =   [$order,$orderby];
        //$ResourcesClassroomApply    =   new ResourcesClassroomApply();
        $ResourcesOpenLabApply      =   new ResourcesOpenLabApply();
        $list       =   $ResourcesOpenLabApply    ->  getWaitExamineList($keyword,$date,$order);
        $data=[];
        foreach($list as $item)
        {
            $data[]=[
                'id'                        =>  $item->id,
                'name'                      =>  $item->lab->name,
                'original_begin_datetime'   =>  date('Y/m/d',strtotime($item->apply_date)) .' '. date('H:i',strtotime($item->OpenLabCalendar->begintime)),
                'original_end_datetime'     =>  date('H:i',strtotime($item->OpenLabCalendar->endtime)),
                'applyer_name'              =>  $item->applyUser->name,
                'detail'                    =>  $item->detail,
                'status'                    =>  $item->status,
            ];
        }
        return response()->json(
            $this->success_rows(1,'获取成功',$list->lastPage(),20,$list->currentPage(),$data)
        );
    }

    /**
     * 审核通过/拒绝开放实验室的申请
     * @api POST /msc/wechat/lab/change-open-lab-apply-status
     * @access public
     *
     * @param Request $request post请求<br><br>
     * <b>post请求字段：</b>
     * * int            id              申请ID(必须的)
     * * string         status          要变更的状态(必须的)
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
    public function postChangeOpenLabApplyStatus(Request $request){
        $this->validate($request,[
            'id'        => 'required|integer',
            'status'    => 'required|in:1,2',
            'reject'    => 'sometimes',
        ]);
        $id     =   $request    ->  get('id');
        $status =   $request    ->  get('status');
        $reject =   $request    ->  get('reject');
        $ResourcesClassroomApply    =   new ResourcesClassroomApply();

        try
        {
            $result=    $ResourcesClassroomApply    ->  dealApply($id,$status,$reject,3);
            if($result)
            {
                return response()->json(
                    $this->success_data(['id'=>$result])
                );
            }
            else
            {
                return response()->json($this->fail(new \Exception('审核失败')));
            }
        }
        catch(\Exception $ex)
        {
            return response()->json($this->fail($ex));
        }
    }

    /**
     * 开放实验室-审核不通过 输入页面
     * @api GET /msc/wechat/lab/refused-open-lab-apply
     * @access public
     *
     * @param Request $request GET请求<br><br>
     * <b>GET请求字段：</b>
     * * string        id        申请ID(必须的)
     *
     * @return view
     *
     * @version 1.0
     * @author Luohaihua <Luohaihua@misrobot.com>
     * @date
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     *
     */
    public function getRefusedOpenLabApply(Request $request){
        $this->validate($request,[
            'id'        => 'required|integer'
        ]);
        $id=intval($request->id);
        return view('msc::wechat.resource.openlab_apply_refuse',['id'=>$id]);
    }

    /**
     * 突发事件-审核通过 时 选择 操作 的页面
     * @api GET /msc/wechat/lab/agree-emergency-apply
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
        $original_begin_datetime    =   $apply->apply_date .' '.$apply->OpenLabCalendar->begintime;
        $original_end_datetime    =   $apply->apply_date .' '.$apply->OpenLabCalendar->endtime;;
        $ResourcesOpenLabPlan    =   new ResourcesOpenLabPlan();
        $data   =   $ResourcesOpenLabPlan ->getConflicts(
                $apply->resources_lab_id,
                $apply->apply_date,
                $apply->OpenLabCalendar->begintime,
                $apply->OpenLabCalendar->endtime
        );
        return view('msc::wechat.resource.emergency_detail_pass',['list'=>$data,'id'=>$id]);
    }

    /**
     * 处理突发事件申请 通过 数据
     * @api POST /msc/wechat/lab/agree-emergency-apply
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
                return redirect()->route('msc.wechat.openLaboratory.emergencyManage');
            }
            else
            {
                throw new \Exception('操作失败');
            }
        }
        catch(\Exception $ex)
        {
            return redirect()->back()->withErrors($ex);
        }

    }

    /**
     * 突发事件-审核不通过 时 填写拒绝理由的页面
     * @api GET /msc/wechat/lab/refund-open-lab-apply
     * @access public
     *
     * @param Request $request GET请求<br><br>
     * <b>GET请求字段：</b>
     * * string        id        申请ID(必须的)
     *
     * @return view
     *
     * @version 1.0
     * @author Luohaihua <Luohaihua@misrobot.com>
     * @date
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     *
     */
    public function getRefundEmergencyApply(Request $request){
        $this->validate($request,[
            'id'        => 'required|integer'
        ]);
        $id     =   $request    ->  get('id');
        $apply  =   ResourcesOpenLabApply::find($id);
        return view('msc::wechat.resource.emergency_detail_refuse',['data'=>$apply]);
    }
    /**
     * 处理突发事件申请 不通过 数据
     * @api POST /msc/wechat/lab/refund-emergency-apply
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
            $resourcesOpenlabApplyModel   ->  refund($id,$reject);
            return redirect()->route('msc.wechat.openLaboratory.emergencyManage');
        }
        catch(\Exception $ex)
        {
            return redirect()->back()->withErrors($ex);
        }
    }

    /**
     * 获取开放实验室使用历史列表-表单
     * @method GET /msc/wechat/lab/openlab-history-list
     * @access public
     *
     * @param Request $request get请求<br><br>
     * <b>get请求字段：</b>
     * * string        参数英文名        参数中文名(必须的)
     *
     * @return view
     *
     * @version 0.6
     * @author wangjiang <wangjiang@misrobot.com>
     * @date 2015-12-4 14:26
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function getOpenlabHistoryList (Request $request)
    {
        return view('msc::wechat.resource.openlab_history');
    }

    /**
     * 获取开放实验室使用历史列表-处理
     * @method GET /msc/wechat/lab/openlab-history-list
     * @access public
     *
     * @param Request $request post请求<br><br>
     * <b>post请求字段：</b>
     * * string        $date        筛选日期(必须的)
     *
     * @return view
     *
     * @version 0.6
     * @author wangjiang <wangjiang@misrobot.com>
     * @date 2015-12-4 14:52
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function postOpenlabHistoryList (Request $request)
    {    	
        $this->validate($request, [
            'date' 	=> 	'sometimes|date_format:Y-m-d',
        ]);

        $searchDate = $request->input('date');

        $where = [];
        if($searchDate)
        {
            $where['date'] = $searchDate;
        }

        $labHis = new ResourcesLabHistory();
        $labHisList = $labHis->getWechatList($where);
		
        return response()->json(                
            $this->success_rows(1, '获取成功', $labHisList->total(), config('msc.page_size',10), $labHisList->currentPage(), array('labHisList'=>$labHisList->toArray()))
        );
    }
    /**
     * 获得一条开放实验室历史使用记录详情
     * @method GET /msc/wechat/lab/openlab-history-item
     * @access public
     *
     * @param Request $request get请求<br><br>
     * <b>get请求字段：</b>
     * * int        $id        开放实验室历史使用记录编号
     *
     * @return view
     *
     * @version 0.6
     * @author wangjiang <wangjiang@misrobot.com>
     * @date 2015-12-4 16:14
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function getOpenlabHistoryItem ($id)
    {    	
        $id = intval($id);
        $labHis = new ResourcesLabHistory();
        $data =  $labHis->getWechatItem($id);
		return view('msc::wechat.resource.openlab_history_details',['data'=>$data]);
    }

}