<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2015/12/7 0007
 * Time: 14:10
 */
namespace Modules\Msc\Http\Controllers\Admin;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Modules\Msc\Entities\ResourcesDeviceApply;
use Modules\Msc\Entities\ResourcesDevicePlan;
use Modules\Msc\Http\Controllers\MscController;
use Modules\Msc\Entities\ResourcesClassroom;
use Modules\Msc\Entities\ResourcesClassroomApply;
use Modules\Msc\Entities\ResourcesClassroomCourses;
use Modules\Msc\Entities\ResourcesClassroomPlan;
use Modules\Msc\Entities\ResourcesLabHistory;
use Modules\Msc\Entities\ResourcesLabApply;
use Modules\Msc\Entities\Groups;
use Modules\Msc\Entities\ResourcesTools;
use Modules\Msc\Entities\ResourcesToolsCate;
use Modules\Msc\Entities\ResourcesToolsItems;
use Modules\Msc\Entities\ResourcesDeviceHistory;
use App\Entities\User;
use Illuminate\Support\Facades\Auth;

class LabToolsController extends MscController
{
    /**
     * 开放实验室设备未审核列表
     * @api       GET /msc/admin/lab-tools/open-lab-tools-apply-list
     * @access    public
     * @param Request $request get请求<br><br>
     *                         <b>get请求字段：</b>
     *                         * string        date            搜索日期
     *                         * string        keyword        关键字
     *                         * string        order_name      排序字段名 e.g 1:预约人,2:设备状态
     *                         * string        order_type      排序方式 枚举 e.g:desc,asc
     *                         * int           page            页码
     * @return view
     * @author    jiangzhiheng <jiangzhiheng@misrobot.com>
     * @date      2015-12-03 13:36
     * @version   1.0
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function getOpenLabToolsApplyList(Request $request)
    {
        //验证传入的数据
        $this->validate($request, [
            'date' => 'sometimes|date_format:Y-m-d',
            'keyword' => 'sometimes',
            'order' => 'sometimes',
            'orderby' => 'sometimes',
        ]);
        //处理各个字段的值
        $keyword = e(urldecode($request->get('keyword')));
        $date = $request->get('date');
        $date = empty($date) ? date('Y-m-d') : $date;
        $orderName = e(urldecode($request->get('order_name')));
        $orderType = e(urldecode($request->get('order_type')));
        $orderName = empty($orderName) ? 'created_at' : $orderName;
        $orderType = empty($orderType) ? 'desc' : $orderType;
        //设置回显信息
        $rollMsg = [];
        $rollMsg[0] = $date; //日期的回显
        if ($keyword !== '') {
            $rollMsg[1] = $keyword;  //关键词的回显
        }
        //处理$orderName
        switch ($orderName) {
            case 1:
                $orderName = 'student.name';
                break;
            case 2:
                $orderName = 'resources_device_history.result_health';
                break;
            default:
                $orderName = 'resources_device_apply.created_at';
        }

        //将排序字段名和排序类型合并为一个数组
        $order = [
            $orderName,
            $orderType
        ];
        $ResourcesDeviceApply = new ResourcesDeviceApply();
        $list                 = $ResourcesDeviceApply->getWaitToolsList ($keyword , $date , $order);
        return view ('msc::admin.open_equipment_manage.openequ_audit' , [ 'data' => $list , 'rollmsg' => $rollMsg]);
    }

    /**
     * 开放实验室设备使用历史
     * @api       GET /msc/admin/lab-tools/open-lab-tools-use-history
     * @access    public
     * @param Request $request get请求<br><br>
     *                         <b>get请求字段：</b>
     *                         * string        date            搜索日期
     *                         * string        keyword        关键字
     *                         * string        order_name      排序字段名 枚举 e.g 1:设备名称 2:预约人 3:是否复位状态自检 4:是否复位设备
     *                         * string        order_type      排序方式 枚举 e.g:desc,asc
     *                         * int           page            页码
     * @return view
     * @version   1.0
     * @author    jiangzhiheng <jiangzhiheng@misrobot.com>
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function getOpenLabToolsUseHistory(Request $request)
    {
        //验证各个字段的值
        $this->validate($request, [
            'keyword' => 'sometimes',
            'date' => 'sometimes|date_format:Y-m-d',
            'order_name' => 'sometimes',
            'order_type' => 'sometimes',
        ]);
        //处理数据
        $keyword = e(urldecode($request->get('keyword')));
        $date = $request->get('date');
        $date = empty($date) ? date('Y-m-d') : $date;
        $orderName = e(urldecode($request->get('order_name')));
        $orderName = empty($orderName) ? 'created_at' : $orderName;
        $orderType = e(urldecode($request->get('order_type')));
        $orderType = empty($orderType) ? 'desc' : $orderType;

        //设置回显信息
        $rollMsg = [];
        $rollMsg[0] = $date; //日期的回显
        if ($keyword !== '') {
            $rollMsg[1] = $keyword;  //关键词的回显
        }
        //处理$orderName字段
        switch ($orderName) {
            case 1:
                $orderName = 'resources_device.name';
                break;
            case 2:
                $orderName = 'student.name';
                break;
            case 3:
                $orderName = 'resources_device_history.result_health';
                break;
            case 4:
                $orderName = 'resources_device_history.result_init';
                break;
            default:
                $orderName = 'resources_device_apply.created_at';
        }

        //将排序的字段名合并为一个数组
        $order = [
            $orderName,
            $orderType
        ];

        //实例化模型,进行方法调用
            $ResourcesDeviceHistory = new ResourcesDeviceHistory();
            $data = $ResourcesDeviceHistory->getDeviceReserveHistoryList($keyword, $date, $order);
//        dd($data->get(1));
            return view('msc::admin.open_equipment_manage.openequip_order_history', ['pagination' => $data , 'rollmsg'=>$rollMsg]);

    }

    /**
     * 查看开放实验室设备使用历史
     * @api       GET /msc/admin/lab/open-lab-tools-use-history-view
     * @access    public
     * @param $id
     * @return view
     * @internal  param Request $request get请求<br><br>
     *                         <b>get请求字段：</b>
     *                         * int           id            设备申请的id
     * @author    jiangzhiheng <jiangzhiheng@misrobot.com>
     * @version   1.0
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function getOpenLabToolsUseHistoryView(Request $request)
    {
        $this->validate($request,[
            'id' => 'required|integer',
        ]);

        $id = $request->get('id');
        //把主键id传入模型的具体方法中,得到具体的数据
        $ResourcesDeviceHistory = new ResourcesDeviceHistory();

        $data = $ResourcesDeviceHistory->viewDeviceReserveHistoryList($id);
        return view('msc::admin.open_equipment_manage.openequip_order_history_detail', ['data' => $data]);
    }

    /**
     * 开放实验室已预约设备
     * @api       GET /msc/admin/lab-tools/open-lab-tools-examined-list
     * @access    public
     * @param Request $request get请求<br><br>
     *<b>get请求字段：</b>
     * * string        date            搜索日期
     * * string        keyword         关键字
     * * string        order_name      排序字段名 枚举 e.g:1:设备名称 2:预约人 3:是否复位自检 4:是否复位
     * * string        order_type      排序方式 枚举 e.g:desc,asc
     * * int           page            页码
     * @return view
     * @version   1.0
     * @author    jiangzhiheng <jiangzhiheng@misrobot.com>
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function getOpenLabToolsExaminedList(Request $request)
    {
        $this->validate($request, [
            'keyword' => 'sometimes',
            'date' => 'sometimes|date_format:Y-m-d',
            'order_name' => 'sometimes',
            'order_type' => 'sometimes',
        ]);

        //处理数据
        $keyword = e(urldecode($request->get('keyword')));
        $date = $request->get('date');
        $date = empty($date) ? date('Y-m-d') : $date;
        $orderName = e(urldecode($request->get('order_name')));
        $orderType = e(urldecode($request->get('order_type')));
        $orderName = empty($orderName) ? '5' : $orderName;
        $orderType = empty($orderType) ? 'desc' : $orderType;

        //设置回显信息
        $rollMsg = ['',''];
        $rollMsg[0] = $date; //日期的回显
        if ($keyword !== '') {
            $rollMsg[1] = $keyword;  //关键词的回显
        }

        //处理$orderName字段
        switch ($orderName) {
            case 1:
                $orderName = 'resources_device.name';
                break;
            case 2:
                $orderName = 'student.name';
                break;
            default:
                $orderName = 'resources_device_apply.created_at';
        }
        //将order合并为一个数组
        $order = [
            $orderName,
            $orderType
        ];

        //获取设备最后的状态

        //调用方法
        $ResourcesDeviceApply = new ResourcesDeviceApply();
        $list                 = $ResourcesDeviceApply->getToolsExaminedList ($keyword,$date,$order);
        return view ('msc::admin.open_equipment_manage.openequ_audited' , [ 'pagination' => $list ,'rollmsg' => $rollMsg]);
    }

    /**
     * 向用户发送紧急通知
     * @api POST /msc/admin/lab-tools/open-lab-tools-urgent-notice
     * @access public
     *
     * @param Request $request post请求<br><br>
     * <b>post请求字段：</b>
     * * int            id              申请ID(必须的)
     * * string         reject          拒绝描述(必须的)
     *
     * @return json {'id':'变更的成功的申请ID'}
     * @author jiangzhiheng<jiangzhiheng@misrobot.com>
     * @version 1.0
     * @date 2015-12-09 10:42
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     *
     */
    public function postOpenLabToolsUrgentNotice (Request $request) {
        $this->validate($request,[
            'id'        => 'required|numeric',
            'reject'    => 'required',
        ]);
        //获取经过AJAX传递过来的值
        $id = $request  ->  get('id'); //此id为设备申请表的主键id
        $reject = e($request->get('reject'));
        try {
            //获取openid
            $apply=ResourcesDeviceApply::find($id);
            $openID = $apply    ->    applyer    ->    openid;
            //发送微信消息
            $result = $this->sendMsg($reject,$openID);
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

    //向微信用户发送普通文本消息
    private function sendMsg($msg,$openid) {
        if (empty($openid)) {
            throw new \Exception('没有找到用户的微信OpenID');
        }

        $userService = new \Overtrue\Wechat\Staff(config('wechat.app_id'),config('wechat.secret'));
        return $userService->send($msg)->to($openid);
    }

    /**
     * 开放设备预约使用历史 统计  着陆页面
     * @api       GET /msc/admin/lab-tools/history-statistics
     * @access    public
     * @param Request $request get请求<br><br>
     * @return view
     * @version   1.0
     * @author    Luohaihua <Luohaihua@misrobot.com>
     * @date      2015-12-07 14:26
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function getHistoryStatistics()
    {
        return view('msc::admin.open_equipment_manage.openequip_order_analyse');
    }

    /**
     *  开放设备预约使用历史 统计  异步获取数据
     * @api       GET /msc/admin/lab-tools/history-statistics-data
     * @access    public
     * @param Request $request get请求<br><br>
     * <b>get请求字段：</b>
     * * int        grade            年级
     * * date       date             归还日期
     * * int        status           归还状态
     * * int        professional     专业id
     * @return json {borrowCount:数量，name:设备名称，status:状态}
     * @version   1.0
     * @author    Luohaihua <Luohaihua@misrobot.com>
     * @date      2015-12-07 14:38
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function getHistoryStatisticsData(Request $request)
    {
        $total = [
            [
                'borrowCount' => 3,
                'name' => '开放设备a',
                'status' => '损坏',
            ],
            [
                'borrowCount' => 3,
                'name' => '开放设备a',
                'status' => '损坏',
            ],
            [
                'borrowCount' => 3,
                'name' => '开放设备a',
                'status' => '损坏',
            ],
        ];
        $resourcesDevicePlan    =   new ResourcesDevicePlan();
        $total  =   $resourcesDevicePlan    ->  historyStatistics();
        $totalData  =[];
        foreach($total as $row)
        {
            $rowData                        =   [];
            $rowData['borrowCount']         =   $row['borrowCount'];
            $rowData['name']                =  $row->device->name;
            $rowData['status']              =   $row->status;
            $totalData[]                    =   $rowData;
        }
        return response()->json(
            $this->success_rows(1, '获取成功', count($totalData), count($totalData), 1, $totalData)
        );
    }
    /**
     *  开放设备预约使用历史 统计  文件导出
     * @api  GET /msc/admin/lab-tools/history-statistics-excl
     * @access    public
     * @param Request $request get请求<br><br>
     * <b>get请求字段：</b>
     * * int        grade            年级
     * * date       date             归还日期
     * * int        status           归还状态
     * * int        professional     专业id
     * @return json {borrowCount:数量，name:设备名称，status:状态}
     * @version   1.0
     * @author    Luohaihua <Luohaihua@misrobot.com>
     * @date      2015-12-07 14:38
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function getHistoryStatisticsExcl(Request $request){
        $grade          =   $request    ->  get('grade');
        $date           =   $request    ->  get('date');
        $status         =   $request    ->  get('status');
        $professional   =   $request    ->  get('professional');

        $resourcesDevicePlan    =   new ResourcesDevicePlan();
        $total  =   $resourcesDevicePlan    ->  historyStatistics($grade,$date,$status,$professional);
        $str=iconv('utf-8','gb2312','名称,数量,状态')."\n";
        if(empty(count($total)))
        {
            $str .=iconv('utf-8','gb2312','无,无,无')."\n";
        }
        else
        {
            foreach($total as $row)
            {
                $count  = iconv('utf-8','gb2312',$row['borrowCount']); //中文转码
                $name   = iconv('utf-8','gb2312',$row->device->name);
                $status = iconv('utf-8','gb2312',$row->status);
                $str .= $name.",".$count.",".$status."\n"; //用引文逗号分开
            }
        }
        $filename = date('Ymd').'.csv'; //设置文件名
        $this->export_csv($filename,$str); //导出
    }
    /**
     * 审核通过/拒绝开放实验室 设备 的申请
     * @api POST /msc/admin/lab-tools/change-open-lab-tools-apply-status
     * @access    public
     * @param Request $request post请求<br><br>
     * <b>post请求字段：</b>
     * * int            id              申请ID(必须的)
     * * string         status          要变更的状态(必须的)
     * * string         reject          拒接描述(必须的)
     * @return json {'id':'变更的成功的申请ID'}
     * @version   1.0
     * @author    Luohaihua <Luohaihua@misrobot.com>
     * @date      2015-12-03 16:08
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function postChangeOpenLabToolsApplyStatus(Request $request)
    {

        $this->validate($request, [
            'id' => 'required|integer',
            'status' => 'required|in:1,2',
            'reject' => 'sometimes',

        ]);
        $id = $request->get('id');
        $status = $request->get('status');
        $reject = $request->get('reject');
        $ResourcesDeviceApply       =   new ResourcesDeviceApply();
        try {
			if($status==1)
			{
				$result = $ResourcesDeviceApply->agreeApply($id);
			}
			else
			{
				$result = $ResourcesDeviceApply->refundApply($id,$reject);
			}
            if ($result) {
                return response()->json(
                    $this->success_data(['id' => $result])
                );
            } else {
                return response()->json($this->fail(new \Exception('审核失败')));
            }
        } catch (\Exception $ex) {
            return response()->json($this->fail($ex));
        }
    }
    private function export_csv($filename,$data){
        header("Content-type:text/csv");
        header("Content-Disposition:attachment;filename=".$filename);
        header('Cache-Control:must-revalidate,post-check=0,pre-check=0');
        header('Expires:0');
        header('Pragma:public');
        echo $data;
    }
}
