<?php
/**
 * Created by PhpStorm.
 * User: j5110
 * Date: 2015/12/28
 * Time: 13:46
 * 考站控制器
 */

namespace Modules\Osce\Http\Controllers\Admin;

use Modules\Osce\Entities\Place as Place;
use Modules\Osce\Entities\Room;
use Modules\Osce\Entities\Station;
use Modules\Osce\Entities\Subject;
use Modules\Osce\Entities\Vcr;
use Modules\Osce\Http\Controllers\CommonController;
use Illuminate\Http\Request;

class StationController extends CommonController
{
    /**
     * 测试
     * /osce/admin/station/test
     */
    public function getTest()
    {

        //dd();
        return view('osce::admin.exammanage.examroom_assignment');
    }

    /**
     * 考场列表的着陆页
     * @api       GET /osce/admin/station/station-list
     * @access    public
     * @param Request $request get请求<br><br>
     * @param Station $model
     * @return view
     * @version   1.0
     * @author    jiangzhiheng <jiangzhiheng@misrobot.com>
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function getStationList(Request $request, Station $model)
    {
        //验证略

        //传入排序
        $orderType = empty(config('order_type')) ? 'created_at' : config('order_type');
        $orderBy = empty(config('order_by')) ? 'desc' : config('order_by');

        //拼凑一个order数组
        $order = [$orderType, $orderBy];

        //获得展示数据
        $data = $model->showList($order);

        //将展示数据放在页面上
        return view('osce::admin.resourcemanage.test_station',
            ['data' => $data]);

    }

    /**
     * 考场新增的着陆页
     * @api       GET /osce/admin/station/add-station
     * @access    public
     * @param Request $request get请求<br><br>
     * @return view
     * @version   1.0
     * @author    jiangzhiheng <jiangzhiheng@misrobot.com>
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function getAddStation(Request $request)
    {
        //验证略


        list($placeCate, $vcr, $room, $subject) = $this->dropDownList();


        //获得上次的时间限制
        $time = $request->session()->get('time', '');

        //将下拉菜单的数据传到页面上
        return view('osce::admin.resourcemanage.test_station_add',
            ['placeCate' => $placeCate, 'vcr' => $vcr, 'room' => $room, 'subject' => $subject, 'time' => $time]);
    }

    /**
     * 考场新增的业务逻辑
     * @api       POST /osce/admin/station/add-station
     * @access    public
     * @param Request $request get请求<br><br>
     * @param Station $model
     * @return view
     * @throws \Exceptio
     * @version   1.0
     * @author    jiangzhiheng <jiangzhiheng@misrobot.com>
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function postAddStation(Request $request, Station $model)
    {
        //验证略
        try {
            //处理相应信息,将$request中的数据分配到各个数组中,待插入各表
            $placeData = $request->only('name', 'type', 'mins', 'room_id', 'create_user_id', 'subject_id');
            $vcrId = $request->only('vcr_id', 'create_user_id');
            $caseId = $request->only('case_id', 'create_user_id');

            $formData = [$placeData, $vcrId, $caseId];

            dd($formData);
            $result = $model->addStation($formData);

            //将当前时间限定的值放入session
            $time = $request->input('time');
            $request->session()->flash('time', $time);

            if (!$result) {
                throw new \Exception('新建考场失败,请重试!');
            } else {
                return redirect()->route('osce::admin.resourcemanage.examroom'); //返回考场列表
            }
        } catch (\Exception $ex) {
            return redirect()->back()->withInput()->withErrors($ex);
        }
    }

    /**
     * 考场编辑的着陆页
     * @api       GET /osce/admin/station/add-station
     * @access    public
     * @param Request $request get请求<br><br>
     * @param Station $model
     * @return view
     * @version   1.0
     * @author    jiangzhiheng <jiangzhiheng@misrobot.com>
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function getEditStation(Request $request, Station $model)
    {
        $this->validate($request, [
            'id' => 'required|integer'
        ]);

        //获取id
        $id = $request->input('id');
        //获取编辑考场的数据
        $rollMsg = $model->rollmsg($id);

        list($placeCate, $vcr, $room, $subject) = $this->dropDownList();
        //将下拉菜单的数据传到页面上
        return view('osce::admin.resourcemanage.test_station_edit',
            ['placeCate' => $placeCate, 'vcr' => $vcr, 'room' => $room, 'subject' => $subject, 'rollmsg' => $rollMsg]);

    }

    /**
     * 考场编辑的业务逻辑
     * @api       POST /osce/admin/station/edit-station
     * @access    public
     * @param Request $request get请求<br><br>
     * @param Station $model Station
     * @return view
     * @version   1.0
     * @author    jiangzhiheng <jiangzhiheng@misrobot.com>
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function postEditStation(Request $request, Station $model)
    {
        //验证数据，暂时省略

        try {
            //处理相应信息,将$request中的数据分配到各个数组中,待插入各表
            $placeData = $request->only('name', 'type', 'time', 'room_id', 'create_user_id', 'subject_id');
            $vcrId = $request->only('vcr_id', 'create_user_id');
            $caseId = $request->only('case_id', 'create_user_id');
            $id = $request->input('id');

            $formData = [$placeData, $vcrId, $caseId];
            $result = $model->updateStation($formData, $id);


            if (!$result) {
                throw new \Exception('编辑考场失败,请重试!');
            } else {
                return redirect()->route(''); //返回考场列表
            }
        } catch (\Exception $ex) {
            return redirect()->back()->withInput()->withErrors($ex);
        }
    }

    public function getDelete(Request $request, Station $station)
    {
        try {
            //获取删除的id
            $id = $request->input('id');
            //将id传入删除的方法
            $result = $station->deleteData($id);
            if ($result) {
                return redirect()->route('osce::admin.resourcemanage.test_station');
            }
        } catch (\Exception $ex) {
            return redirect()->back()->withErrors($ex);
        }
    }

    /**
     * 下拉菜单，单独封装成了一个方法
     * @return array
     */
    private function dropDownList()
    {
        //将下拉菜单的数据查出
        $placeCate = ['0' => '请选择类别', '1' => '技能操作', '2' => '标准化病人(SP)', '3' => '理论考试']; //考站类型
        $vcr = Vcr::where('status', 1)
            ->select(['id', 'name'])
            ->get();     //关联摄像机
        $room = Room::all(['id', 'name']);  //房间
        $subject = Subject::all(['id', 'title']);
        return array($placeCate, $vcr, $room, $subject);  //评分标准
    }
}