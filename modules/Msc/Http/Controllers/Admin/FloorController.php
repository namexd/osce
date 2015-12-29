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
use Pingpong\Modules\Routing\Controller;
use Modules\Msc\Entities\Floor;
use Modules\Msc\Entities\School;
use Illuminate\Http\Request;
use URL;
class FloorController extends Controller {

    /**
     * Created by PhpStorm.
     * User: weihuiguo
     * Date: 2015/12/28 0028
     * Time: 17:01
     * 楼栋列表
     */
    public function index(Floor $Floor){
        $keyword = !empty(Input::get('keyword'))?Input::get('keyword'):'';
        $where['keyword'] = $keyword;
        $datalist = $Floor->getFilteredPaginateList($where);
        //dd($datalist);

        return view('msc::admin.labmanage.ban_maintain',['data'=>$datalist,'keyword'=>Input::get('keyword')]);
    }


    /**
     * Created by PhpStorm.
     * User: weihuiguo
     * Date: 2015/12/28 0028
     * Time: 17:01
     * 新加楼栋操作
     */
    public function getAddFloorInsert(Request $Request){
        //dd(Input::get('name'));
        $this->validate($Request, [
            'name'      => 'required',
            'floor_top'       => 'required|integer',
            'floor_buttom'        => 'required|integer',
            'address' => 'required',
            'status'   => 'required|in:0,1',
            'school_id' =>'required',
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
            'created_at'=>time(),
            'updated_at'=>time(),
        ];
        //dd($data);
        $add = Floor::create($data);
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
     * 修改楼栋
     */
    public function getEditFloor(){
        $id = urlencode(e(Input::get('id')));
        if($id){
            $floorDetail = Floor::find($id);
        }else{
            return redirect()->back()->withInput()->withErrors('系统异常');
        }
        $data = [
            'floorDetail' => $floorDetail,
        ];
        //return view('msc::admin.',$data);
    }

    /**
     * Created by PhpStorm.
     * User: weihuiguo
     * Date: 2015/12/28 0028
     * Time: 17:01
     * 修改楼栋操作
     */
    public function getEditFloorInsert(){
        $this->validate($Request, [
            'name'      => 'required',
            'floor_top'       => 'sometimes|in:1,2,3',
            'floor_buttom'        => 'sometimes|integer',
            'address' => 'sometimes|in:1,2',
            'status'   => 'sometimes|integer',
            'school_id' =>'required',
            'status' => 'required',
            'created_user_id' => 'required',
        ]);
        $data = [
            'name'=>Input::get('name'),
            'floor_top'=>Input::get('floor_top'),
            'floor_buttom'=>Input::get('floor_buttom'),
            'address'=>Input::get('address'),
            'status'=>Input::get('status'),
            'school_id'=>Input::get('school_id'),
            'created_user_id'=>Input::get('created_user_id'),
        ];
        $add = DB::connection('msc_mis')->table('location')->where('id','=',urlencode(e(Input::get('id'))))->update($data);
        if($data != fasle){
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
        if($id){
            $data = DB::connection('msc_mis')->table('location')->where('id','=',$id)->update(['status'=>Input::get('status')]);
            if($data != fasle){
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
     * 楼栋删除
     */
    public function getDeleteFloor(){
        $id = urlencode(e(Input::get('id')));
        if($id){
            $data = DB::connection('msc_mis')->table('location')->where('id','=',$id)->delete();
            if($data != fasle){
                return redirect()->back()->withInput()->withErrors('删除成功');
            }else{
                return redirect()->back()->withInput()->withErrors('系统异常');
            }
        }else{
            return redirect()->back()->withInput()->withErrors('系统异常');
        }
    }
}