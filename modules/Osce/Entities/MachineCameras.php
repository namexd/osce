<?php
/**
 * 设备摄像机模型
 * Created by PhpStorm.
 * User: fengyell <Luohaihua@misrobot.com>
 * Date: 2015/12/30
 * Time: 14:11
 */

namespace Modules\Osce\Entities;


class MachineCameras extends CommonModel
{
    protected $connection	=	'osce_mis';
    protected $table 		= 	'machine_cameras';
    public $incrementing	=	true;
    public $timestamps	    =	true;
    //protected $fillable 	=	[''];
}