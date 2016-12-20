<?php
/**
 * Created by PhpStorm.
 * User: CoffeeKizoku
 * Date: 2016/1/3
 * Time: 10:09
 */

namespace Modules\Osce\Http\Controllers\Admin;


use Modules\Osce\Entities\Area;
use Modules\Osce\Http\Controllers\CommonController;
use Modules\Osce\Entities\RoomCate as RoomCate;
use Illuminate\Http\Request;

class RoomCateController extends CommonController
{

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
     * @author    ZouYuChao <ZouYuChao@sulida.com>
     * @copyright  2013-2017 sulida.com  Inc. All Rights Reserved
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

            return redirect()->back()->withErrors($ex->getMessage());
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
     * @author    ZouYuChao <ZouYuChao@sulida.com>
     * @copyright  2013-2017 sulida.com  Inc. All Rights Reserved
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
     * @author    ZouYuChao <ZouYuChao@sulida.com>
     * @copyright  2013-2017 sulida.com  Inc. All Rights Reserved
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
            return redirect()->back()->withErrors($ex->getMessage());
        }
    }

    /**
     * 删除场所类
     * @api       POST /osce/admin/room-cate/delete
     * @access    public
     * @param Request $request get请求<br><br>
     *                         <b>get请求字段：</b>
     * @param Area|RoomCate $model
     * @return view
     * @version   1.0
     * @author    ZouYuChao <ZouYuChao@sulida.com>
     * @copyright  2013-2017 sulida.com  Inc. All Rights Reserved
     */
    public function postDelete(Request $request, Area $model)
    {
        $this->validate($request, [
            'id' => 'required|integer',
        ]);

        $id = $request->input('id');
        try {
            $result = $model->where('id',$id)->delete();

            if (!$result) {
                return response()->json($this->success_data(['删除成功！']));
            }
        } catch (\Exception $ex) {
            return response()->json($this->fail($ex));
        }

    }
}