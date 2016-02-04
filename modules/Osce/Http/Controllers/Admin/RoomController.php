<?php
/**
 * Created by PhpStorm.
 * User: CoffeeKizoku
 * Date: 2016/1/2
 * Time: 13:29
 */

namespace Modules\Osce\Http\Controllers\Admin;


use Modules\Osce\Entities\Area;
use Modules\Osce\Entities\AreaVcr;
use Modules\Osce\Entities\RoomVcr;
use Modules\Osce\Entities\Vcr;
use Modules\Osce\Http\Controllers\CommonController;
use Illuminate\Http\Request;
use Modules\Osce\Entities\Room as Room;
use DB;
use Auth;

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
        $keyword = e($request->input('keyword', ""));
        $type    = $request ->input('type', '0');
        $id      = $request ->input('id', '');

        try{
            //获取当前场所的类
            $data = $room->showRoomList($keyword, $type, $id);
            //获取当前的标签
            $area = config('osce.room_cate');
            //展示页面

            if ($type === "0") {
                return view('osce::admin.resourcemanage.examroom', ['area' => $area, 'data' => $data,'type'=>$type,'keyword'=>$keyword]);
            } else if ($type == 1){
                return view('osce::admin.resourcemanage.central_control', ['area' => $area, 'data' => $data,'type'=>$type,'keyword'=>$keyword]);
            }else if ($type == 2){
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
     * @param Room $model
     * @return view
     * @throws \Exception
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
        $id = $request->input('id');
        $type = $request->input('type');
        $data = $model->showRoomList("", $type, $id);

        //TODO:zhoufuxiang，查询没被其他考场关联的摄像机
        $model = new Vcr();
        list($vcr,$modelVcr) = $model->selectVcr($id, $type);

        if(!empty($modelVcr)){
            $data->vcr_id = $modelVcr->vcr_id;
        }else{
            $data->vcr_id = 0;
        }
        //将数据展示到页面
        return view('osce::admin.resourcemanage.examroom_edit', ['data' => $data, 'vcr'=>$vcr, 'type'=>$type]);
    }

    /**
     * 修改房间页面 业务处理
     * @api       POST /osce/admin/room/edit-room
     * @access    public
     * @param Request $request post请求<br><br>
     *                         <b>get请求字段：</b>
     *                         array           id            主键ID
     * @return view
     * @throws \Exception
     * @version   1.0
     * @author    jiangzhiheng <jiangzhiheng@misrobot.com>
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function postEditRoom(Request $request)
    {
        //验证数据，暂时省略
        $this->validate($request, [
            'id'            => 'required|integer',
            'name'          => 'required',
            'description'   => 'required',
            'type'          => 'required'
        ]);

        $id         = $request->input('id');
        $vcr_id     = $request->get('vcr_id');
        $formData   = $request->only('name', 'description', 'address', 'code');
        $type = $request->input('type','0');
        $user = Auth::user();
        if(!$user){
            throw new \Exception('操作人不存在，请先登录');
        }

        try {
            if ($type === '0') {
                $room = new Room();
                $room->editRoomData($id, $vcr_id, $formData);
            } else {
                $area = new Area();
                $area->editAreaData($id, $vcr_id, $formData);
            }
            return redirect()->route('osce.admin.room.getRoomList');

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
    public function getAddRoom(Request $request)
    {
        $id = $request->input('id');
        $type = $request->input('type');

        //TODO:zhoufuxiang，查询没有被其他考场关联的摄像机
        $vcr = Vcr::where('used',0)
            ->whereNotIn('status',[2,3])
            ->select(['id', 'name'])->get();     //关联摄像机


        return view('osce::admin.resourcemanage.examroom_add',['vcr' =>$vcr, 'type' => $type]);
    }

    /**
     * 往room表新插入一行数据
     * @api       POST /osce/admin/room/create-room
     * @access    public
     * @param Request $request post请求<br><br>
     *                         <b>get请求字段：</b>
     *                         array           id            主键ID
     *                         array          status         状态
     * @param Room $room
     * @return view
     * @version   1.0
     * @author    jiangzhiheng <jiangzhiheng@misrobot.com>
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function postCreateRoom(Request $request, Room $room)
    {
        try {
            //验证
            $this->validate($request, [
                'vcr_id'        => 'required',
                'name'          => 'required|unique:osce_mis.room,name',
                'address'       => 'required',
                'code'          => 'sometimes',
                'description'   => 'required',
                'type'          => 'required',
            ],[
                'name.unique'   =>  '名称必须唯一',
                'vcr_id.required'=> '摄像头id必须输入'
            ]);
            //TODO   表单内容变化没有提交nfc字段
            $formData = $request->only('name', 'address', 'code', 'description');
            $type   = $request->input('type');
            $vcrId  = $request->get('vcr_id');
            if (!$user = Auth::user()) {
                throw new \Exception('当前操作者没有登陆');
            }
            $userId = $user->id;
            $formData['created_user_id'] = $userId;
            $formData['cate']            = $type;

            if ($type === '0') {
                $room->createRoom($formData,$vcrId,$userId);
            } else {
                $area = new Area();
                $area->createRoom($formData,$vcrId,$userId);
            }

            return redirect()->route('osce.admin.room.getRoomList',['type'=>$type]);
        } catch (\Exception $ex) {
            return redirect()->back()->withErrors($ex->getMessage());
        }
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
                'id' => 'required|integer',
                'type' => 'sometimes|integer'
            ]);

            $id = $request->input('id');
            $type = $request->input('type','0');
            if (!$id) {
                throw new \Exception('没有该房间！');
            }

            if ($type === '0') {
                $room->deleteData($id);
            } else {
                $area = new Area();
                $area->deleteArea($id);
            }

            return $this->success_data(['删除成功！']);
        } catch (\Exception $ex) {
            return $this->fail($ex);
        }
    }


    /**
     * 判断名称是否已经存在
     * @url POST /osce/admin/resources-manager/postNameUnique
     * @author Zhoufuxiang <Zhoufuxiang@misrobot.com>     *
     */
    public function postNameUnique(Request $request)
    {
        $this->validate($request, [
//            'name'      => 'required',
        ]);

        $id     = $request  -> get('id');
        $value   = $request  -> get('name');
        $code   = $request  -> get('code');
        $name = 'name';
        if(!empty($code)){
            $name = 'code';
            $value= $code;
        }
        //实例化模型
        $model =  new Room();
        //查询 该名字 是否存在
        if(empty($id)){
            $result = $model->where("$name", $value)->first();
        }else{
            $result = $model->where("$name", $value)->where('id', '<>', $id)->first();
        }
        if($result){
            return json_encode(['valid' =>false]);
        }else{
            return json_encode(['valid' =>true]);
        }
    }


}