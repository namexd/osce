<?php
/**
 * Created by PhpStorm.
 * User: CoffeeKizoku
 * Date: 2016/1/3
 * Time: 10:09
 */

namespace Modules\Osce\Http\Controllers\Admin;


use Modules\Osce\Http\Controllers\CommonController;
use Modules\Osce\Entities\RoomCate as RoomCate;
use Illuminate\Http\Request;

class RoomCateController extends CommonController
{

    /**
     * 获取场所类别列表,根据场所类来查找
     * @api       GET /osce/admin/room-cate/room-cate-list
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
    public function getRoomCateList(Request $request)
    {
        //验证规则，暂时留空

        //获取各字段
        $formData = $request->only('keyword', 'order_by', 'order_name');
        //获取当前场所的类
        //目前暂时按照一级来做，所以不需要pid
//        $pid = $request->input('pid');
//        $pid = empty($pid) ? 1 : $pid;
        //展示页面
        $place = new RoomCate();
        $data = $place->showRoomCateList($formData);
        dd($data);
    }

    /**
     * 插入一条数据
     * @api       POST /osce/admin/room-cate/create-room-cate
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
    public function postCreateRoomCate(Request $request)
    {

        $this->validate($request, [
            'name' => 'required|unique:place,name|max:20|min:4',

            'pid' => 'required|integer',
            'status' => 'required|integer',
            'cid' => 'required|integer'
        ]);


        $formData = $request->only('name', 'pid', 'status', 'cid');


        $RoomCate = new RoomCate();
        $result = $RoomCate->insertData($formData);
        try {
            if (!$result) {
                throw new \Exception('数据插入失败！请重试');
            } else {

                return redirect()->route('osce.admin.place.getRoomCateList');
            }
        } catch (\Exception $ex) {

            return redirect()->back()->withErrors($ex);
        }
    }

    /**
     * 修改数据 着陆页
     * @api       GET /osce/admin/room-cate/edit-room-cate
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
    public function getEditRoomCate(Request $request)
    {
        $this->validate($request, [
            'id' => 'required|integer'
        ]);

        $formData = $request->only('id');
        $RoomCate = new RoomCate();
        $data = $RoomCate->showRoomCateList($formData);

//        return view('',['data' => $data]);
    }

    /**
     * 修改数据 处理
     * @api       POST /osce/admin/room-cate/edit-room-cate
     * @access    public
     * @param Request $request get请求<br><br>
     *                         <b>get请求字段：</b>
     * @return view
     * @version   1.0
     * @author    jiangzhiheng <jiangzhiheng@misrobot.com>
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function postEditRoomCate(Request $request, RoomCate $RoomCate)
    {
        $this->validate($request, [
            'id' => 'required|integer',
            'name' => 'required|unique:place,name|max:20|min:4',
            'detail' => 'required',
        ]);

        $formData = $request->only('name', 'detail', 'status');
        $id = $request->input('id');

        $result = $RoomCate->updateData($id, $formData);

        try {
            if (!$result) {
                throw new \Exception('数据修改失败！请重试');
            } else {
                return redirect()->route('osce.admin.place.getRoomCateList');
            }
        } catch (\Exception $ex) {
            return redirect()->back()->withErrors($ex);
        }
    }

    /**
     * 删除场所类
     * @api       POST /osce/admin/room-cate/delete-room-cate
     * @access    public
     * @param Request $request get请求<br><br>
     *                         <b>get请求字段：</b>
     * @return view
     * @version   1.0
     * @author    jiangzhiheng <jiangzhiheng@misrobot.com>
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function postDeleteRoomCate(Request $request, RoomCate $model)
    {
        $this->validate($request, [
            'id' => 'required|integer',
        ]);

        $id = $request->input('id');

        $result = $model->deleteData($id);

        if (!$result) {
            return redirect()->back()->withErrors('删除失败,请重试!');
        }
        return redirect()->route('osce.admin.place.getRoomCateList');
    }
}