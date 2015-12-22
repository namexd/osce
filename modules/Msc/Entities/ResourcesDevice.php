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
    protected $fillable 	=	['id', 'resources_lab_id', 'name', 'code', 'resources_device_cate_id', 'max_use_time','warning','detail','status'];

    public function classroom() {
        return $this->belongsTo('Modules\Msc\Entities\ResourcesClassroom','id','resources_lab_id');
    }

    public function device_cate() {
        return $this->hasMany('Modules\Msc\Entities\ResourcesDeviceCate','id','resources_device_cate_id');
    }


    // 根据日期获取某一类可预约的开放设备列表
    public function getAvailableList ($cateId, $date)
    {
        //根据$cateId查到对应的实验室
        //resources_lab的opened为2的均为开放实验室
        //根据实验室找到对应的开放时间
        $builder = $this    ->  leftJoin (
            'resources_lab',
            function ($join) use ($cateId) {
                $join   ->  on('resources_lab.id' ,'=' ,$this->table.'.resources_lab_id');
            }
        )   ->  leftJoin (
            'resources_openlab_calendar',
            function ($join) {
                $join   ->  on('resources_openlab_calendar.resources_lab_id' ,'=' ,'resources_lab.id');
            }
        )   ->where ($this->table.'.resources_device_cate_id' ,'=' , $cateId)
            ->where ('resources_lab.opened' ,'=' ,2);
        //将此时查询到的数据转化为数组
        $array = $builder->select([
            'resources_lab.name as name',
            'resources_openlab_calendar.week as week',
        ])    -> get()    ->toArray();
        //循环该数组，得到当天是否开放
        $result = [];
        foreach ($array as $item) {
            $weekend = date('N',strtotime($date));
            //实验室在星期几开放
            $weekOpen = $item['week'];
            $weekOpen = explode(',',$weekOpen);
            if (in_array($weekend,$weekOpen)) {
                //说明实验室当天开放
                //查询有哪些设备可以被选择
                $judgment = $this -> leftJoin (
                    'resources_device_plan',
                    function ($join) {
                        $join -> on('resources_device_plan.resources_device_id','=',$this->table.'.id');
                    }
                )   ->  whereRaw(
                    'unix_timestamp(resources_device_plan.currentdate) = ? ',
                    [strtotime($date)]
                )   ->  select([
                        'resources_device_plan.currentdate as currentdate',
                    ])  -> first();
                //如果$judgment是NULL，就说明当前没有被占用
                if ($judgment['currentdate'] == null) {
                    //链表查询开放设备图片
                    $result = $this   ->    leftJoin(
                        'resources',
                        function($join) {
                            $join   ->  on ('resources.item_id' ,'=' , $this->table.'.id');
                        }
                    )   ->  leftJoin (
                        'resources_image',
                        function ($join) {
                            $join -> on ('resources_image.resources_id','=','resources.id');
                        }
                    )   -> where('resources.type','=','OPENDEVICE')
                        -> select([
                        $this->table.'.name as name',
                        $this->table.'.code as code',
                        $this->table.'.id as id',
                        'resources_image.url as url', // 图片
                    ]);
                }
            }
        }
        return $result   ->   paginate(config('msc.page_size',10));


//        // 能申请的开放设备ids
//        $devices = $this->leftJoin('resources_device_plan', function ($join) {
//            $join->on('resources_device_plan.resources_device_id', '=', 'resources_device.id');
//        })->whereRaw(
//            'unix_timestamp(resources_device_plan.currentdate)=? ',
//            [strtotime($date)]
//        )   ->where('resources_device.resources_device_cate_id', '=', $cateId)
////            ->whereNotIn('resources_device_plan.status', [0,1]) // -3=不允许预约 ，-2=已过期(未使用)  -1=已取消 0=已预约未使用 1=使用中 2=已使用
//            ->select([
//            'resources_device.id as id',
//        ])->get();
//
//
//
//        $ids = [];
//        if ($devices)
//        {
//            foreach ($devices as $device)
//            {
//                $ids[] = $device->id;
//            }
//        }
//
//
//        // 可以申请的开放设备
//        return $this->leftJoin('resources', function ($join) use($ids) {
//            $join->on('resources.item_id', '=', 'resources_device.id')
//                 ->where('resources.type', '=', 'OPENDEVICE');
//        })->leftJoin('resources_image', function ($join){
//            $join->on('resources_image.resources_id', '=', 'resources.id');
//        })->whereIn('resources_device.id', $ids)
//          //->where('resources_device.resources_device_cate_id', '=', $cateId)
//          ->select([
//              'resources_device.id as id', // 编号
//              'resources_device.name as name', // 名称
//              'resources_device.code as code', // 编码
//              'resources_image.url as url', // 图片
//          ])->paginate(config('msc.page_size',10));
    }

    // 根据开放设备id和日期获得可预约时段及其状态
    public function getTimeList ($id, $date)
    {
        $item = $this->leftJoin('resources_lab', function ($join) use($id){
            $join->on('resources_lab.id','=','resources_device.resources_lab_id')
                 ->where('resources_device.id', '=', $id);
        })->select([
            'resources_device.name as name', // 设备名字
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
            $temp['name'] = $item->name;


            $data[] = $temp;
        }

        return $data;
    }

    //设备表和实验室的关系
    public function ResourcesClassroom(){
        return $this->hasOne('Modules\Msc\Entities\ResourcesClassroom','id','resources_lab_id');
    }

    /**
     * 新增加开放设备的方法
     * 传入参数为两个：1为本表字段的数组，2为图片URL的地址
     */
    public function addDeviceResources($formData,$imagesPath) {
        try{
            $this->beginTransaction();
            $resources = $this->create($formData);
            if (!$resources) {
                throw new \Exception('新增开放设备失败！');
            }

            //创建一个数组，为resources表准备数据
            $_formData = [
                'type'        => 'DEVICE',
                'item_id'     => $resources->id,
                'description' => '',
            ];

            //将该数据插入到resources表
            $_resources = Resources::create($_formData);
            if (!$_resources) {
                throw new \Exception('新增开放设备失败！');
            }

            //将图片地址插入资源图片表中
            if (!empty($imagesPath)) {
                foreach ($imagesPath as $item) {
                    $data = [
                        'resources_id'      =>      $_resources->id,
                        'url'               =>      $item,
                        'order'             =>      0,
                        'descrption'        =>      '',
                    ];
                    $result = ResourcesImage::create($data);
                    if (!$result) {
                        throw new \Exception('开放设备图片保存失败');
                    }
                }
            }

            $this->commit();
            return true;
        } catch (\Exception $ex) {
            $this->rollback();
            return $ex;
        }
    }
}