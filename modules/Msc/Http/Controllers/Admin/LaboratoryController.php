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
use Modules\Msc\Entities\OpenPlan;
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
        //$this->start_sql(1);
        $datalist = $Laboratory->getFilteredPaginateList($where);
        //$this->end_sql(1);
        //$datalist = $datalist->toArray();
        //dd($datalist);
        foreach($datalist as $k=>$v){
            $v->opentype = $v->open_type;
            $v->open_type = $Laboratory->getType($v->open_type);
            $floor = $v->floors;
            if(is_null($floor) || $floor == ''){
                unset($datalist[$k]);
            }

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
        ],[
            "total.required" => "实验室容量必填",
            //"integer"      => ":attribute 长度必须在 :min 和 :max 之间"
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
        ],[
            "total.required" => "实验室容量必填",
            //"integer"      => ":attribute 长度必须在 :min 和 :max 之间"
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
            //dd(Input::get('type'));
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
        if(Input::get('type') == 1){
            $local_id = Laboratory::where('id','=',Input::get('id'))->first();
            $floor = $floor->where('id','=',$local_id->location_id)->first();

        }else{
            $floor = $floor->where('id','=',Input::get('id'))->first();
        }

        $floorList = $this->get_float($floor['floor_top'],$floor['floor_buttom']);
        //dd($floorList);
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
    //二维数组去重
    function er_array_unique($arr){
        $newarr = array();
        if(is_array($arr)){
            foreach($arr as $v){
                if(!in_array($v,$newarr,true)){
                    $newarr[] = $v;
                }
            }
        }else{
            return false;
        }
        return $newarr;
    }

    //查找实验室已存在的开放日历
    public function get_lab_cleander($id){
        $lid = $id;
        $cleaner = OpenPlan::where('lab_id','=',$lid)->where('status','=',1)->get();
        $cleaner = $cleaner->toArray();
        //dd($cleaner);
        $array = array();
        foreach($cleaner as $k=>$v){
            if(array_key_exists('year',$v) || array_key_exists('month',$v) || array_key_exists('day',$v)){
                $array[$k]['year'] = $v['year'];
                $array[$k]['month'] = $v['month'];
                $array[$k]['day'] = $v['day'];
                $array[$k]['lab_id'] = $lid;
                $array[$k]['status'] = 1;

            }
        }

        $arr = array();
        $array = $this->er_array_unique($array);
        sort($array);
        foreach($array as $o=>$date){
            $arr[$o]['date'] = $date;
            $arr[$o]['child'] = OpenPlan::where($date)->select('begintime','endtime','period_type')->get();
        }
        return $arr;
    }

    //如果进入日历时数据已存在
    public function getEditLabCleander(){
        return ['data'=>$this->get_lab_cleander(Input::get('id'))];
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
        $where['status'] = 1;
        $where['location_id'] = $local_id;
        $user = Auth::user();
        $role_id = DB::connection('sys_mis')->table('sys_user_role')->where('user_id','=',$user->id)->first();
        if(!$role_id){
            return '没有该账户对应的角色';exit;
        }
        $role_name = DB::connection('sys_mis')->table('sys_roles')->where('id','=',$role_id->role_id)->first();
        if($role_name->name == '超级管理员'){

        }else{
            $where['manager_user_id'] = $user['id'];
        }
        foreach($floor as $k=>$v){
            $where['floor'] = $v;
            $labArr[$k]['floor'] = $v;
            $this->start_sql(1);
            $labArr[$k]['lab'] = Laboratory::where($where)->get();
            //$this->end_sql(1);
        }
        return $labArr;
    }

    //判断上午，中午，下午，晚上
    public function get_n($type){
        switch ($type) {
            case 'morning':
                $name = '1';
                break;
            case 'noon':
                $name = '2';
                break;
            case 'afternoon':
                $name = '3';
                break;
            default:
                $name = '4';
        }
        return $name;
    }
//    /**
//     * 计算日期是当月第几周
//     */
//    public function current_week($firstDate=''){
//        $firstDate=empty($firstDate)?strtotime(date('Y').'-01-01'):(is_numeric($firstDate)?$firstDate:strtotime($firstDate));
//        //开学第一天的时间戳
//        list($year,$month,$day)=explode('-',date('Y-n-j',$firstDate));
//        $time_chuo_of_first_day=mktime(0,0,0,$month,$day,$year);
//        //今天的时间戳
//        list($year,$month,$day)=explode('-',date('Y-n-j'));
//        $time_chuo_of_current_day=mktime(0,0,0,$month,$day,$year);
//        $zhou=intval(($time_chuo_of_current_day-$time_chuo_of_first_day)/60/60/24/7)+1;
//        return $zhou;
//
//    }


    /**
     * Created by PhpStorm.
     * User: weihuiguo
     * Date: 2016年1月6日11:40:11
     * 添加或修改实验室开放时间
     */
    public function postOperatingLabCleander(){
        DB::connection('msc_mis')->beginTransaction();
        $date = explode('&',trim(Input::get('date'),'&'));
        $timestr =  explode('@',trim(Input::get('timestr'),'@'));
        //dd($timestr);
        foreach($timestr as $k=>$v){
            $arr['time'.$k] = explode('!',trim($v,'!'));
        }
        if(count($arr) > 1 && count($arr) < 3){
            $brr = array_merge($arr['time0'],$arr['time1']);
        }
        if(count($arr) < 2){
            foreach($arr as $lttwo){
                $brr = $lttwo;
            }
        }

        if(count($arr) >= 3 && count($arr) < 4){
            //dd(count($arr));
            $data[1] = array_merge($arr['time0'],$arr['time1']);
            //$data[2] = array_merge($arr['time2'],$arr['time3']);
            $brr = array_merge($data[1],$arr['time2']);
        }

        if(count($arr) >= 4){
            $data[1] = array_merge($arr['time0'],$arr['time1']);
            $data[2] = array_merge($arr['time2'],$arr['time3']);
            $brr = array_merge($data[1],$data[2]);
        }
        //dd($brr);
        $cnt = count($brr);
        for($i=0;$i < $cnt;$i++){
            if($i%2 == 0){
                $begintime[] = $brr[$i];
            }else{
                $endtime[] = $brr[$i];
            }
        }
        for($j=0;$j < $cnt/2;$j++){
            $where[$j]['begintime'] = $begintime[$j];
            $where[$j]['endtime'] = $endtime[$j];
        }
        $user = Auth::user();

        foreach($date as $kk=>$dv){
            $datearr = explode('-',$dv);
            $post[$kk]['year'] = $datearr[0];
            $post[$kk]['month'] = $datearr[1];
            $post[$kk]['day'] = $datearr[2];

        }
       foreach($post as $aa=>$pp){
           $post[$aa]['lab_id'] = Input::get('lid');
           $post[$aa]['created_user_id'] = $user->id;
           $post[$aa]['created_at'] = date('Y-m-d H:i:s');
           $post[$aa]['updated_at'] = date('Y-m-d H:i:s');
       }
        foreach($where as $o=>$time){
            $plan[] = array_merge($time,$post[0]);
        }
        try{
            foreach($plan as $t1=>$v1){
                $begintime = explode('*',$v1['begintime']);
                $endtime = explode('*',$v1['endtime']);
                $plan[$t1]['begintime'] = $begintime[1];
                $plan[$t1]['endtime'] = $endtime[1];
                $plan[$t1]['period_type'] = $this->get_n($endtime[0]);
            }
            OpenPlan::insert($plan);

        } catch (Exception $e){
            DB::connection('msc_mis')->rollback();
            return ['status'=>1,'info'=>$e];exit;
        }



        if(@$post[1]){
            foreach ($where as $o => $time) {
                $plan1[] = array_merge($time, $post[1]);
            }
            try{
                foreach($plan1 as $t1=>$v1){
                    $begintime = explode('*',$v1['begintime']);
                    $endtime = explode('*',$v1['endtime']);
                    $plan1[$t1]['begintime'] = $begintime[1];
                    $plan1[$t1]['endtime'] = $endtime[1];
                    $plan1[$t1]['period_type'] = $this->get_n($endtime[0]);
                }
                OpenPlan::insert($plan1);
            } catch (Exception $e){
                DB::connection('msc_mis')->rollback();
                return ['status'=>1,'info'=>$e];exit;
            }
        }
        if(@$post[2]) {
            foreach ($where as $o => $time) {
                $plan2[] = array_merge($time, $post[2]);
            }
            try{
                foreach($plan2 as $t1=>$v1){
                    $begintime = explode('*',$v1['begintime']);
                    $endtime = explode('*',$v1['endtime']);
                    $plan2[$t1]['begintime'] = $begintime[1];
                    $plan2[$t1]['endtime'] = $endtime[1];
                    $plan2[$t1]['period_type'] = $this->get_n($endtime[0]);
                }
                OpenPlan::insert($plan2);
            } catch (Exception $e){
                DB::connection('msc_mis')->rollback();
                return ['status'=>1,'info'=>$e];exit;
            }
        }
        if(@$post[3]) {
            foreach ($where as $o => $time) {
                $plan3[] = array_merge($time, $post[3]);
            }
            try{
                foreach($plan3 as $t1=>$v1){
                    $begintime = explode('*',$v1['begintime']);
                    $endtime = explode('*',$v1['endtime']);
                    $plan3[$t1]['begintime'] = $begintime[1];
                    $plan3[$t1]['endtime'] = $endtime[1];
                    $plan3[$t1]['period_type'] = $this->get_n($endtime[0]);
                }
                OpenPlan::insert($plan3);
            } catch (Exception $e){
                DB::connection('msc_mis')->rollback();
                return ['status'=>1,'info'=>$e];exit;
            }
        }
        DB::connection('msc_mis')->commit();
        //當前添加的實驗室的开放日历
        $labdata = $this->get_lab_cleander(Input::get('lid'));
        return ['status'=>1,'info'=>'操作成功','data'=>$labdata];
    }


    /**
     * Created by PhpStorm.
     * User: weihuiguo
     * Date: 2016年1月11日11:35:27
     * 修改实验室开放时间
     */
    public function postDoEditLabCleander(){

    }

    /**
     * Created by PhpStorm.
     * User: weihuiguo
     * Date: 2016年1月11日11:35:27
     * 实验室预约记录审核
     */
    public function getLabOrderList(){

        return view('msc::admin.labmanage.lab_maintain',[
        ]);
    }
}