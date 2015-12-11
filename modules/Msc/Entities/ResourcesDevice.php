<?php
/**
 * Created by PhpStorm.
 * User: wangjiang
 * Date: 2015/12/7 0007
 * Time: 16:15
 */

namespace Modules\Msc\Entities;

use Modules\Msc\Repositories\Common as MscCommon;
use Modules\Msc\Entities\ResourcesDevicePlan;

class ResourcesDevice extends  CommonModel {
    protected $connection	=	'msc_mis';
    protected $table 		= 	'resources_device';
    public    $timestamps	=	true;
    protected $primaryKey	=	'id';
    public    $incrementing	=	true;
    protected $fillable 	=	['id', 'resources_lab_id', 'name', 'code', 'resources_device_cate_id', 'max_use_time'];

    // 根据日期获取某一类可预约的开放设备列表
    public function getAvailableList ($cateId, $date)
    {
        // 不能申请的开放设备ids
        $devices = $this->leftJoin('resources_device_plan', function ($join) use($date) {
            $join->on('resources_device_plan.resources_device_id', '=', 'resources_device.id')
                 //->where('resources_device.resources_device_cate_id', '=', $cateId)
                 ->whereIn('resources_device_plan.status', [0,1]); // -3=不允许预约 ，-2=已过期(未使用)  -1=已取消 0=已预约未使用 1=使用中 2=已使用
        })->whereRaw(
            'unix_timestamp(resources_device_plan.currentdate)=? ',
            [strtotime($date)]
        )->select([
            'resources_device.id as id',
        ])->get();


        $ids = [];
        if ($devices)
        {
            foreach ($devices as $device)
            {
                $ids[] = $device->id;
            }
        }


        // 可以申请的开放设备
        return $this->leftJoin('resources', function ($join) use($ids) {
            $join->on('resources.item_id', '=', 'resources_device.id')
                 ->where('resources.type', '=', 'OPENDEVICE');
        })->leftJoin('resources_image', function ($join){
            $join->on('resources_image.resources_id', '=', 'resources.id');
        })->whereNotIn('resources_device.id', $ids)
          ->where('resources_device.resources_device_cate_id', '=', $cateId)
          ->select([
              'resources_device.id as id', // 编号
              'resources_device.name as name', // 名称
              'resources_device.code as code', // 编码
              'resources_image.url as url', // 图片
          ])->paginate(config('msc.page_size',10));
    }

    // 根据开放设备id和日期获得可预约时段及其状态
    public function getTimeList ($id, $date)
    {
        $item = $this->leftJoin('resources_lab', function ($join) use($id){
            $join->on('resources_lab.id','=','resources_device.resources_lab_id')
                 ->where('resources_device.id', '=', $id);
        })->select([
            'resources_lab.begintime as begintime', // 实验室开门时间
            'resources_lab.endtime as endtime', // 实验室关门时间
            'resources_device.max_use_time as max_use_time', // 仪器使用一次时间(min)
        ])->firstOrFail();


        $timeSecs = MscCommon::devide_time_sec($item->begintime, $item->endtime, $item->max_use_time);

        $resourcesDevicePlan = new ResourcesDevicePlan();
        $data = [];
        foreach ($timeSecs as $key => $timeSec)
        {
            $temp = [];

            $timeArray = explode('-', $timeSec);
            $beginTime = $timeArray['0'];
            $endTime   = $timeArray['1'];

            $plans = $resourcesDevicePlan
                ->where('resources_device_plan.resources_device_id', '=', $id)
                ->whereIn('resources_device_plan.status', [0,1]) // -3=不允许预约 ，-2=已过期(未使用)  -1=已取消 0=已预约未使用 1=使用中 2=已使用
                ->where(function ($query) use($date, $beginTime, $endTime) {
                    $query->whereRaw(
                        'unix_timestamp(resources_device_plan.begintime)>? and unix_timestamp(resources_device_plan.begintime)<? and unix_timestamp(resources_device_plan.currentdate)=? ',
                        [strtotime($beginTime), strtotime($endTime), strtotime($date)]
                    );
                })->orWhere(function ($query) use($date, $beginTime, $endTime) {
                    $query->whereRaw(
                        'unix_timestamp(resources_device_plan.endtime)>? and unix_timestamp(resources_device_plan.endtime)<? and unix_timestamp(resources_device_plan.currentdate)=? ',
                        [strtotime($beginTime), strtotime($endTime), strtotime($date)]
                    );
                })->get();

            if (0!=count($plans)) // 有未使用的计划
            {
                $temp['status'] = 1; // 不可预约
            }
            else
            {
                $temp['status'] = 2; // 可预约
            }
            $temp['time'] = $timeSec;
            $temp['id']   = $id;


            $data[] = $temp;
        }

        return $data;
    }

    //设备表和实验室的关系
    public function ResourcesClassroom(){
        return $this->hasOne('Modules\Msc\Entities\ResourcesClassroom','id','resources_lab_id');
    }
}