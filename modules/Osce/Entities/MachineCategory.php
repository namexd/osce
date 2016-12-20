<?php
/**
 * 设备类别模型
 * Created by PhpStorm.
 * User: fengyell <Zouyuchao@sulida.com>
 * Date: 2015/12/30
 * Time: 11:25
 */

namespace Modules\Osce\Entities;


class MachineCategory extends CommonModel
{
    protected $connection	=	'osce_mis';
    protected $table 		= 	'machine_category';
    public $incrementing	=	true;
    public $timestamps	    =	true;
    protected $fillable 	=	['name'];
}