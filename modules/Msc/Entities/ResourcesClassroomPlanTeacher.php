<?php
/**
 * Created by PhpStorm.
 * User: fengyell <Luohaihua@misrobot.com>
 * Date: 2015/12/1
 * Time: 15:15
 */

namespace Modules\Msc\Entities;
use Modules\Msc\Entities\CommonModel;

class ResourcesClassroomPlanTeacher extends  CommonModel
{
    protected $connection	=	'msc_mis';
    protected $table 		= 	'resources_lab_plan_teacher';
    public $timestamps	=	false;
    protected $primaryKey	=	'id';
    public $incrementing	=	true;
    protected $guarded 		= 	[];
    protected $hidden 		= 	[];

    protected $fillable 	=	[];
    public $search          =   [];
    public function teacher(){
        return $this->belongsTo('\Modules\Msc\Entities\Teacher','teacher_id','id');
    }
    public function userInfo(){
        return $this->belongsTo('App\Entities\User','teacher_id','id');
    }

}