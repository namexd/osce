<?php
/**
 * Created by PhpStorm.
 * User: j5110
 * Date: 2015/12/28
 * Time: 13:46
 */

namespace Modules\Osce\Http\Controllers\Admin;


use DB;
use Illuminate\Http\Request;
use Modules\Osce\Entities\Place as Place;
use Modules\Osce\Entities\PlaceCate as PlaceCate;
use Modules\Osce\Http\Controllers\CommonController;

class PlaceController extends CommonController
{
    /**
     * 测试
     * /osce/admin/place/test
     */
    public function getTest(){
        return view('osce::admin.resourcemanage.categories');
    }

    /**
     * 获取场所列表,根据场所类来查找
     * @api       GET /osce/admin/place/place-list
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
    public function getPlaceList(Request $request)
    {
        //验证规则，暂时留空

        //获取各字段
        $formData = $request->only('keyword', 'order_by', 'order_name');
        //获取当前场所的类
        $pid = $request->input('pid');
        $pid = empty($pid) ? 1 : $pid;
        //展示页面
        $place = new Place();

        $data = $place->showPlaceList($formData, $pid);
        // dd($data);

        return view('osce::admin.resourcemanage.examroom', ['data' => $data]);
    }

    /**

     * 修改页面的着陆页
     * @api       GET /osce/admin/place/edit-place
     * @access    public
     * @param Request $request post请求<br><br>
     *                         <b>get请求字段：</b>
     *                         array           id            主键ID
     * @return view
     * @version   1.0
     * @author    jiangzhiheng <jiangzhiheng@misrobot.com>
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */

    public function getEditPlace(Request $request)
    {
        //验证ID
        $this->validate($request, [

            'id' => 'required|integer'
        ]);

        //取出id的值
        try {
            $formData = $request->input('id');


            $data = $model->showPlaceList($formData);
            //将数据展示到页面
            return view('osce::admin.resourcemanage.examroom_edit', ['data' => $data]);

        } catch (\Exception $ex) {
            return redirect()->back()->withErrors($ex);
        }
    }

    /**

     * 修改页面 业务处理
     * @api       POST /osce/admin/place/edit-place
     * @access    public
     * @param Request $request post请求<br><br>
     *                         <b>get请求字段：</b>
     *                         array           id            主键ID
     * @return view
     * @version   1.0
     * @author    jiangzhiheng <jiangzhiheng@misrobot.com>
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */

    public function postEditPlace(Request $request)
    {
        //验证数据，暂时省略

        $formData = $request->only('name', 'detail', 'status');
        $id = $request->input('id');

        $place = new Place();
        $result = $place->updateData($id, $formData);


        try {
            if (!$result) {
                throw new Exception('数据修改失败！请重试');
            } else {
                return redirect()->route('osce.admin.place.getPlaceList');
            }
        } catch (\Exception $ex) {

            return redirect()->back()->withErrors($ex);
        }

    }


    /**
     * 考场新增
     */
    public function getAddPlace(Request $request)
    {
        return view('osce::admin.resourcemanage.examroom_add');
    }

    public function postEditPlace(Request $request)
    {
        //验证数据，暂时省略


    }
    /**
     * 往place表新插入一行数据
     * @api       POST /osce/admin/place/change-status
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
    public function postCreatePlace(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|unique:place,name|max:20|min:4',

            'status' => 'required|integer'
        ]);

        $formData = $request->only('name', 'pid', 'status');

        $result = DB::table('place')->insertGetId($formData);
        try {
            if (!$result) {
                throw new Exception('数据插入失败！请重试');
            } else {

                return redirect()->route('osce.admin.place.getPlaceList');
            }
        } catch (\Exception $ex) {

            return redirect()->back()->withErrors($ex);
        }
    }

    /**
     * 获取场所类别列表,根据场所类来查找
     * @api       GET /osce/admin/place/place-cate-list
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
    public function getPlaceCateList(Request $request)
    {
        //验证规则，暂时留空

        //获取各字段
        $formData = $request->only('keyword', 'order_by', 'order_name');
        //获取当前场所的类
        //目前暂时按照一级来做，所以不需要pid
//        $pid = $request->input('pid');
//        $pid = empty($pid) ? 1 : $pid;
        //展示页面
        $place = new PlaceCate();
        $data = $place->showPlaceCateList($formData);
        dd($data);
    }

    /**
     * 插入一条数据
     * @api       POST /osce/admin/place/create-place-cate
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
    public function postCreatePlaceCate(Request $request)
    {

        $this->validate($request, [
            'name' => 'required|unique:place,name|max:20|min:4',

            'pid' => 'required|integer',
            'status' => 'required|integer',
            'cid' => 'required|integer'
        ]);


        $formData = $request->only('name', 'pid', 'status', 'cid');


        $placeCate = new PlaceCate();
        $result = $placeCate->insertData($formData);
        try {
            if (!$result) {
                throw new Exception('数据插入失败！请重试');
            } else {

                return redirect()->route('osce.admin.place.getPlaceCateList');
            }
        } catch (\Exception $ex) {

            return redirect()->back()->withErrors($ex);
        }
    }

    /**
     * 修改数据 着陆页
     * @api       GET /osce/admin/place/edit-place-cate
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
    public function getEditPlaceCate(Request $request)
    {
        $this->validate($request, [
            'id' => 'required|integer'
        ]);

        $formData = $request->only('id');
        $placeCate = new PlaceCate();
        $data = $placeCate->showPlaceCateList($formData);

//        return view('',['data' => $data]);
    }

    /**
     * 修改数据 处理
     * @api       POST /osce/admin/place/edit-place-cate
     * @access    public
     * @param Request $request get请求<br><br>
     *                         <b>get请求字段：</b>
     * @return view
     * @version   1.0
     * @author    jiangzhiheng <jiangzhiheng@misrobot.com>
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function postEditPlaceCate(Request $request)
    {
        $this->validate($request, [
            'id' => 'required|integer',
            'name' => 'required|unique:place,name|max:20|min:4',
            'detail' => 'required',
        ]);

        $formData = $request->only('name', 'detail', 'status');
        $id = $request->input('id');

        $placeCate = new PlaceCate();
        $result = $placeCate->updateData($id, $formData);

        try {
            if (!$result) {
                throw new Exception('数据修改失败！请重试');
            } else {
                return redirect()->route('osce.admin.place.getPlaceCateList');
            }
        } catch (\Exception $ex) {
            return redirect()->back()->withErrors($ex);
        }
    }

    /**
     * 删除场所或场所类的状态
     * @api       POST /osce/admin/place/delete
     * @access    public
     * @param Request $request get请求<br><br>
     *                         <b>get请求字段：</b>
     * @return view
     * @version   1.0
     * @author    jiangzhiheng <jiangzhiheng@misrobot.com>
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function postDelete(Request $request)
    {
        $this->validate($request, [
            'id' => 'required|integer',
            'type' => 'required'
        ]);

        $id = $request->input('id');
        $type = $request->input('type');
        try {
            if ($type == 'place') {
                $model = new Place();
            } elseif ($type == 'place_cate') {
                $model = new PlaceCate();
            } else {
                abort(404, '该页面不存在，请联系管理员');
            }
            $result = $model->deleteData($id);

            if (!$result) {
                throw new Exception('数据删除失败！请重试');
            }

            if ($type == 'place') {
                return redirect()->route('osce.admin.place.getPlaceList');
            } elseif ($type == 'place_cate') {
                return redirect()->route('osce.admin.place.getPlaceCateList');
            }
        } catch (\Exception $ex) {
            return redirect()->back()->withErrors($ex);
        }
    }
}