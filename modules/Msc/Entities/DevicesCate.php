<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2015/12/30 0030
 * Time: 10:10
 */

namespace Modules\Msc\Entities;


use Illuminate\Database\Eloquent\Model;

class DevicesCate  extends  Model
{

    public $timestamps	=	true;
    public $incrementing	=	true;

    protected $connection	=	'msc_mis';
    protected $table 		= 	'device_cate';
    protected $primaryKey	=	'id';
    protected $guarded 		= 	[];
    protected $hidden 		= 	[];
    protected $fillable 	=	['name','code','description','level','pid','created_user_id','id'];

}