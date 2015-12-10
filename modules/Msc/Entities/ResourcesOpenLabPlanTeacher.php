<?php
/**
 * Created by PhpStorm.
 * User: wangjiang
 * Date: 2015/12/8 0008
 * Time: 10:20
 */

namespace Modules\Msc\Entities;

use Illuminate\Database\Eloquent\Model;

class ResourcesOpenLabPlanTeacher extends Model
{
    protected $connection = 'msc_mis';
    protected $table      = 'resources_openlab_plan_teacher';
    protected $fillable   = ['id', 'resources_openlab_plan_id', 'teacher_id'];
    public function teacher(){
        return $this->hasOne('Modules\Msc\Entities\Teacher','id','teacher_id');
    }
    public function user(){
        return $this->hasOne('App\Entities\User','id','teacher_id');
    }
}