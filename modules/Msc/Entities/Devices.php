<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2015/12/30 0030
 * Time: 10:09
 */

namespace Modules\Msc\Entities;


use Illuminate\Database\Eloquent\Model;

class Devices  extends Model
{
    public $timestamps	=	true;
    public $incrementing	=	true;
    protected $connection	=	'msc_mis';
    protected $table 		= 	'devices';
    protected $primaryKey	=	'id';
    protected $guarded 		= 	[];
    protected $hidden 		= 	[];
    protected $fillable 	=	['name','code','mux_use_time','warning','detail','status','devices_cate_id','created_user_id','id'];

    /**
     * 关联设备类型表
     * Modules\Msc\Entities\DevicesCate
     */
    public function devicesCateInfo()
    {
        return $this->hasOne('Modules\Msc\Entities\DevicesCate','id','devices_cate_id');
    }
    //获取资源列表
    public function getDevicesList($DeviceIdArr,$keyword='', $status='', $devices_cate_id=''){
        $builder = $this;
        if(!empty($DeviceIdArr)){
            $builder = $builder->whereNotIn('devices.id',$DeviceIdArr);
        }

        if ($keyword)
        {
            $builder = $builder->where($this->table.'.name','like','%'.$keyword.'%');
        }
        if(in_array($status,[1,2])){
               $builder = $builder->where($this->table.'.status',$status-1);
           }
        if($devices_cate_id){
               $builder = $builder->where($this->table.'.devices_cate_id',$devices_cate_id);
           }

        $builder = $builder->leftJoin(
            'device_cate',
            function($join){
                $join   ->  on(
                    $this->table. '.devices_cate_id',
                    '=',
                    'device_cate.id'
                );
            }
        )->select($this->table.'.*','device_cate.name as catename');
        return $builder->orderBy( $this->table.'.status','desc')->orderBy( $this->table.'.id','desc')->paginate(config('msc.page_size',10));
    }


    //改变专业状态
    public  function changeStatus($professionId){
        $data=$this->where('id',$professionId)->select('status')->first();

        foreach($data as $tmp){
            $status=$tmp;
        };

        return $this->where('id',$professionId)->update(['status'=>3-$status]);

    }
}