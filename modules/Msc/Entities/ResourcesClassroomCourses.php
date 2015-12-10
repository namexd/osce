<?php
/**
 * 资源-教室
 * author Luohaihua
 * date 2015-11-24
 */
namespace Modules\Msc\Entities;

use Modules\Msc\Entities\CommonModel;

class ResourcesClassroomCourses extends  CommonModel {
    protected $connection	=	'msc_mis';
    protected $table 		= 	'resources_lab_courses';
    public $timestamps	=	false;
    protected $primaryKey	=	'id';
    public $incrementing	=	true;
    protected $guarded 		= 	[];
    protected $hidden 		= 	[];

    protected $fillable 	=	['resources_lab_id', 'course_id'];
    public $search          =   [];
    /**
     * 课程
     */
    public function courses(){
        return $this->hasOne('Modules\Msc\Entities\Courses','id','course_id');
    }
    /**
     * 教室
     */
    public function classroom(){
        return $this->hasOne('Modules\Msc\Entities\ResourcesClassroom','id','resources_lab_id');
    }

    public function scopeEntity ($query, $labId, $courseId)
    {
        return $query->where('resources_lab_id', $labId)->where('course_id', $courseId);
    }
}