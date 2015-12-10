<?php
/**
 * Created by PhpStorm.
 * User: wangjiang
 * Date: 2015/11/26 0026
 * Time: 11:15
 */

namespace Modules\Msc\Entities;

use Illuminate\Database\Eloquent\Model;

class Training extends Model
{
    protected $connection	=	'msc_mis';
    protected $table 		= 	'training';
    protected $fillable 	=	['name', 'description', 'begindate', 'enddate', 'total'];

    // 包含培训课程
    public function trainingCourses (){
        return $this->hasMany('Modules\Msc\Entities\TrainingCourse', 'training_id');
    }
}