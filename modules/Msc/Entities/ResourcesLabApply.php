<?php
/**
 * Created by PhpStorm.
 * User: wangjiang
 * Date: 2015/12/6
 * Time: 15:10
 */

namespace Modules\Msc\Entities;

class ResourcesLabApply extends  CommonModel {
    protected $connection	=	'msc_mis';
    protected $table 		= 	'resources_lab_apply';
    public    $timestamps	=	true;
    protected $primaryKey	=	'id';
    public    $incrementing	=	true;
}