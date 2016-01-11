<?php
/**
 * 楼栋控制器
 *
 * @author weihuiguo <weihuiguo@misrobot.com>
 * @date 2015年12月28日17:10:20
 * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
 */

namespace Modules\Msc\Http\Controllers\Admin;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Modules\Msc\Entities\Laboratory;
use Pingpong\Modules\Routing\Controller;
use Modules\Msc\Http\Controllers\MscController;
use Modules\Msc\Entities\Floor;
use Modules\Msc\Entities\School;
use Illuminate\Http\Request;
use URL;
use DB;
class FloorController extends MscController {

    /**
     * Created by PhpStorm.
     * User: weihuiguo
     * Date: 2015/12/28 0028
     * Time: 17:01
     * 楼栋列表
     */
    public function index(Floor $Floor){
        $keyword = !empty(Input::get('keyword'))?Input::get('keyword'):'';
        if(Input::get('status') >= 0){

            $where['status'] = Input::get('status');
        }
        if(Input::get('schools') >= 0){
            $where['schools'] = Input::get('schools');
        }
        $where['keyword'] = $keyword;
        $datalist = $Floor->getFilteredPaginateList($where);
        foreach($datalist as $v){
            $lab = Laboratory::where(['location_id'=>$v->id,'status'=>1])->get();
            //var_dump($lab);
            $lab = $lab->toArray();
            if(!empty($lab)){
                $v->dtype = 1;
            }
        }
        $school = DB::connection('msc_mis')->table('school')->get();
        $keyword = Input::get('keyword')?Input::get('keyword'):'';
        return view('msc::admin.labmanage.ban_maintain',[
            'data'=>$datalist,
            'school'=>$school,
            'keyword'=>$keyword,
            'status'=>Input::get('status'),
            'schools'=>Input::get('schools'),
        ]);





    }


    /**
     * Created by PhpStorm.
     * User: weihuiguo
     * Date: 2015/12/28 0028
     * Time: 17:01
     * 新加楼栋操作
     */
    public function postAddFloorInsert(Request $Request){
        //dd(Input::get('name'));
        $this->validate($Request, [
            'name'      => 'required',
            'floor_top'       => 'required|integer',
            'floor_buttom'        => 'required|integer',
            'address' => 'required',
            'status'   => 'required|in:0,1',
            'school_id' =>'required',
        ],[
            "name.required" => "楼栋名称必填",
            "floor_top.required" => "楼层数（地上）必填",
            "floor_top.integer"  => "楼栋必须为数字",
            "floor_buttom.required" => "楼层数（地下）必填",
            "floor_buttom.integer"  => "楼栋必须为数字",
            "address.required" => "地址必填",
            "school_id.required" => "学院必选",
            //"integer"      => ":attribute 长度必须在 :min 和 :max 之间"
        ]);
        $user = Auth::user();
        $data = [
            'name'=>Input::get('name'),
            'floor_top'=>Input::get('floor_top'),
            'floor_buttom'=>Input::get('floor_buttom'),
            'address'=>Input::get('address'),
            'status'=>Input::get('status'),
            'school_id'=>Input::get('school_id'),
            'created_user_id'=>$user->id,
        ];
        //dd($data);
        $add = Floor::create($data);
        //dd($add);
        if($add != false){
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
     * 修改楼栋操作
     */
    public function postEditFloorInsert(Request $Request){
        $this->validate($Request, [
            'name'      => 'required',
            'floor_top'       => 'required|integer',
            'floor_buttom'        => 'required|integer',
            'address' => 'required',
            'status'   => 'required|in:0,1',
            'school_id' =>'required',
        ]);
        $data = [
            'name'=>Input::get('name'),
            'floor_top'=>Input::get('floor_top'),
            'floor_buttom'=>Input::get('floor_buttom'),
            'address'=>Input::get('address'),
            'status'=>Input::get('status'),
            'school_id'=>Input::get('school_id'),
        ];
        //dd(Input::get('id'));
        $add = DB::connection('msc_mis')->table('location')->where('id','=',urlencode(e(Input::get('id'))))->update($data);
        if($add != false){
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
     * 楼栋停用
     */
    public function getStopFloor(){
        $id = urlencode(e(Input::get('id')));
        //dd($id);
        $data = [
            'status'=>Input::get('type')
        ];
        if($id){
            $data = DB::connection('msc_mis')->table('location')->where('id','=',$id)->update($data);

            if(Input::get('type')){
                $name = '启用成功';
            }else{
                $name = '停用成功';
            }
            if($data != false){
                return redirect()->back()->withInput()->withErrors($name);
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
     * 楼栋删除
     */
    public function getDeleteFloor(){
        $id = urlencode(e(Input::get('id')));
        if($id){
            $lab = Laboratory::where(['location_id'=>$id,'status'=>1])->get();
            if(!$lab){
                $data = DB::connection('msc_mis')->table('location')->where('id','=',$id)->delete();
            }else{
                return redirect()->back()->withInput()->withErrors('该楼栋下有实验室，不可删除');
            }

            if($data != false){
                return redirect()->back()->withInput()->withErrors('删除成功');
            }else{
                return redirect()->back()->withInput()->withErrors('系统异常');
            }
        }else{
            return redirect()->back()->withInput()->withErrors('系统异常');
        }
    }
}