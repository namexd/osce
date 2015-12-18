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

    protected $table 		= 	'resources_lab_calendar';
    protected $fillable 	=	['id', 'resources_lab_id', 'week', 'begintime','endtime'];


    public function resourcesClassroom(){

        return $this->hasOne('Modules\Msc\Entities\ResourcesClassroom','id','resources_lab_id');

    }

    public function resourcesClassroomApply(){

        return $this->hasMany('Modules\Msc\Entities\ResourcesClassroomApply','resources_lab_id','resources_lab_id');

    }

    //根据日历表，获取教室安排信息
    public function getCourseArrangementList($data){
        $thisBuilder = $this;

        if(!empty($data['week'])){
            $thisBuilder->where('week','like', '%'.$data['week'].'%');
        }
        $result = $thisBuilder->with(['resourcesClassroom' => function($query)
        {
            $query->where('opened','=',1);

        }])->paginate(7);


        return $result;

    }

    

    
}