<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/1/4 0004
 * Time: 11:52
 */

namespace Modules\Msc\Entities;




use Illuminate\Database\Eloquent\Model;

class LadDevice extends Model
{
    public $timestamps	=	true;
    public $incrementing	=	true;
    protected $connection	=	'msc_mis';
    protected $table 		= 	'lab_device';
    protected $primaryKey	=	'id';
    protected $guarded 		= 	[];
    protected $hidden 		= 	[];
    protected $fillable 	=	['lab_id','device_id','total','created_user_id','id'];

    //关联实验室表
    public function LadInfo()
    {
        return $this->hasOne('Modules\Msc\Entities\Laboratory','lab_id','id');
    }
    //关联设备资源表
    public function DeviceInfo(){
        return $this->hasOne('Modules\Msc\Entities\Devices','id','device_id');
    }

    public function GetLadDevice($lab_id){
        return $this->where('lab_id','=',$lab_id)->with(['DeviceInfo'=>function($DeviceInfo){
            $DeviceInfo->with('devicesCateInfo');
        }])->paginate(config('msc.page_size',10));
    }



//    public function getLadDevice($LadId){
//        if($LadId){
//            $result = $this->leftJoin('')->
//            where('lab_id','=',$LadId)->select();
//
//        }
//
//    }
}