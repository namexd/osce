<?php
/**
 * Created by PhpStorm.
 * User: CoffeeKizoku
 * Date: 2016/1/2
 * Time: 13:29
 */

namespace Modules\Osce\Http\Controllers\Admin;


use Modules\Osce\Entities\Area;
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
            'id' => 'sometimes|integer',
            'keyword' => 'sometimes',
            'type' => 'sometimes|integer'
        ]);
        //获取各字段
        $keyword = e($request->input('keyword', ''));
        $type = $request->input('type', 1);
        $id = $request->input('id', '');
        //获取当前场所的类
        list($area,$data) = $room->showRoomList($keyword, $type, $id);
//        dd($data);

        //展示页面
        if ($type == 1) {
            return view('osce::admin.resourcemanage.examroom', ['area' => $area, 'data' => $data,'type'=>$type]);
        } else if ($type == 2){
            return view('osce::admin.resourcemanage.central_control', ['area' => $area, 'data' => $data,'type'=>$type]);
        }else if ($type == 3){
            return view('osce::admin.resourcemanage.corridor', ['area' => $area, 'data' => $data,'type'=>$type]);
        }else{
            return view('osce::admin.resourcemanage.waiting', ['area' => $area, 'data' => $data,'type'=>$type]);
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

        $data = $model->showRoomList("",$type,$id);


        //将数据展示到页面
        return view('osce::admin.resourcemanage.examroom_edit', ['data' => $data]);
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

        $formData = $request->only('name', 'description');
        $id = $request->input('id');
        $Room = new Room();
        $result = $Room->updateData($id, $formData);

        try {
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
    public function getAddRoom()
    {
        return view('osce::admin.resourcemanage.examroom_add');
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
    public function postCreateRoom(Request $request, Room $room)
    {
        //验证
        $this->validate($request, [
            'name' => 'required',
            'nfc' => 'required',
            'address' => 'required',
            'code' => 'required',
            'description' => 'required'
        ]);
        $formData = $request->only('name', 'nfc', 'address', 'code', 'description');

        DB::connection('osce_mis')->beginTransaction();

        $result = $room->insertData($formData);
        if (!$result) {
            DB::connection('osce_mis')->rollBack();
            return redirect()->back()->withErrors('插入数据失败,请重试!');
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
            return json_encode($this->success_data(['删除成功！']));
        } catch (\Exception $ex) {
            DB::connection('osce_mis')->rollBack();
            return json_encode($this->fail($ex));
        }
    }


}