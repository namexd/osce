<?php
/**
 * Created by PhpStorm.
 * User: j5110
 * Date: 2015/12/28
 * Time: 13:46
 * 考站控制器
 */

namespace Modules\Osce\Http\Controllers\Admin;

use Modules\Osce\Entities\CaseModel;
use Modules\Osce\Entities\Place as Place;
use Modules\Osce\Entities\Room;
use Modules\Osce\Entities\Station;
use Modules\Osce\Entities\Subject;
use Modules\Osce\Entities\Vcr;
use Modules\Osce\Http\Controllers\CommonController;
use Illuminate\Http\Request;
use DB;

class StationController extends CommonController
{
    /**
     * 测试
     * /osce/admin/station/test
     */
    public function getTest()
    {

        //dd();
        return view('osce::admin.exammanage.score_query_detail');
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

        //搜索名字
        $name = e($request->get('name'));

        //拼凑一个order数组
        $order = [$orderType, $orderBy];
        //考站类型
        $placeCate = ['1' => '技能操作', '2' => '标准化病人(SP)', '3' => '理论考试'];

        //获得展示数据
        $data = $model->showList($order,  $ajax = false, $name);

        //将展示数据放在页面上
        return view('osce::admin.resourcemanage.test_station',['data' => $data, 'placeCate'=>$placeCate, 'name'=>$name]);

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


        list($placeCate, $vcr, $case, $room, $subject) = $this->dropDownList();

        //获得上次的时间限制
        $time = $request->session()->get('time',"");
        //将下拉菜单的数据传到页面上
        return view('osce::admin.resourcemanage.test_station_add',
            ['placeCate' => $placeCate, 'vcr' => $vcr, 'case' => $case, 'room' => $room, 'subject' => $subject, 'time' => $time]);
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
        $this->validate($request, [
            'name'          => 'required|unique:osce_mis.station,name',
            'type'          => 'required|integer',
//            'description'   => 'required',
//            'code'          => 'required',
            'mins'          => 'required',
            'vcr_id'        => 'required|integer',
            'room_id'       => 'required|integer',
            'case_id'       => 'required|integer',
            'subject_id'    => 'required|integer'
        ],[
            'name.unique'   =>  '考站名称必须唯一'
        ]);

        DB::connection('osce_mis')->beginTransaction();
        try {
            //处理相应信息,将$request中的数据分配到各个数组中,待插入各表
            $stationData = $request->only('name', 'type', 'mins', 'subject_id');
            $vcrId  = $request->input('vcr_id');
            $caseId = $request->input('case_id');
            $roomId = $request->input('room_id');

            //将参数放进一个数组中，方便传送
            $formData = [$stationData, $vcrId, $caseId, $roomId];

            //将当前时间限定的值放入session
            $time = $request->input('mins');
            $request->session()->flash('time', $time);
            if (!($request->session()->has('time'))) {
                throw new \Exception('未能将时间保存！');
            }

            if (!($model->addStation($formData))) {
                throw new \Exception('未能将考站保存！');
            };

            DB::connection('osce_mis')->commit();
            return redirect()->route('osce.admin.Station.getStationList'); //返回考场列表

        } catch (\Exception $ex) {
            DB::connection('osce_mis')->rollBack();
            return redirect()->back()->withErrors($ex->getMessage());
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
        list($placeCate, $vcr, $case, $room, $subject) = $this->dropDownList($id);
        //将下拉菜单的数据传到页面上
        return view('osce::admin.resourcemanage.test_station_edit',
            [   'placeCate' => $placeCate, 'vcr' => $vcr, 'case' => $case,
                'room' => $room, 'subject' => $subject, 'rollmsg' => $rollMsg
            ]);
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
        $this->validate($request, [
            'id'            => 'required|integer',
            'name'          => 'required',
            'type'          => 'required|integer',
            'mins'          => 'required|integer',
            'subject_id'    => 'required|integer',
//            'description'   => 'required',
//            'code'          => 'required',
            'vcr_id'        => 'required|integer',
            'case_id'       => 'required|integer',
            'room_id'       => 'required|integer',
        ]);

        try {
            //处理相应信息,将$request中的数据分配到各个数组中,待插入各表
            $placeData = $request->only('name', 'type', 'subject_id', 'mins');
            $vcrId  = $request->input('vcr_id');
            $caseId = $request->input('case_id');
            $roomId = $request->input('room_id');
            $id     = $request->input('id');
            $formData = [$placeData, $vcrId, $caseId, $roomId];

            $model->updateStation($formData, $id);

            return redirect()->route('osce.admin.Station.getStationList'); //返回考场列表

        } catch (\Exception $ex) {
            return redirect()->back()->withErrors($ex->getMessage());
        }
    }

    /**
     * 考场删除的业务逻辑
     * @api       POST /osce/admin/station/delete
     * @access    public
     * @param Request $request post请求<br><br>
     * @param Station $station
     * @return view
     * @internal param Station $model Station
     * @version   1.0
     * @author    jiangzhiheng <jiangzhiheng@misrobot.com>
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function postDelete(Request $request, Station $station)
    {
        try {
            //获取删除的id
            $id = $request->input('id');
            if (!$id) {
                throw new \Exception('没有该考站！');
            }
            //将id传入删除的方法
            $result = $station->deleteData($id);
            if($result) {
                return $this->success_data(['删除成功！']);
            }
        } catch (\Exception $ex) {
            return $this->fail($ex);
        }
    }

    /**
     * 下拉菜单，单独封装成了一个方法
     * $id为考场id
     * @param string $id
     * @return array
     */
    private function dropDownList($id = "")
    {
        //将下拉菜单的数据查出
        $placeCate = ['1' => '技能操作', '2' => '标准化病人(SP)', '3' => '理论考试']; //考站类型
        if ($id == "") {
            $vcr = Vcr::where('status', 1)
                ->select(['id', 'name'])
                ->get();     //关联摄像机
        } else {
            //根据station的id找到对应的vcr的id
            $vcrId = Station::findOrFail($id)->vcrStation()->select('vcr.id as id')->first()->id;

            $vcr  = Vcr::where('status', 1)
                    ->orWhere(function($query) use($vcrId){
                        $query->where('id','=',$vcrId);
                    })
                    ->select(['id', 'name'])
                    ->get();     //关联摄像机
        }
        $case   = CaseModel::all(['id', 'name']);
        $room   = Room::all(['id', 'name']);        //房间
        $subject= Subject::all(['id', 'title']);
//        dd($subject);


        return array($placeCate, $vcr, $case, $room, $subject);  //评分标准
    }
}