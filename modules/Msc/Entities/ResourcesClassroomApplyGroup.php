<?php
/**
 * Created by PhpStorm.
 * User: fengyell <Luohaihua@misrobot.com>
 * Date: 2015/12/1
 * Time: 11:50
 */

namespace Modules\Msc\Entities;

use Modules\Msc\Entities\CommonModel;

class ResourcesClassroomApplyGroup extends CommonModel
{
    protected $connection	=	'msc_mis';
    protected $table 		= 	'resources_lab_apply_group';
    public $timestamps	=	false;
    protected $primaryKey	=	'id';
    public $incrementing	=	true;
    protected $guarded 		= 	[];
    protected $hidden 		= 	[];

    protected $fillable 	=	[];
    public $search          =   [];

    public function groups(){
        return $this->belongsTo('\Modules\Msc\Entities\Groups','student_group_id','id');
    }
}