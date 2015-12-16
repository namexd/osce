<?php
/**
 * Created by PhpStorm.
 * User: fengyell <Luohaihua@misrobot.com>
 * Date: 2015/12/1
 * Time: 11:50
 */

namespace Modules\Msc\Entities;

use Modules\Msc\Entities\CommonModel;

class ResourcesClassroomPlanGroup extends CommonModel
{
    protected $connection	=	'msc_mis';
    protected $table 		= 	'resources_lab_plan_group';
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

    //通过计划id查找应到人数
    public function getTotal($id){
        $build=$this->leftJoin("student_group",function($join){
            $join->on($this->table.'.student_group_id','=','student_group.group_id');
        })->where($this->table.".resources_lab_plan_id",'=',$id);
        return $build->count();
    }
}