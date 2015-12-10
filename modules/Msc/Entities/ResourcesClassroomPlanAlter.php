<?php
/**
 * Created by PhpStorm.
 * User: fengyell <Luohaihua@misrobot.com>
 * Date: 2015/11/30
 * Time: 14:39
 */

namespace Modules\Msc\Entities;
use Modules\Msc\Entities\CommonModel;

class ResourcesClassroomPlanAlter extends CommonModel
{
    protected $connection	=	'msc_mis';
    protected $table 		= 	'resources_lab_plan_alter';
    public $timestamps	=	true;
    protected $primaryKey	=	'id';
    public $incrementing	=	true;
    protected $guarded 		= 	[];
    protected $hidden 		= 	[];

    protected $fillable 	=	['description', 'new_plan_id', 'original_plan_id'];
    public $search          =   [];
}