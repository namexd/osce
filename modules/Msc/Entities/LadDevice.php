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

    //����ʵ���ұ�
    public function LadInfo()
    {
        return $this->hasOne('Modules\Msc\Entities\Laboratory','lab_id','id');
    }
    //�����豸��Դ��
    public function DeviceInfo(){
        return $this->hasOne('Modules\Msc\Entities\Devices','id','device_id');
    }

    /**
     * @param $lab_id
     * @return mixed
     * @author tangjun <tangjun@163.com>
     * @date    2016年1月13日18:30:58
     * @copyright 2013-2015 MIS 163.com Inc. All Rights Reserved
     */
    public function GetLadDevice($lab_id){
        return $this->where('lab_id','=',$lab_id)->with(['DeviceInfo'=>function($DeviceInfo){
            $DeviceInfo->with('devicesCateInfo');
        }])->paginate(config('msc.page_size',10));
    }

    /**
     * @param $lab_id
     * @return mixed
     * @author tangjun <tangjun@163.com>
     * @date    2016年1月13日18:30:58
     * @copyright 2013-2015 MIS 163.com Inc. All Rights Reserved
     */
    public function GetLadDeviceAll($lab_id){
        return $this->where('lab_id','=',$lab_id)->with(['DeviceInfo'=>function($DeviceInfo){
            $DeviceInfo->with('devicesCateInfo');
        }])->get();
    }
    /**
     * @param $lab_id
     * @return mixed
     * @author tangjun <tangjun@163.com>
     * @date    2016年1月7日18:14:05
     * @copyright 2013-2015 MIS 163.com Inc. All Rights Reserved
     */
    public function getLadDeviceId($lab_id){
        $IdArr = [];
        $DeviceArr = $this->where('lab_id','=',$lab_id)->select(['device_id'])->get();
        if(!empty($DeviceArr) && is_array($DeviceArr->toArray())){
            foreach($DeviceArr->toArray() as $v){
                $IdArr [] = $v['device_id'];
            }
        }
        return  $IdArr;
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