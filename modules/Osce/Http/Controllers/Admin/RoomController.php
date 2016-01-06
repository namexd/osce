<?php
/**
 * Created by PhpStorm.
 * User: CoffeeKizoku
 * Date: 2016/1/2
 * Time: 13:29
 */

namespace Modules\Osce\Http\Controllers\Admin;


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
     *                         string        order_name      排序字段名 枚举 e.g 1:设备名称 2:预约人 3:是否复位状态自检 4:是否复位设备
     *                         string        order_by        排序方式 枚举 e.g:desc,asc
     * @return view
     * @version   1.0
     * @author    jiangzhiheng <jiangzhiheng@misrobot.com>
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function getRoomList(Request $request)
    {
        //验证规则，暂时留空

        //获取各字段
        $formData = $request->only('keyword');
        //获取当前场所的类
        $model = new Room();
        $data = $model->showRoomList($formData);
        //将创建人插入$data对象

        foreach ($data as $item) {
            $item['creater'] = empty($model->creater()) ? '-' : $model->find($item['id'])->creater->name;
        }


        //展示页面
        return view('osce::admin.resourcemanage.examroom', ['data' => $data]);
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
            'id' => 'required|integer'
        ]);

        //取出id的值
        $formData = $request->only('id');

        $data = $model->showRoomList($formData);

        //拼装创建者
        $data['creater'] = empty($model->creater()) ? '-' : $model->find($data['id'])->creater->name;
        //将数据展示到页面
//        dd($data);
        return view('osce::admin.resourcemanage.examroom_edit', ['data' => $data]);
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

        $formData = $request->only('name', 'nfc', 'address', 'code', 'create_user_id');
        $id = $request->input('id');
        dd($formData);
        $Room = new Room();
        $result = $Room->updateData($id, $formData);

        try {
            if (!$result) {
                throw new \Exception('数据修改失败！请重试');
            } else {
                return redirect()->route('osce.admin.Room.getRoomList');
            }
        } catch (\Exception $ex) {
            return redirect()->back()->withErrors($ex);
        }

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
        $formData = $request->only('name', 'nfc', 'address', 'code', 'create_user_id');

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
     * @return view
     * @version   1.0
     * @author    jiangzhiheng <jiangzhiheng@misrobot.com>
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function postDelete(Request $request, Room $room)
    {
        //验证略

        $id = $request->input('id');

        DB::connection('osce_mis')->beginTransaction();
        $result = $room->deleteData($id);
        if (!$result) {
            DB::connection('osce_mis')->rollBack();
            return redirect()->back()->withErrors('系统异常');
        }

        DB::connection('msc_mis')->commit();
        return redirect()->route('osce.admin.Room.getRoomList');
    }


}