<?php
/**
 * 实验室控制器
 *
 * @author weihuiguo <weihuiguo@163.com>
 * @date 2015年12月28日17:10:20
 * @copyright 2013-2015 MIS 163.com Inc. All Rights Reserved
 */

namespace Modules\Msc\Http\Controllers\Admin;

use Pingpong\Modules\Routing\Controller;
use Modules\Msc\Entities\Laboratory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Modules\Msc\Entities\Floor;
use URL;
use DB;
class LaboratoryController extends Controller {

    /**
     * Created by PhpStorm.
     * User: weihuiguo
     * Date: 2015/12/28 0028
     * Time: 17:01
     * 实验室列表
     */
    public function index(Laboratory $Laboratory){
        $keyword = !empty(Input::get('keyword'))?Input::get('keyword'):'';
        $where['keyword'] = $keyword;
        $datalist = $Laboratory->getFilteredPaginateList($where);
        //学院
        $school = DB::connection('msc_mis')->table('school')->get();
        //教学楼
        $floor = DB::connection('msc_mis')->table('location')->where('status','=',1)->get();
        return view('msc::admin.labmanage.lab_maintain',['school'=>$school,'floor'=>$floor]);

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
            'short_name'       => 'sometimes|in:1,2,3',
            'enname'        => 'sometimes|integer',
            'short_enname' => 'sometimes|in:1,2',
            'location_id'   => 'sometimes|integer',
            'open_type' =>'required',
            'manager_user_id' => 'required',
            'status' => 'required',
            'created_user_id' => 'required',
            'floor' => 'required',
            'code' => 'required',
        ]);
        $data = [
            'name'=>Input::get('name'),
            'short_name'=>Input::get('short_name'),
            'enname'=>Input::get('enname'),
            'short_enname'=>Input::get('short_enname'),
            'location_id'=>Input::get('location_id'),
            'open_type'=>Input::get('open_type'),
            'manager_user_id'=>Input::get('manager_user_id'),
            'floor'=>Input::get('floor'),
            'status'=>Input::get('status'),
            'created_user_id'=>Input::get('created_user_id'),
            'floor'=>Input::get('floor'),
            'code'=>Input::get('code'),
        ];
        $add = Laboratory::create($data);
        if($data != fasle){
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
     * 修改实验室
     */
    public function getEditLab(){
        $id = urlencode(e(Input::get('id')));
        if($id){
            $floorDetail = Laboratory::find($id);
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
     * 修改实验室操作
     */
    public function getEditLabInsert(Request $Request){
        $this->validate($Request, [
            'name'      => 'required',
            'short_name'       => 'sometimes|in:1,2,3',
            'enname'        => 'sometimes|integer',
            'short_enname' => 'sometimes|in:1,2',
            'location_id'   => 'sometimes|integer',
            'open_type' =>'required',
            'manager_user_id' => 'required',
            'status' => 'required',
            'created_user_id' => 'required',
            'floor' => 'required',
            'code' => 'required',
        ]);
        $data = [
            'name'=>Input::get('name'),
            'short_name'=>Input::get('short_name'),
            'enname'=>Input::get('enname'),
            'short_enname'=>Input::get('short_enname'),
            'location_id'=>Input::get('location_id'),
            'open_type'=>Input::get('open_type'),
            'manager_user_id'=>Input::get('manager_user_id'),
            'floor'=>Input::get('floor'),
            'status'=>Input::get('status'),
            'created_user_id'=>Input::get('created_user_id'),
            'floor'=>Input::get('floor'),
            'code'=>Input::get('code'),
        ];
        $add = DB::connection('msc_mis')->table('lab')->where('id','=',urlencode(e(Input::get('id'))))->update($data);
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
     * 实验室停用
     */
    public function getStopLab(){
        $id = urlencode(e(Input::get('id')));
        if($id){
            $data = DB::connection('msc_mis')->table('lab')->where('id','=',$id)->update(['status'=>Input::get('status')]);
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
     * 实验室删除
     */
    public function getDeleteLab(){
        $id = urlencode(e(Input::get('id')));
        if($id){
            $data = DB::connection('msc_mis')->table('lab')->where('id','=',$id)->delete();
            if($data != fasle){
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
        //地下-2楼
        for($i=$ground;$i > 0;$i--){
            $arr[$i] = '-'.$i.'楼';
        }
        //地上
        for ($i=1; $i <= $underground; $i++) {
            $brr[$i] = $i.'楼';
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
    public function getLocal(){
        $id = Input::get('id');
        $local = DB::connection('msc_mis')->table('location')->where('school_id','=',$id)->where('status','=',1)->get();
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
}