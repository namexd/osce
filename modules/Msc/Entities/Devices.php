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
     * 关联用户信息
     * @return \App\Entities\User
     */
    public function devicesInfo()
    {
        return $this->hasOne('Modules\Msc\Entities\DevicesCate','devices_cate_id','id');
    }
    //获取资源列表
    public function getDevicesList($keyword='', $status='', $devices_cate_id=''){


        $builder = $this;

        if ($keyword)
        {
            $builder = $builder->where($this->table.'.name','like','%'.$keyword.'%');
        }
        $builder = $builder->leftJoin(
            'device_cate',
            function($join){
                $join   ->  on(
                    $this->table. '.created_user_id',
                    '=',
                    'device_cate.id'
                );
            }
        )->select($this->table.'.*','device_cate.name as dname');
        return $builder->orderBy( $this->table.'.id')->paginate(config('msc.page_size',10));
    }

}