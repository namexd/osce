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
    protected $table 		= 	'lad_device';
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
        return $this->hasOne('Modules\Msc\Entities\Devices','device_id','id');
    }

    public function getLadDevice($LadId){
        if($LadId){
            $result = $this->leftJoin('')->
            where('lab_id','=',$LadId)->select();

        }

    }
}