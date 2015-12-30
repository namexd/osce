<?php
/**
 * Created by PhpStorm.
 * User: j5110
 * Date: 2015/12/28
 * Time: 13:46
 */

namespace Modules\Osce\Http\Controllers\Admin;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Mockery\CountValidator\Exception;
use Modules\Osce\Entities\Place as Place;
use Modules\Osce\Entities\PlaceCate as PlaceCate;
use Modules\Osce\Http\Controllers\CommonController;
use Modules\Osce\Repositories\Factory;

class PlaceController extends CommonController
{
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
        $place = Factory::place();
        $data = $place->showPlaceList($formData,$pid);
        // dd($data);
        return view('osce::admin.resourcemanage.examroom',['data'=>$data]);
    }

    /**
     * 修改状态
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
    public function postChangeStatus(Request $request)
    {
        //验证字段
        foreach ($request as $item) {
            $this->validate($item, [
                'id' => 'required|integer',
                'status' => 'required|integer',
            ]);
        }

        //将其传入具体修改的方法
        $place = Factory::place();
        $result = $place->change($request);
        return response()->json($result);
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
        $this->validate($request,[
            'name' => 'required|unique:place,name|max:20|min:4',
            'pid'  => 'required|integer',
            'status'  => 'required|integer'
        ]);

        $model = 'place';
        $result =  $this->create($request,$model);
        try {
            if (!$result) {
                throw new Exception('数据插入失败！请重试');
            } else {
                $array = [
                    'code' => 1,
                    'message' => $result,
                ];
                return response()->json($array);
            }
        } catch (\Exception $ex) {
            return response()->json($this->fail($ex));
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
        $place = Factory::placeCate();
        $data = $place->showPlaceCateList($formData);
        dd($data);
    }

    public function postCreatePlaceCate(Request $request)
    {
        $this->validate($request,[
            'name' => 'required|unique:place,name|max:20|min:4',
            'pid'  => 'required|integer',
            'status'  => 'required|integer',
            'cid' => 'required|integer'
        ]);

        $model = 'place_cate';
        $result =  $this->create($request,$model);
        try {
            if (!$result) {
                throw new Exception('数据插入失败！请重试');
            } else {
                $array = [
                    'code' => 1,
                    'message' => $result,
                ];
                return response()->json($array);
            }
        } catch (\Exception $ex) {
            return response()->json($this->fail($ex));
        }
    }
}