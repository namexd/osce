<?php
/**
 * Created by PhpStorm.
 * User: wangjiang
 * Date: 2015/12/7 0007
 * Time: 16:17
 */

namespace Modules\Msc\Entities;

class ResourcesDeviceCate extends  CommonModel {
    protected $connection	=	'msc_mis';
    protected $table 		= 	'resources_device_cate';
    public    $timestamps	=	true;
    protected $primaryKey	=	'id';
    public    $incrementing	=	true;
    protected $fillable 	=	['id', 'pid', 'name', 'manager_id', 'manager_name', 'manager_mobile', 'location', 'detail','created.at'];
}