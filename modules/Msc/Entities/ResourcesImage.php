<?php

namespace Modules\Msc\Entities;

use Modules\Msc\Entities\CommonModel;

class ResourcesImage extends CommonModel {

    protected $connection	=	'msc_mis';
    protected $table 		= 	'resources_image';
    public  $timestamps	=	true;
    protected $primaryKey	=	'id';
    public $incrementing	=	true;
    protected $guarded 		= 	[];
    protected $hidden 		= 	[];
    protected $fillable 	=	['resources_id','url','order','descrption',];
}