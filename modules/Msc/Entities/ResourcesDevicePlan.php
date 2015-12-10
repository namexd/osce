<?php
/**
 * Created by PhpStorm.
 * User: wangjiang
 * Date: 2015/12/7 0007
 * Time: 16:18
 */

namespace Modules\Msc\Entities;
use Illuminate\Support\Facades\Auth;
use DB;

class ResourcesDevicePlan extends  CommonModel {
    protected $connection	=	'msc_mis';
    protected $table 		= 	'resources_device_plan';
    public    $timestamps	=	true;
    protected $primaryKey	=	'id';
    public    $incrementing	=	true;
    protected $fillable 	=	['id', 'resources_device_id', 'currentdate', 'begintime', 'endtime', 'status'];
    /*
     * 设备关联
     */
    public function device(){
        return $this->belongsTo('Modules\Msc\Entities\ResourcesDevice','resources_device_id','id');
    }

    /*
     * 设备申请人关联
     */
    public function applyer(){
        return $this->belongsTo('Modules\Msc\Entities\Student','resources_device_id','id');
    }
    public function opertionUser(){
        return $this->hasOne('App\Entities\User','id', 'opertion_uid' );
    }
    public function student(){
        return $this->hasOne('Modules\Msc\Entities\Student','id', 'opertion_uid' );
    }
    public function getStatusAttribute($value){
        switch ($value) {
            case -3:
                $name = '不允许预约';
                break;
            case -2:
                $name = '未使用';
                break;
            case -1:
                $name = '已取消';
                break;
            case 0:
                $name = '已预约未使用';
                break;
            case 1:
                $name = '使用中';
                break;
            case 2:
                $name = '已使用';
                break;

            default:
                $name = '-';
        }
        return $name;
    }
    /**
     * 获取当前用户开放设备外借历史列表
     * @access public
     *
     * @return pagination
     *
     * @version 0.7
     * @author Luohaihua <Luohaihua@misrobot.com>
     * @date
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     *
     */
    public function getUserOpenDeviceHistroy($uid)
    {
        return $this    ->  where('opertion_uid','=',$uid)
                        ->  paginate(config('msc.page_size'));
    }
    /**
     * 获取设备相关信息
     * @access public
     *
     *
     * @version 0.7
     * @author tangjun <tangjun@misrobot.com>
     * @date
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     *
     */
    public function getDeviceInfo($data,$status = 0){
        if(!empty($data['resources_device_id'])){
            $timeYMD = date('Y-m-d',time());
            $timeHIS = date('H:i:s',time());
            $thisBuilder = $this->where('resources_device_id','=',$data['resources_device_id'])
                ->where('currentdate','=',$timeYMD)
                ->where('begintime','<',$timeHIS)
                ->where('endtime','>',$timeHIS)
                ->where('status','=',$status);
            if(!empty($data['uid'])){
                $thisBuilder = $thisBuilder->where('opertion_uid','=',$data['uid']);
            }
        }
        $result = $thisBuilder->with(['device'=>function($q){
            $q->with('ResourcesClassroom');
        },'user','ResourcesDeviceHistory'])->first();
        return  $result;
    }

    //计划表和用户表的关联
    public function user(){
        return $this->hasOne('App\Entities\User','id','opertion_uid');
    }

    //计划表和历史表的关联
    public function ResourcesDeviceHistory(){
        return  $this->hasOne('Modules\Msc\Entities\ResourcesDeviceHistory','resources_device_plan_id','id');
    }

    //获取历史记录详情数据
    public function getHistoryDetail($data){
        $thisBuilder = $this->where('status','=',2);
        if(!empty($data['id'])){
            $thisBuilder = $thisBuilder->where('id','=',$data['id']);
        }
        $result = $thisBuilder->with(['ResourcesDevice'=>function($q){
            $q->with('ResourcesClassroom');
        }])->get();
        return $result;
    }

    // 检查某个开放设备某个时间段是否被占用-占用了：true 没有：false
    public function checkConflicts ($deviceId, $date, $beginTime, $endTime)
    {
        return $this->where(function ($query) use($deviceId, $date) {
            $query->where('resources_device_id', $deviceId)
                  ->whereRaw(
                      'unix_timestamp(currentdate)=? ',
                      [strtotime($date)]
                  );
        })->where(function ($query) use($beginTime, $endTime) {
            $query->whereRaw(
                'unix_timestamp(begintime)>? and unix_timestamp(begintime)<? ',
                [strtotime($beginTime), strtotime($endTime)]
            );
        })->orWhere(function ($query) use($beginTime, $endTime) {
            $query->whereRaw(
                'unix_timestamp(endtime)>? and unix_timestamp(endtime)<? ',
                [strtotime($beginTime), strtotime($endTime)]
            );
        })->get() ? true : false;
    }


    public function historyStatistics($grade='',$date='',$status='',$professional=''){
        return $this   ->  with(['student'=>function($q) use($grade,$professional){
            if(!empty($grade))
            {
                $q->where('grade','=',$grade);
            }
            if(!empty($professional))
            {
                $q->where('professional','=',$professional);
            }
        }])
        ->groupBy('resources_device_id')
        ->select(DB::raw('count(*) as borrowCount, resources_device_id,status'))
        ->get();
    }

    // 使用完开放设备后更新计划表状态  历史表写入结束时间
    public function afterUseDevice ($planId, $historyId)
    {
        DB::beginTransaction();

        // 计划表状态改为“已使用”
        $result = $this->where('id', '=', $planId)->update(['status'=>2]);
        if (!$result)
        {
            DB::rollback();
            throw new \Exception('更新计划表状态失败');
        }

        // 2.历史表写入结束时间
        $resourcesDeviceHis = new ResourcesDeviceHistory();
        $result = $resourcesDeviceHis->where('id', '=', $historyId)->update(['end_datetime'=>date('Y-m-d H:i:s', time())]);
        if (!$result)
        {
            DB::rollback();
            throw new \Exception('结束时间写入历史记录表失败');
        }

        DB::commit();
    }


}