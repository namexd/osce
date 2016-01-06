<?php
/**
 * 实验室控制器
 *
 * @author weihuiguo <weihuiguo@misrobot.com>
 * @date 2015年12月28日17:10:20
 * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
 */

namespace Modules\Msc\Http\Controllers\Admin;

use Illuminate\Support\Facades\Auth;
use Pingpong\Modules\Routing\Controller;
use Modules\Msc\Entities\Laboratory;
use Modules\Msc\Entities\Teacher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Modules\Msc\Entities\Floor;
use Illuminate\Support\Facades\Cache;
use Modules\Msc\Http\Controllers\MscController;
use URL;
use DB;
class LaboratoryController extends MscController {

    /**
     * Created by PhpStorm.
     * User: weihuiguo
     * Date: 2015/12/28 0028
     * Time: 17:01
     * 实验室列表
     */
    public function index(Laboratory $Laboratory){
        $keyword = !empty(Input::get('keyword'))?Input::get('keyword'):null;
        if(Input::get('status') >= 0){

            $where['status'] = Input::get('status');
        }
        if(Input::get('open_type') >= 0){
            $where['open_type'] = Input::get('open_type');
        }
        $where['keyword'] = $keyword;
        $datalist = $Laboratory->getFilteredPaginateList($where);
        //$datalist = $datalist->toArray();
        //dd($datalist);
        foreach($datalist as $v){
            $v->opentype = $v->open_type;
            $v->open_type = $Laboratory->getType($v->open_type);

        }
        //学院
        $school = DB::connection('msc_mis')->table('school')->get();
        //教学楼
        $floor = DB::connection('msc_mis')->table('location')->where('status','=',1)->get();
        //获取和老师管理的用户数据
        $teacher = new Teacher();
        $teacher =  $teacher->getdata();
        //dd($teacher);
        return view('msc::admin.labmanage.lab_maintain',[
            'datalist'=>$datalist,
            'school'=>$school,
            'floor'=>$floor,
            'teacher'=>$teacher,
            'keyword'=>Input::get('keyword')?Input::get('keyword'):'',
            'status'=>Input::get('status')?Input::get('status'):'',
            'open_type'=>Input::get('open_type')?Input::get('open_type'):'',
        ]);

    }


    /**
     * Created by PhpStorm.
     * User: weihuiguo
     * Date: 2015/12/28 0028
     * Time: 17:01
     * 新加实验室操作
     */
    public function getAddLabInsert(Request $Request){
        $this->validate($Request, [
            'name'      => 'required',
            'short_name'       => 'required',
            'enname'        => 'required',
            'short_enname' => 'required',
            'open_type' =>'required',
            'manager_user_id' => 'required|integer',
            'status' => 'required',
            'floor' => 'required',
            'code' => 'required',
            'total' => 'required|integer'
        ]);
        $user = Auth::user();
        $data = [
            'name'=>Input::get('name'),
            'short_name'=>Input::get('short_name'),
            'enname'=>Input::get('enname'),
            'short_enname'=>Input::get('short_enname'),
            'location_id'=>Input::get('building'),
            'open_type'=>Input::get('open_type'),
            'manager_user_id'=>Input::get('manager_user_id'),
            'floor'=>Input::get('floor'),
            'status'=>Input::get('status'),
            'created_user_id'=>$user->id,
            'floor'=>Input::get('floor'),
            'code'=>Input::get('code'),
            'total'=>Input::get('total'),
        ];
        //dd(Input::get('total'));
        $add = Laboratory::create($data);
        //dd($add);
        if($data != false){
            return redirect()->back()->withInput()->withErrors('添加成功');
        }else{
            return redirect()->back()->withInput()->withErrors('系统异常');
        }
    }



    /**
     * Created by PhpStorm.
     * User: weihuiguo
     * Date: 2015/12/28 0028
     * Time: 17:01
     * 修改实验室操作
     */
    public function getEditLabInsert(Request $Request,Laboratory $laboratory){
        $this->validate($Request, [
            'name'      => 'required',
            'short_name'       => 'required',
            'enname'        => 'required',
            'short_enname' => 'required',
            'open_type' =>'required',
            'manager_user_id' => 'required|integer',
            'status' => 'required',
            'floor' => 'required',
            'code' => 'required',
            'total' => 'required|integer'
        ]);
        $user = Auth::user();
        $data = [
            'name'=>Input::get('name'),
            'short_name'=>Input::get('short_name'),
            'enname'=>Input::get('enname'),
            'short_enname'=>Input::get('short_enname'),
            'location_id'=>Input::get('building'),
            'open_type'=>Input::get('open_type'),
            'manager_user_id'=>Input::get('manager_user_id'),
            'floor'=>Input::get('floor'),
            'status'=>Input::get('status'),
            'created_user_id'=>$user->id,
            'floor'=>Input::get('floor'),
            'code'=>Input::get('code'),
            'total'=>Input::get('total'),
        ];
        $add = $laboratory->where('id','=',urlencode(e(Input::get('id'))))->update($data);
        if($data != false){
            return redirect()->back()->withInput()->withErrors('修改成功');
        }else{
            return redirect()->back()->withInput()->withErrors('系统异常');
        }
    }

    /**
     * Created by PhpStorm.
     * User: weihuiguo
     * Date: 2015/12/28 0028
     * Time: 17:01
     * 实验室停用
     */
    public function getStopLab(Laboratory $laboratory){
        $id = urlencode(e(Input::get('id')));
        if($id){
            $data = $laboratory->where('id','=',$id)->update(['status'=>Input::get('type')]);
            if($data != false){
                return redirect()->back()->withInput()->withErrors('停用成功');
            }else{
                return redirect()->back()->withInput()->withErrors('系统异常');
            }
        }else{
            return redirect()->back()->withInput()->withErrors('系统异常');
        }
    }



    /**
     * Created by PhpStorm.
     * User: weihuiguo
     * Date: 2015/12/28 0028
     * Time: 17:01
     * 实验室删除
     */
    public function getDeleteLab(){
        $id = urlencode(e(Input::get('id')));
        //dd($id);
        if($id){
            $data = Laboratory::find($id);
            $del = $data->delete();
            //dd($del);
            if($del != false){
                return redirect()->back()->withInput()->withErrors('删除成功');
            }else{
                return redirect()->back()->withInput()->withErrors('系统异常');
            }
        }else{
            return redirect()->back()->withInput()->withErrors('系统异常');
        }
    }
    /**
     * Created by PhpStorm.
     * User: weihuiguo
     * Date: 2015/12/28 0028
     * Time: 17:01
     * 计算教学楼楼层
     */
    function get_float($ground,$underground){
        $arr = array();
        $brr = array();
        //地下-2楼
        for($i=$underground;$i > 0;$i--){
            $arr['-'.$i] = '-'.$i;
        }

        //地上
        for ($i=1; $i <= $ground; $i++) {
            $arr[$i] = $i;
        }
        $data = array_merge($arr,$brr);
        return $data;
    }

    /**
     * Created by PhpStorm.
     * User: weihuiguo
     * Date: 2015/12/28 0028
     * Time: 17:01
     * 联动查找楼栋
     */
    public function getLocal(Floor $floor){
        $id = Input::get('id');
        $local = $floor->where('school_id','=',$id)->where('status','=',1)->get();
        if($local != false){
            return $local;exit;
        }else{
            return 0;exit;
        }

    }

    /**
     * Created by PhpStorm.
     * User: weihuiguo
     * Date: 2015/12/28 0028
     * Time: 17:01
     * 联动查找楼层
     */
    public function getFloor(Floor $floor){
        $floor = $floor->where('id','=',Input::get('id'))->first();
        $floorList = $this->get_float($floor['floor_top'],$floor['floor_buttom']);
        if($floorList != false){
            return $floorList;exit;
        }else{
            return 0;exit;
        }
    }


    //TODO::实验室开放日历
    /**
     * Created by PhpStorm.
     * User: weihuiguo
     * Date: 2016年1月4日11:09:03
     * 实验室开发日历
     */
    public function getLabClearnder(){
        $location = Floor::where('status','=',1)->get();
        return view('msc::admin.labmanage.open_calendar',[
            'location' => $location,
        ]);
    }

    /**
     * Created by PhpStorm.
     * User: weihuiguo
     * Date: 2016年1月4日11:09:03
     * 根据楼栋查找楼层及该楼层所有实验室
     */
    public function getFloorLab(){
        $labArr = [];
        $local_id = Input::get('lid');
        $local = Floor::where('id','=',$local_id)->first();
        $floor = $this->get_float($local['floor_top'],$local['floor_buttom']);
        $where['status'] = 0;
        $where['location_id'] = $local_id;
        $user = Auth::user();
        $role_id = DB::connection('sys_mis')->table('sys_user_role')->where('user_id','=',$user->id)->first();
        $role_name = DB::connection('sys_mis')->table('sys_roles')->where('id','=',$role_id->role_id)->first();
        if($role_name->name == '超级管理员'){

        }else{
            $where['manager_user_id'] = $user['id'];
        }
        foreach($floor as $k=>$v){
            $where['floor'] = $v;
            $labArr[$k]['floor'] = $v;
            $labArr[$k]['lab'] = Laboratory::where($where)->get();
        }
        return $labArr;
    }



    /**
     * Created by PhpStorm.
     * User: weihuiguo
     * Date: 2016年1月6日11:40:11
     * 添加或修改实验室开放时间
     */
    public function postOperatingLabCleander(){

    }
}