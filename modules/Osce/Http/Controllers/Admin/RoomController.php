<?php
/**
 * Created by PhpStorm.
 * User: CoffeeKizoku
 * Date: 2016/1/2
 * Time: 13:29
 */

namespace Modules\Osce\Http\Controllers\Admin;


use Modules\Osce\Entities\Area;
use Modules\Osce\Entities\RoomVcr;
use Modules\Osce\Entities\Vcr;
use Modules\Osce\Http\Controllers\CommonController;
use Illuminate\Http\Request;
use Modules\Osce\Entities\Room as Room;
use DB;

class RoomController extends CommonController
{
    /**
     * 获取房间列表,根据场所类来查找
     * @api       GET /osce/admin/room/room-list
     * @access    public
     * @param Request $request get请求<br><br>
     *                         <b>get请求字段：</b>
     *                         string        keyword         关键字
     *                         int           type            类型
     *                         int           id              房间的id
     * @return view
     * @version   2.0
     * @author    jiangzhiheng <jiangzhiheng@misrobot.com>
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function getRoomList(Request $request, Room $room)
    {
        //验证规则，暂时留空
        $this->validate($request,[
            'id'        => 'sometimes|integer',
            'type'      => 'sometimes|integer',
            'keyword'   => 'sometimes'
        ]);

        //获取各字段
        $keyword = e($request->input('keyword', ''));
        $type    = $request ->input('type', 1);
        $id      = $request ->input('id', '');

        try{
            //获取当前场所的类
            list($area,$data) = $room->showRoomList($keyword, $type, $id);

            //展示页面
            if ($type == 1) {
                return view('osce::admin.resourcemanage.examroom', ['area' => $area, 'data' => $data,'type'=>$type,'keyword'=>$keyword]);
            } else if ($type == 2){
                return view('osce::admin.resourcemanage.central_control', ['area' => $area, 'data' => $data,'type'=>$type,'keyword'=>$keyword]);
            }else if ($type == 3){
                return view('osce::admin.resourcemanage.corridor', ['area' => $area, 'data' => $data,'type'=>$type,'keyword'=>$keyword]);
            }else{
                return view('osce::admin.resourcemanage.waiting', ['area' => $area, 'data' => $data,'type'=>$type,'keyword'=>$keyword]);
            }
        } catch(\Exception $ex){
            return redirect()->back()->withErrors($ex->getMessage());
        }


    }

    /**
     * 修改房间页面的着陆页
     * @api       GET /osce/admin/room/edit-room
     * @access    public
     * @param Request $request post请求<br><br>
     *                         <b>get请求字段：</b>
     *                         array           id            主键ID
     * @return view
     * @version   1.0
     * @author    jiangzhiheng <jiangzhiheng@misrobot.com>
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */

    public function getEditRoom(Request $request, Room $model)
    {
        //验证ID
        $this->validate($request, [
            'id' => 'required|integer',
            'type' => 'required|integer',
        ]);

        //取出id的值
        $id = $request->get('id');
        $type = $request->input('type');
        //TODO:zhoufuxiang，查询属于离线状态的摄像机
        $vcr = Vcr::where('status', 0)
            ->select(['id', 'name'])
            ->get();     //关联摄像机

        $data = $model->showRoomList("",$type,$id);
        $roomVcr = RoomVcr::where('room_id', $id)->first();
        if(!empty($roomVcr)){
            $data->vcr_id = $roomVcr->vcr_id;
        }else{
            $data->vcr_id = 0;
        }

        //将数据展示到页面
        return view('osce::admin.resourcemanage.examroom_edit', ['data' => $data, 'vcr'=>$vcr]);
    }

    /**
     * 添加摄像机房间页面的着陆页
     * @api       GET /osce/admin/room/edit-room
     * @access    public
     * @param Request $request post请求<br><br>
     *                         <b>get请求字段：</b>
     *                         array           id            主键ID
     * @return view
     * @version   1.0
     * @author    jiangzhiheng <jiangzhiheng@misrobot.com>
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function getAddVcr(Request $request, Room $room)
    {
        //获取摄像头数据
        $data = Vcr::where('status','<>',0)->get();
        return view('osce::admin.resourcemanage.central_control_add',['data'=>$data]);
    }

    /**
     * 修改房间页面 业务处理
     * @api       POST /osce/admin/room/edit-room
     * @access    public
     * @param Request $request post请求<br><br>
     *                         <b>get请求字段：</b>
     *                         array           id            主键ID
     * @return view
     * @version   1.0
     * @author    jiangzhiheng <jiangzhiheng@misrobot.com>
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function postEditRoom(Request $request)
    {
        //验证数据，暂时省略
        $this->validate($request, [
            'id' => 'required|integer',
            'name' => 'required',
            'description' => 'required'
        ]);

        $id         = $request->input('id');
        $vcr_id     = $request->get('vcr_id');
        $formData   = $request->only('name', 'description', 'address', 'code');

        try {
            $Room = new Room();
            $result = $Room->editRoomData($id, $vcr_id, $formData);
//            $result = $Room->updateData($id, $formData);
            if (!$result) {
                throw new \Exception('数据修改失败！请重试');
            } else {
                return redirect()->route('osce.admin.room.getRoomList');
            }
        } catch (\Exception $ex) {
            return redirect()->back()->withErrors($ex->getMessage());
        }

    }

    /**
     * 添加着陆页
     * @api       get /osce/admin/room/get-room
     * @access    public
     * @param Request $request post请求<br><br>
     *                         <b>get请求字段：</b>
     *                         array           id            主键ID
     * @return view
     * @version   1.0
     * @author    jiangzhiheng <jiangzhiheng@misrobot.com>
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function getAddRoom($id="")
    {
        if ($id == "") {
            //TODO:zhoufuxiang，查询属于离线状态的摄像机
            $vcr = Vcr::where('status', 0)
                ->select(['id', 'name'])
                ->get();     //关联摄像机
        } else {
            //根据station的id找到对应的vcr的id
            $vcrId = Room::findOrFail($id)->vcrStation()->select('vcr.id as id')->first()->id;
            //TODO:zhoufuxiang，查询属于离线状态的摄像机
            $vcr  = Vcr::where('status', 0)
                ->orWhere(function($query) use($vcrId){
                    $query->where('id','=',$vcrId);
                })
                ->select(['id', 'name'])
                ->get();     //关联摄像机
        }


        return view('osce::admin.resourcemanage.examroom_add',[
            'vcr' =>$vcr,
        ]);
    }

    /**
     * 往room表新插入一行数据
     * @api       POST /osce/admin/room/create-room
     * @access    public
     * @param Request $request post请求<br><br>
     *                         <b>get请求字段：</b>
     *                         array           id            主键ID
     *                         array          status         状态
     * @return view
     * @version   1.0
     * @author    jiangzhiheng <jiangzhiheng@misrobot.com>
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function postCreateRoom(Request $request, RoomVcr $roomVcr)
    {
        //验证
        $this->validate($request, [
            'vcr_id'        => 'required',
            'name'  => 'required|unique:osce_mis.room,name',
//            'nfc' => 'required',
            'address' => 'required',
            'code' => 'required',
            'description' => 'required'
        ],[
            'name.unique'   =>  '名称必须唯一',
        ]);
//        $formData = $request->only('name', 'nfc', 'address', 'code', 'description');
        //todo   表单内容变化没有提交nfc字段
        $formData = $request->only('name', 'address', 'code', 'description');
        $vcrId =$request->get('vcr_id');

        DB::connection('osce_mis')->beginTransaction();
        $roomSave =DB::connection('osce_mis')->table('room')->insertGetId($formData);

        //  todo   摄像机是可以多选的  待完善。。。。。。。。。
//        $vcrId= serialize($vcrId);
        $data=[
            'room_id'=>$roomSave,
            'vcr_id'=>$vcrId,
        ];
        $result = $roomVcr->insertData($data);
        if (!$result) {
            DB::connection('osce_mis')->rollBack();
            return redirect()->back()->withErrors('插入数据失败,请重试!');
        }
        $vcr = Vcr::where('id',$vcrId)->update(['status'=>1]);
        if (!$vcr) {
            DB::connection('osce_mis')->rollBack();
            return redirect()->back()->withErrors('摄像机状态修改失败,请重试!');
        }

        DB::connection('osce_mis')->commit();
        return redirect()->route('osce.admin.room.getRoomList');
    }

    /**
     * 往room表删除数据
     * @api       POST /osce/admin/room/delete
     * @access    public
     * @param Request $request post请求<br><br>
     *                         <b>get请求字段：</b>
     *                         array           id            主键ID
     * @param Room $room
     * @return view
     * @version   1.0
     * @author    jiangzhiheng <jiangzhiheng@misrobot.com>
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function postDelete(Request $request, Room $room)
    {
        try {
            //验证略
            $this->validate($request, [
                'id' => 'required|integer'
            ]);
            DB::connection('osce_mis')->beginTransaction();
            $id = $request->input('id');
            if (!$id) {
                throw new \Exception('没有该房间！');
            }

            $result = $room->deleteData($id);
            if (!$result) {
                throw new \Exception('系统错误，请重试！');
            }

            DB::connection('osce_mis')->commit();
            return $this->success_data(['删除成功！']);
        } catch (\Exception $ex) {
            DB::connection('osce_mis')->rollBack();
            return $this->fail($ex);
        }
    }


}