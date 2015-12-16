<?php
/**
 * Created by PhpStorm.
 * User: wangjiang
 * Date: 2015/11/26 0026
 * Time: 11:34
 */

namespace Modules\Msc\Entities;

use Illuminate\Database\Eloquent\Model;

class Courses extends Model
{
    protected $connection	=	'msc_mis';
    protected $table 		= 	'courses';
    public $incrementing	=	true;
    public $timestamps	=	true;
    protected $fillable 	=	['name', 'code', 'length', 'detail'];

    /*
     * 课程已有的计划
     */
    public function plans(){
        return $this->hasMany('Modules\Msc\Entities\ResourcesClassroomPlan','course_id','id');
    }

    // 课程相关的教室
    public function classrooms()
    {
        return $this->belongsToMany('Modules\Msc\Entities\ResourcesClassroom', 'resources_lab_courses', 'course_id', 'resources_lab_id');
    }

    public function courses() {
        return $this->hasOne('ResourcesClassroomCourses','course_id','id');
    }
    
}