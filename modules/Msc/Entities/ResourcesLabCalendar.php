<?php
/**
 * Created by PhpStorm.
 * User: wangjiang
 * Date: 2015/12/3 0003
 * Time: 15:04
 */

namespace Modules\Msc\Entities;

use Illuminate\Database\Eloquent\Model;

class ResourcesLabCalendar extends Model
{
    protected $connection	=	'msc_mis';

    protected $table 		= 	'resources_openlab_calendar';
    protected $fillable 	=	['id', 'resoutces_lab_id', 'week', 'begintime','endtime'];


    public function resourcesClassroom(){

        return $this->hasOne('Modules\Msc\Entities\ResourcesClassroom','id','resources_lab_id');

    }

    public function resourcesClassroomApply(){

        return $this->hasMany('Modules\Msc\Entities\ResourcesClassroomApply','resources_lab_id','resources_lab_id');

    }


    //»ñÈ¡ÊµÑéÊÒ×ÊÔ´ÁÐ±í
    public function getLaboratoryClassroomList($data){
        $thisBuilder = $this;
        if(!empty($data['month']) && !empty($data['days'])){
            $thisBuilder = $this->where('resources_openlab_calendar.month', 'like', '%'.$data['month'].'%')->where('resources_lab_calendar.days','like','%'.$data['days'].'%');
        }

        $result = $thisBuilder->with(['resourcesClassroom' => function($query)
        {
            $query->where('opened','=',1);

        },'resourcesClassroomApply' => function($q)
        {
            $q->where('apply_user_type','=',0);

        }])->paginate(7);


        return $result;
    }

    

    
}