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
use Modules\Msc\Entities\PlanApply;
use Modules\Msc\Entities\LabApply;
use Modules\Msc\Entities\LabPlan;
use Illuminate\Support\Facades\Cache;
use Modules\Msc\Http\Controllers\MscController;
use URL;
use DB;
class LaboratoryController extends MscController
{

    /**
     * Created by PhpStorm.
     * User: weihuiguo
     * Date: 2015/12/28 0028
     * Time: 17:01
     * 实验室列表
     */
    public function index(Laboratory $Laboratory)
    {
        $keyword = !empty(Input::get('keyword')) ? Input::get('keyword') : null;
        if (Input::get('status') >= 0) {

            $where['status'] = Input::get('status');
        }
        if (Input::get('open_type') >= 0) {
            $where['open_type'] = Input::get('open_type');
        }
        $where['keyword'] = $keyword;
        //$this->start_sql(1);
        $datalist = $Laboratory->getFilteredPaginateList($where);
        //$this->end_sql(1);
        //$datalist = $datalist->toArray();
        //dd($datalist);
        foreach ($datalist as $k => $v) {
            $v->opentype = $v->open_type;
            $v->open_type = $Laboratory->getType($v->open_type);
            $floor = $v->floors;
            if (is_null($floor) || $floor == '') {
                unset($datalist[$k]);
            }

        }
        //学院
        $school = DB::connection('msc_mis')->table('school')->get();
        //教学楼
        $floor = DB::connection('msc_mis')->table('location')->where('status', '=', 1)->get();
        //获取和老师管理的用户数据
        $teacher = new Teacher();
        $teacher = $teacher->getdata();
        //dd($teacher);
        //dd($teacher);
        return view('msc::admin.labmanage.lab_maintain', [
            'datalist' => $datalist,
            'school' => $school,
            'floor' => $floor,
            'teacher' => $teacher,
            'keyword' => Input::get('keyword') ? Input::get('keyword') : '',
            'status' => Input::get('status') ? Input::get('status') : '',
            'open_type' => Input::get('open_type') ? Input::get('open_type') : '',
        ]);

    }


    /**
     * Created by PhpStorm.
     * User: weihuiguo
     * Date: 2015/12/28 0028
     * Time: 17:01
     * 新加实验室操作
     */
    public function getAddLabInsert(Request $Request)
    {
        $this->validate($Request, [
            'name' => 'required',
            'open_type' => 'required',
            'manager_user_id' => 'required|integer',
            'status' => 'required',
            'floor' => 'required',
            'code' => 'required',
            'total' => 'required|integer'
        ], [
            "total.required" => "实验室容量必填",
            //"integer"      => ":attribute 长度必须在 :min 和 :max 之间"
        ]);
        $user = Auth::user();
        $data = [
            'name' => Input::get('name'),

            'location_id' => Input::get('building'),
            'open_type' => Input::get('open_type'),
            'manager_user_id' => Input::get('manager_user_id'),
            'floor' => Input::get('floor'),
            'status' => Input::get('status'),
            'created_user_id' => $user->id,
            'floor' => Input::get('floor'),
            'code' => Input::get('code'),
            'total' => Input::get('total'),
        ];
        //dd(Input::get('total'));
        $add = Laboratory::create($data);
        //dd($add);
        if ($data != false) {
            return redirect()->back()->withInput()->withErrors('添加成功');
        } else {
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
    public function getEditLabInsert(Request $Request, Laboratory $laboratory)
    {
        $this->validate($Request, [
            'name' => 'required',

            'open_type' => 'required',
            'manager_user_id' => 'required|integer',
            'status' => 'required',
            'floor' => 'required',
            'code' => 'required',
            'total' => 'required|integer'
        ], [
            "total.required" => "实验室容量必填",
            //"integer"      => ":attribute 长度必须在 :min 和 :max 之间"
        ]);
        $user = Auth::user();
        $data = [
            'name' => Input::get('name'),
            'short_name' => Input::get('short_name'),
            'enname' => Input::get('enname'),
            'short_enname' => Input::get('short_enname'),
            'location_id' => Input::get('building'),
            'open_type' => Input::get('open_type'),
            'manager_user_id' => Input::get('manager_user_id'),
            'floor' => Input::get('floor'),
            'status' => Input::get('status'),
            'created_user_id' => $user->id,
            'floor' => Input::get('floor'),
            'code' => Input::get('code'),
            'total' => Input::get('total'),
        ];
        $add = $laboratory->where('id', '=', urlencode(e(Input::get('id'))))->update($data);
        if ($data != false) {
            return redirect()->back()->withInput()->withErrors('修改成功');
        } else {
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
    public function getStopLab(Laboratory $laboratory)
    {
        $id = urlencode(e(Input::get('id')));
        if ($id) {
            $data = $laboratory->where('id', '=', $id)->update(['status' => Input::get('type')]);
            //dd(Input::get('type'));
            if (Input::get('type')) {
                $name = '启用成功';
            } else {
                $name = '停用成功';
            }
            if ($data != false) {
                return redirect()->back()->withInput()->withErrors($name);
            } else {
                return redirect()->back()->withInput()->withErrors('系统异常');
            }
        } else {
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
    public function getDeleteLab()
    {
        $id = urlencode(e(Input::get('id')));
        //dd($id);
        if ($id) {
            $data = Laboratory::find($id);
            $del = $data->delete();
            //dd($del);
            if ($del != false) {
                return redirect()->back()->withInput()->withErrors('删除成功');
            } else {
                return redirect()->back()->withInput()->withErrors('系统异常');
            }
        } else {
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
    function get_float($ground, $underground)
    {
        $arr = array();
        $brr = array();
        //地下-2楼
        for ($i = $underground; $i > 0; $i--) {
            $arr['-' . $i] = '-' . $i;
        }

        //地上
        for ($i = 1; $i <= $ground; $i++) {
            $arr[$i] = $i;
        }
        $data = array_merge($arr, $brr);
        return $data;
    }

    /**
     * Created by PhpStorm.
     * User: weihuiguo
     * Date: 2015/12/28 0028
     * Time: 17:01
     * 联动查找楼栋
     */
    public function getLocal(Floor $floor)
    {
        $id = Input::get('id');
        $local = $floor->where('school_id', '=', $id)->where('status', '=', 1)->get();
        if ($local != false) {
            return $local;
            exit;
        } else {
            return 0;
            exit;
        }

    }

    /**
     * Created by PhpStorm.
     * User: weihuiguo
     * Date: 2015/12/28 0028
     * Time: 17:01
     * 联动查找楼层
     */
    public function getFloor(Floor $floor)
    {
        if (Input::get('type') == 1) {
            $local_id = Laboratory::where('id', '=', Input::get('id'))->first();
            $floor = $floor->where('id', '=', $local_id->location_id)->first();

        } else {
            $floor = $floor->where('id', '=', Input::get('id'))->first();
        }

        $floorList = $this->get_float($floor['floor_top'], $floor['floor_buttom']);
        //dd($floorList);
        if ($floorList != false) {
            return $floorList;
            exit;
        } else {
            return 0;
            exit;
        }
    }


    //TODO::实验室开放日历
    /**
     * Created by PhpStorm.
     * User: weihuiguo
     * Date: 2016年1月4日11:09:03
     * 实验室开发日历
     */
    public function getLabClearnder()
    {
        $location = Floor::where('status', '=', 1)->get();

        return view('msc::admin.labmanage.open_calendar', [
            'location' => $location,
        ]);
    }

    //二维数组去重
    function er_array_unique($arr)
    {
        $newarr = array();
        if (is_array($arr)) {
            foreach ($arr as $v) {
                if (!in_array($v, $newarr, true)) {
                    $newarr[] = $v;
                }
            }
        } else {
            return false;
        }
        return $newarr;
    }

    //查找实验室已存在的开放日历
    public function get_lab_cleander($id)
    {
        $lid = $id;
        $cleaner = OpenPlan::where('lab_id', '=', $lid)->where('status', '=', 1)->get();
        $cleaner = $cleaner->toArray();
        //dd($cleaner);
        $array = array();
        foreach ($cleaner as $k => $v) {
            if (array_key_exists('year', $v) || array_key_exists('month', $v) || array_key_exists('day', $v)) {
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
        foreach ($array as $o => $date) {
            $arr[$o]['date'] = $date;
            $arr[$o]['child'] = OpenPlan::where($date)->select('begintime', 'endtime', 'period_type', 'id')->get();
        }
        return $arr;
    }

    //如果进入日历时数据已存在
    public function getEditLabCleander()
    {
        return ['data' => $this->get_lab_cleander(Input::get('id'))];
    }

    /**
     * Created by PhpStorm.
     * User: weihuiguo
     * Date: 2016年1月4日11:09:03
     * 根据楼栋查找楼层及该楼层所有实验室
     */
    public function getFloorLab()
    {
        $labArr = [];
        $local_id = Input::get('lid');
        $local = Floor::where('id', '=', $local_id)->first();
        $floor = $this->get_float($local['floor_top'], $local['floor_buttom']);
        $where['status'] = 1;
        $where['location_id'] = $local_id;
        $user = Auth::user();
        $role_id = DB::connection('sys_mis')->table('sys_user_role')->where('user_id', '=', $user->id)->first();
        if ($role_id) {
            $role_name = DB::connection('sys_mis')->table('sys_roles')->where('id', '=', $role_id->role_id)->first();
        }

        if (@$role_name->name == '超级管理员') {

        } else {
            $where['manager_user_id'] = $user['id'];
        }
        foreach ($floor as $k => $v) {
            $where['floor'] = $v;
            $labArr[$k]['floor'] = $v;
            $this->start_sql(1);
            $labArr[$k]['lab'] = Laboratory::where($where)->get();
            //$this->end_sql(1);
        }
        return $labArr;
    }

    //判断上午，中午，下午，晚上
    public function get_n($type)
    {
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
    public function postOperatingLabCleander()
    {
        $dataid = Input::get('dateid');
        $count = count($dataid);
        DB::connection('msc_mis')->beginTransaction();
        $date = explode('&', trim(Input::get('date'), '&'));
        $timestr = explode('@', trim(Input::get('timestr'), '@'));

        foreach ($timestr as $k => $v) {
            $arr['time' . $k] = explode('!', trim($v, '!'));
        }

        if (count($arr) > 1 && count($arr) < 3) {
            $brr = array_merge($arr['time0'], $arr['time1']);
        }

        if (count($arr) < 2) {
            foreach ($arr as $lttwo) {
                $brr = $lttwo;
            }
        }

        if (count($arr) >= 3 && count($arr) < 4) {
            //dd(count($arr));
            $data[1] = array_merge($arr['time0'], $arr['time1']);
            //$data[2] = array_merge($arr['time2'],$arr['time3']);
            $brr = array_merge($data[1], $arr['time2']);
        }

        if (count($arr) >= 4) {
            $data[1] = array_merge($arr['time0'], $arr['time1']);
            $data[2] = array_merge($arr['time2'], $arr['time3']);
            $brr = array_merge($data[1], $data[2]);
        }
        //dd($brr);
        $cnt = count($brr);
        for ($i = 0; $i < $cnt; $i++) {
            if ($i % 2 == 0) {
                $begintime[] = $brr[$i];
            } else {
                $endtime[] = $brr[$i];
            }
        }
        for ($j = 0; $j < $cnt / 2; $j++) {
            $where[$j]['begintime'] = $begintime[$j];
            $where[$j]['endtime'] = $endtime[$j];
        }
        $user = Auth::user();

        foreach ($date as $kk => $dv) {
            $datearr = explode('-', $dv);
            $post[$kk]['year'] = $datearr[0];
            $post[$kk]['month'] = $datearr[1];
            $post[$kk]['day'] = $datearr[2];

        }
        foreach ($post as $aa => $pp) {
            $post[$aa]['lab_id'] = Input::get('lid');
            $post[$aa]['created_user_id'] = "$user->id";
            $post[$aa]['created_at'] = date('Y-m-d H:i:s');
            $post[$aa]['updated_at'] = date('Y-m-d H:i:s');
        }

        for ($q = 0; $q < count($post); $q++) {
            foreach ($where as $o => $time) {
                $plan1[] = array_merge($time, $post[$q]);
            }
            try {
                foreach ($plan1 as $t1 => $v1) {
                    $begintime = explode('*', $v1['begintime']);
                    $endtime = explode('*', $v1['endtime']);
                    $plan1[$t1]['begintime'] = @$begintime[1];
                    $plan1[$t1]['endtime'] = @$endtime[1];
                    $plan1[$t1]['period_type'] = $this->get_n($endtime[0]);
                }

                $cnt = count($plan1);
                if ($dataid) {
                    if ($cnt > $count) {
                        for ($i = 0; $i < $cnt; $i++) {
                            unset($plan1[$i]['created_at']);
                            //当时间数大于ID数

                            if ($i < $count) {
                                OpenPlan::where('id', '=', $dataid[$i])->update($plan1[$i]);
                            } else {
                                OpenPlan::insert($plan1[$i]);
                            }
                        }
                    } else {
                        for ($i = 0; $i < $count; $i++) {
                            unset($plan1[$i]['created_at']);
                            //时间数小于ID数

                            if ($i < $cnt) {
                                OpenPlan::where('id', '=', $dataid[$i])->update($plan1[$i]);
                            } else {
                                OpenPlan::where('id', '=', $dataid[$i])->delete();
                            }
                        }


                    }
                } else {
                    OpenPlan::insert($plan1);
                }
                //exit;
            } catch (Exception $e) {
                DB::connection('msc_mis')->rollback();
                return ['status' => 1, 'info' => $e];
                exit;
            }
        }

        DB::connection('msc_mis')->commit();
        //當前添加的實驗室的开放日历
        $labdata = $this->get_lab_cleander(Input::get('lid'));
        return ['status' => 1, 'info' => '操作成功', 'data' => $labdata];
    }


    /**
     * Created by PhpStorm.
     * User: weihuiguo
     * Date: 2016年1月11日11:35:27
     * 实验室预约记录审核
     */
    public function getLabOrderList(LabApply $LabApply)
    {
        $keyword = Input::get('keyword') ? Input::get('keyword') : '';
        $type = Input::get('type') ? Input::get('type') : 1;
        $user = Auth::user();
        $role = DB::connection('sys_mis')->table('sys_user_role')->where('user_id','=',$user->id)->first();
        $role_name = DB::connection('sys_mis')->table('sys_roles')->where('id','=',@$role->role_id)->first();

        if($role_name){
            if($role_name->name == "超级管理员"){
                $id = '';
            }else{
                $id = $user->id;
            }
        }else{
            $id = $user->id;
        }
        //$this->start_sql(1);
        $LabOrderList = $LabApply->get_check_list($keyword, $type,$id);
        //$this->end_sql(1);
        foreach ($LabOrderList as $v) {
            $v->address = $v->labname . $v->floor . '楼' . $v->code;
            if (empty($v->begintime) && empty($v->endtime)) {
                foreach ($v->PlanApply as $plan) {
                    $v->playdate .= date('H:i', strtotime(@$plan->OpenPlan->begintime)) . ' ~ ' . date('H:i', strtotime(@$plan->OpenPlan->endtime)) . '<br>';
                    $v->playyear = date('Y', strtotime(@$plan->OpenPlan->year)) . '-' . date('m', strtotime(@$plan->OpenPlan->month)) . '-' . date('d', strtotime(@$plan->OpenPlan->day));
                }
            } else {
                $v->begintime = date('H:i', strtotime($v->begintime));
                $v->endtime = date('H:i', strtotime($v->endtime));
            }
            //$v->playdate = htmlentities($v->playdate);
        }
        //dd($LabOrderList);
        // exit;
        if ($type == 1) {
            $view = 'booking_examine';
        } else {
            $view = 'booking_examine_other';
        }
        return view('msc::admin.labmanage.' . $view, [
            'LabOrderList' => $LabOrderList,
            'type' => $type
        ]);
    }

    /**
     * Created by PhpStorm.
     * User: weihuiguo
     * Date: 2016年1月11日11:35:27
     * 判断是否冲突
     */
    public function _check()
    {
        $id = Input::get('id');
        $open_plan_id = PlanApply::where('apply_id', '=', $id)->first();
        if ($open_plan_id) {
            $PlanApply = PlanApply::where('open_plan_id', '=', $open_plan_id->open_plan_id)->get();
            //当前日历ID存在多个
            if (count($PlanApply) > 1) {

                //循环判断是否有老师并统计学生人数
                foreach ($PlanApply as $v) {
                    $LabApply[] = LabApply::where('id', '=', $v->apply_id)->first();
                }
                $t = 0;
                $s = 0;
                // dd($LabApply);
                foreach ($LabApply as $apply) {
                    if (@$apply->type == 2 && @$apply->user_type == 2) {
                        $t++;
                    } else {
                        $s++;
                    }
                }
                //dd($s.$t);
                if ($t >= 1) {
                    $data = [
                        'status' => 0,
                        'info' => '该实验室已经有 学员（' . $s . '人）预约使用，确认通过教师预约？通过教师预约会取消之前的预约记录，请确认已经提前做好沟通!'
                    ];
                    return $data;
                    exit;
                } else {
                    $data = [
                        'status' => 1,
                    ];
                    return $data;
                    exit;
                }
            } else {
                $data = [
                    'status' => 1,
                ];
                return $data;
                exit;
            }

        } else {
            $data = [
                'status' => 1,
            ];
            return $data;
            exit;
        }
    }

    /**
     * Created by PhpStorm.
     * User: weihuiguo
     * Date: 2016年1月11日11:35:27
     * 实验室预约记录单条审核
     */
    public function getLabOrderCheck(LabApply $LabApply, OpenPlan $OpenPlan)
    {
        DB::connection('msc_mis')->beginTransaction();
        $id = Input::get('id');
        $data['status'] = Input::get('type');
        if ($id) {
            $datadetail = $LabApply->getonelaborderdetail($id);
            $datadetail = $datadetail->toArray();

            //审核通过
            if (!@Input::get('description')) {
                //dd($datadetail['plan_apply']);
                if ($datadetail['plan_apply']) {
                    foreach ($datadetail['plan_apply'] as $v) {

                        if ($v['open_plan']) {
                            if ($datadetail['total'] >= $v['open_plan']['apply_num']) {
                                $do = $LabApply->where('id', '=', $id)->update($data);
                                if (!$do) {
                                    DB::connection('msc_mis')->rollBack();
                                    return redirect()->back()->withInput()->withErrors('系统异常');
                                }
                            } else {
                                return redirect()->back()->withInput()->withErrors($data['labname'] . '预约已满');
                            }
                            //dd(111);
                        } else {
                            //dd($v['open_plan']);
                            $do = $LabApply->where('id', '=', $id)->update($data);
                            if ($do != 1) {
                                DB::connection('msc_mis')->rollBack();
                                return redirect()->back()->withInput()->withErrors('系统异常');
                            }
                        }

                    }
                    //exit;
                    DB::connection('msc_mis')->commit();
                    return redirect()->back()->withInput()->withErrors('操作成功');
                } else {
                    $do = $LabApply->where('id', '=', $id)->update($data);
                    if (!$do) {
                        DB::connection('msc_mis')->rollBack();
                        return redirect()->back()->withInput()->withErrors('系统异常');
                    }
                    DB::connection('msc_mis')->commit();
                    return redirect()->back()->withInput()->withErrors('操作成功');
                }


            } else {
                //审核不通过
                $data['refuse_reason'] = Input::get('description');
                $editLabApply = LabApply::where('id', '=', $id)->update($data);
                if ($datadetail['plan_apply']) {
                    //有实验室日历预约
                    if ($editLabApply === false) {
                        DB::connection('msc_mis')->rollBack();
                        return redirect()->back()->withInput()->withErrors('系统异常');
                    } else {
                        $dellabplan = LabPlan::where('lab_apply_id', '=', $id)->delete();
                        if ($editLabApply === false && editLabApply != 0) {
                            DB::connection('msc_mis')->rollBack();
                            return redirect()->back()->withInput()->withErrors('系统异常');
                        } else {
                            $planplay[] = PlanApply::where('apply_id', '=', $id)->get();
                            if (!$planplay) {
                                DB::connection('msc_mis')->commit();
                                return redirect()->back()->withInput()->withErrors('操作成功');
                            }
                        }
                    }
                    if (!@$planplay) {
                        foreach ($planplay as $k => $v) {
                            $planplay[$k] = $v->toArray();

                        }
                        foreach ($planplay as $k => $v) {
                            if ($v) {
                                $apply_num = OpenPlan::where('id', '=', $v[0]['open_plan_id'])->first();
                                //$total = $LabApply->get_total($v);
                                if ($apply_num > 0) {
                                    $editopenplay = OpenPlan::where('id', '=', $v[0]['open_plan_id'])->decrement('apply_num', 1);
                                }
                                if ($editopenplay === false && $editopenplay != 0) {
                                    DB::connection('msc_mis')->rollBack();
                                    return redirect()->back()->withInput()->withErrors('系统异常');
                                }
                            }
                        }
                    }

                } else {
                    //没有实验室日历预约
                    if ($editLabApply === false) {
                        DB::connection('msc_mis')->rollBack();
                        return redirect()->back()->withInput()->withErrors('系统异常');
                    }
                }

                DB::connection('msc_mis')->commit();
                return redirect()->back()->withInput()->withErrors('操作成功');
            }


        } else {
            return redirect()->back()->withInput()->withErrors('系统异常');
        }
    }

    /**
     * Created by PhpStorm.
     * User: weihuiguo
     * Date: 2016年1月11日11:35:27
     * 实验室预约记录详情
     */
    public function getLabOrderdetail(LabApply $LabApply)
    {
        $id = Input::get('id');
        $laborderdetail = $LabApply->getonelaborderdetail($id);
        //dd($laborderdetail);
        if (empty($laborderdetail->begintime) && empty($laborderdetail->endtime) && !empty($laborderdetail->PlanApply)) {
            foreach ($laborderdetail->PlanApply as $plan) {
                @$laborderdetail->playdate .= date('H:i', strtotime($plan->OpenPlan->begintime)) . ' ~ ' . date('H:i', strtotime($plan->OpenPlan->endtime)) . ' , ';
                @$laborderdetail->playyear = date('Y', strtotime($plan->OpenPlan->year)) . '-' . date('m', strtotime($plan->OpenPlan->month)) . '-' . date('d', strtotime($plan->OpenPlan->day));
            }


        } else {
            $laborderdetail->begintime = date('H:i', strtotime(@$laborderdetail->begintime));
            $laborderdetail->endtime = date('H:i', strtotime(@$laborderdetail->endtime));
        }
        return $laborderdetail;
    }

    //查找一維數組重複元素
    public function array_repeat($arr)
    {
        if (!is_array($arr)) return $arr;

        $arr1 = array_unique($arr);

        $arr3 = array_diff_key($arr, $arr1);

        return array_unique($arr3);
    }

    /**
     * Created by PhpStorm.
     * User: weihuiguo
     * Date: 2016年1月11日11:35:27
     * 实验室预约记录批量审核
     */
    public function postLabOrderallcheck(LabApply $LabApply, OpenPlan $OpenPlan)
    {

        DB::connection('msc_mis')->beginTransaction();
        $str = trim(Input::get('idstr'), ',');
        $arr = explode(',', $str);
        $data = $LabApply->getonelaborderdata($arr);
        //dd($arr);
        $data = $data->toArray();
        $arr = array();

        foreach ($data as $k => $LabApply) {
            if ($LabApply['plan_apply']) {
                foreach ($LabApply['plan_apply'] as $ke => $v) {
                    $data[$k]['order'] = $v['open_plan']['apply_num'] ? $v['open_plan']['apply_num'] : 0;
                }
            } else {
                $data[$k]['order'] = 0;
            }

        }
        //dd($data);
        //获取所有申请的ID并取出重复的
        foreach ($data as $k => $LabApply) {
            if ($LabApply['type'] == 2) {
                foreach ($LabApply['plan_apply'] as $ke => $v) {
                    $arr[] = $v['open_plan_id'];
                }

                $brr = $this->array_repeat($arr);
            }
        }

        //根据所有申请的ID并取出重复的数据
        foreach ($data as $ke => $LabApply) {
            if ($LabApply['type'] == 2) {
                foreach ($LabApply['plan_apply'] as $k => $v) {
                    if (in_array($v['open_plan_id'], $brr)) {
                        $crr[] = $data[$ke];
                    }
                }
                $check[] = $LabApply['user_type'];
            }
        }
        //统计老师类型出现的次数
        $checkteacher = array_count_values($check);
        if (!@$checkteacher[2]) {
            $checkteacher[2] = 0;
        }
        //冲突
        //1.批量选择时包含老师{
        if ($checkteacher[2] == 1) {
            if (!Input::get('teacher')) {
                $info = [
                    'status' => 0,
                    'info' => '该实验室已经有 学员预约使用，确认通过教师预约？通过教师预约会取消之前的预约记录，请确认已经提前做好沟通!'
                ];
                return $info;
                die;
            }
            foreach ($data as $k => $v) {
                if ($v['user_type'] == 2) {
                    //  通过老师
                    $LabApply = LabApply::where('id', '=', $v['id'])->update(['status' => 2]);
                } else {
                    //  不通过学生
                    $LabApply = LabApply::where('id', '=', $v['id'])->update(['status' => 3, 'refuse_reason' => '你的' . $v['labname'] . '预约与老师的预约冲突']);
                }
                if ($LabApply === false) {
                    DB::connection('msc_mis')->rollBack();
                    $info = [
                        'status' => 2,
                        'info' => '系统异常111！'
                    ];
                    return $info;
                    die;
                }
            }
        } elseif ($checkteacher[2] > 1) {
            //3.包含两个以上老师
            $info = [
                'status' => 3,
                'info' => '开放实验室预约存在老师与老师的冲突，请电话联系'
            ];
            return $info;
            die;
        } else {
            //2.不包含老师{
            foreach ($data as $k => $v) {
                //  直接通过所有
                if ($v['total'] >= $v['order']) {
                    $LabApply = LabApply::where('id', '=', $v['id'])->update(['status' => 2]);
                    if (!$LabApply) {
                        $info = [
                            'status' => 2,
                            'info' => '系统异常222！'
                        ];
                        return $info;
                        die;
                    }
//                        //var_dump($v['plan_apply']);//var_dump($v['type']);
//                        if(!empty($v['plan_apply']) && !is_null($v['plan_apply']) && !$v['plan_apply']){
//                            $apply_num = @$v['plan_apply'][0]['open_plan']['apply_num']+1;
//                            $apply_numDO = @$OpenPlan->where('id','=',$v['plan_apply'][0]['open_plan']['id'])->update(['apply_num'=>$apply_num]);
//                            if(!$apply_numDO){
//                                $info = [
//                                    'status' => 2,
//                                    'info' => '系统异常333！'
//                                ];
//                                return $info;
//                                die;
//                            }
//                        }
                } else {
                    $info = [
                        'status' => 4,
                        'info' => $v['labname'] . '实验室预约已满！'
                    ];
                    return $info;
                    die;
                }

                if ($LabApply === false) {
                    DB::connection('msc_mis')->rollBack();
                    $info = [
                        'status' => 2,
                        'info' => '系统异常444！'
                    ];
                    return $info;
                    die;
                }
            }
            // exit;
        }

        DB::connection('msc_mis')->commit();
        $info = [
            'status' => 1,
            'info' => '操作成功！'
        ];
        return $info;

    }

    /**
     * Created by PhpStorm.
     * User: weihuiguo
     * Date: 2016年1月11日11:35:27
     * 实验室预约记录批量审核不通过
     */
    public function getLabOrderDonot(LabApply $LabApply)
    {
        DB::connection('msc_mis')->beginTransaction();
        $user = Auth::user();
        $idstr = explode(',', trim(Input::get('idstr'), ','));
        $data = [
            'refuse_reason' => Input::get('description'),
            'audit_time' => date('Y-m-d H:i:s'),
            'audit_user' => $user->name,
            'audit_id' => $user->id,
            'status' => 3,
        ];
        foreach ($idstr as $v) {
            $editLabApply = LabApply::where('id', '=', $v)->update($data);

            if ($editLabApply === false) {
                DB::connection('msc_mis')->rollBack();
                return redirect()->back()->withInput()->withErrors('系统异常');
            } else {
                $dellabplan = LabPlan::where('lab_apply_id', '=', $v)->delete();

                if ($editLabApply === false && editLabApply != 0) {
                    DB::connection('msc_mis')->rollBack();
                    return redirect()->back()->withInput()->withErrors('系统异常');
                } else {
                    $planplay[] = PlanApply::where('apply_id', '=', $v)->get();
                    if (!$planplay) {
                        DB::connection('msc_mis')->commit();
                        return redirect()->back()->withInput()->withErrors('操作成功');
                    }
                }
            }
        }
        foreach ($planplay as $k => $v) {
            $planplay[$k] = $v->toArray();

        }
        foreach ($planplay as $k => $v) {
            if ($v) {
                $apply_num = OpenPlan::where('id', '=', $v[0]['open_plan_id'])->first();
                //$total = $LabApply->get_total($v);
                if ($apply_num > 0) {
                    $editopenplay = OpenPlan::where('id', '=', $v[0]['open_plan_id'])->decrement('apply_num', 1);
                }
                if ($editopenplay === false && $editopenplay != 0) {
                    DB::connection('msc_mis')->rollBack();
                    return redirect()->back()->withInput()->withErrors('系统异常');
                }
            }
        }
        //exit;
        DB::connection('msc_mis')->commit();
        return redirect()->back()->withInput()->withErrors('操作成功');
    }
    //数组排序
    public function array_sort($arr,$keys,$type='asc'){
        $keysvalue = $new_array = array();
        foreach ($arr as $k=>$v){
            @$keysvalue[$k] = @$v[$keys];
        }
        if($type == 'asc'){
            asort($keysvalue);
        }else{
            arsort($keysvalue);
        }
        reset($keysvalue);
        foreach ($keysvalue as $k=>$v){
            $new_array[$k] = $arr[$k];
        }
        return $new_array;
    }
    /***
     * 实验室预约查看
     */
    public function getLabOrderShow(Laboratory $Laboratory)
    {
        $nowtime = Input::get('laydate')?Input::get('laydate'):date('Y-m-d');
        $type = Input::get('type')?Input::get('type'):2;
        $user = Auth::user();
        $role = DB::connection('sys_mis')->table('sys_user_role')->where('user_id','=',$user->id)->first();
        $role_name = DB::connection('sys_mis')->table('sys_roles')->where('id','=',@$role->role_id)->first();

        if($role_name){
            if($role_name->name == "超级管理员"){
                $id = '';
            }else{
                $id = $user->id;
            }
        }else{
            $id = $user->id;
        }
        //开放实验室
        if($type == 2){
            $laboratory = $Laboratory->get_opencheck_list($nowtime,$type,$id);
            $laboratory = $laboratory->toArray();
            foreach($laboratory['data'] as $k=>$v) {
                if(empty($v['open_plan'])){
                    unset($laboratory['data'][$k]);
                }else{
                    foreach($v['open_plan'] as $k1=>$v1){
                        $laboratory['data'][$k]['open_plan'][$k1]['begintime'] = date('H:i',strtotime($v1['begintime']));
                        $laboratory['data'][$k]['open_plan'][$k1]['endtime'] = date('H:i',strtotime($v1['endtime']));
                        if(!empty($v1)){
                            if($v1['plan_apply']){
                                foreach($v1['plan_apply'] as $key => $val){
                                    if(!empty($val['lab_apply'])){
                                       // if($val['lab_apply']['user_type'] == 2){
                                            $laboratory['data'][$k]['open_plan'][$k1]['apply_id'] = $val['lab_apply']['id'];
                                            $laboratory['data'][$k]['open_plan'][$k1]['apply_name'] = $val['lab_apply']['user']['name'];
                                            $laboratory['data'][$k]['open_plan'][$k1]['course_name'] = $val['lab_apply']['course_name'];
                                            $laboratory['data'][$k]['open_plan'][$k1]['user_type'] = $val['lab_apply']['user_type'];
                                            $laboratory['data'][$k]['open_plan'][$k1]['apply_time'] = $val['lab_apply']['apply_time'];
                                            break;
                                        //}
                                    }
                                }
                            }else{
                                $laboratory['data'][$k]['open_plan'][$k1]['user_type'] = 1;
                            }

                        }
                    }
                }
            }
            //普通实验室
        }else{
            $laboratory = $Laboratory->get_check_list($nowtime,$type,$id);
            $laboratory = $laboratory->toArray();
            foreach($laboratory['data'] as $k=>&$v){
                if(empty($v['lab_apply'])){
                    unset($laboratory['data'][$k]);
                }else{
                    $laboratory['data'][$k]['cnt'] = count($v['lab_apply']);
                    foreach($v['lab_apply'] as $k1=>&$v1){
                        $v1['begintime'] = date('H:i',strtotime($v1['begintime']));
                        $v1['endtime'] = date('H:i',strtotime($v1['endtime']));
                    }
                }
            }

        }
        return view('msc::admin.labmanage.lab_booking',[
            'Laboratory' => $laboratory,
            'type' => $type,
            'nowtime' => $nowtime,
        ]);
    }


    /**
     * 查找普通实验室预约记录详情
     */
    public function getLabDetail(LabApply $LabApply){
        $id = Input::get('id');
        //dd($id);
        $labapply = $LabApply->getLabdetail($id);
        $labapply = $labapply->toArray();
        //dd($labapply);
        $labapply['begintime'] = date('H:i',strtotime($labapply['begintime']));
        $labapply['endtime'] = date('H:i',strtotime($labapply['endtime']));
        return $labapply;
    }

    /**
     * 查找开放实验室预约记录详情
     */
    public function getStudentLabDetail(LabApply $LabApply){
        $id = Input::get('id');
        $labapply = $LabApply->getStudentLabdetail($id);
        $labapply = $labapply->toArray();
        return $labapply;
    }
}