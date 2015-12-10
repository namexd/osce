<?php
/**
 * Created by PhpStorm.
 * User: wangjiang
 * Date: 2015/11/26 0026
 * Time: 11:20
 */

namespace Modules\Msc\Entities;

use Illuminate\Database\Eloquent\Model;

class TrainingCourse extends Model
{
    protected $connection	=	'msc_mis';
    protected $table 		= 	'training_course';
    protected $fillable 	=	['course_id', 'training_id', 'resources_lab_id', 'begin_dt', 'end_dt', 'validation_pass'];

    // 所属课程
    public function course (){
        return $this->belongsTo('Modules\Msc\Entities\Courses', 'course_id');
    }

    // 所属培训
    public function training (){
        return $this->belongsTo('Modules\Msc\Entities\Training', 'training_id');
    }

    // 搜索资源教室
    public function resourcesClassroom (){
        return $this->belongsTo('Modules\Msc\Entities\ResourcesClassroom', 'resources_lab_id');
    }
}