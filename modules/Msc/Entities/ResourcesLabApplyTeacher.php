<?php
/**
 * Created by PhpStorm.
 * User: 梧桐雨间的枫叶
 * Date: 2015/11/29
 * Time: 18:29
 */

namespace Modules\Msc\Entities;
use Modules\Msc\Entities\CommonModel;

class ResourcesLabApplyTeacher extends  CommonModel {
    protected $connection	=	'msc_mis';
    protected $table 		= 	'resources_lab_plan_teacher';
    public $timestamps	=	true;
    protected $primaryKey	=	'id';
    public $incrementing	=	true;
    protected $guarded 		= 	[];
    protected $hidden 		= 	[];
    //包含查询条件的数据库对象（唐俊）
    public $builder         =   '';

}