<?php
/**
 * Created by PhpStorm.
 * User: fengyell <Luohaihua@misrobot.com>
 * Date: 2015/11/12
 * Time: 13:33
 */

namespace Modules\Msc\Entities;

use Modules\Msc\Entities\CommonModel;


class ResourcesLocation extends  CommonModel
{
    protected $connection	=	'msc_mis';
    protected $table 		= 	'resources_location';
    public $timestamps	=	true;
    protected $primaryKey	=	'id';
    public $incrementing	=	true;
    protected $guarded 		= 	[];
    protected $hidden 		= 	[];
    protected $fillable 	=	['code','name','pid','level','description'];
    public $search 	=	['code','name','description'];
}