<?php
/**
 * Created by PhpStorm.
 * User: tangjun
 * Date: 2015/12/3 0003
 * Time: 15:04
 */

namespace Modules\Msc\Entities;

use Illuminate\Database\Eloquent\Model;

class ResourcesOpenLabCalendar extends Model
{
    protected $connection	=	'msc_mis';
    protected $table 		= 	'resources_openlab_calendar';
    protected $fillable 	=	['id', 'resoutces_lab_id', 'week', 'begintime'];

    public function resourcesClassroom(){

        return $this->hasOne('Modules\Msc\Entities\ResourcesClassroom','id','resources_lab_id');

    }

    public function ResourcesOpenLabApply(){

        return $this->hasMany('Modules\Msc\Entities\ResourcesOpenLabApply','resources_lab_calendar_id','id');

    }
    //
    public function getLaboratoryClassroomList($data){
        $thisBuilder = $this;
        if(!empty($data['week'])){
            $thisBuilder->where('week','like', '%'.$data['week'].'%');
        }
        $result = $thisBuilder->with(['resourcesClassroom' => function($query)
        {
            $query->where('opened','=',1);

        },'ResourcesOpenLabApply'])->paginate(7);


        return $result;
    }



    public function get_lab(){
        return $this->hasOne('Modules\Msc\Entities\ResourcesClassroom','id','resources_lab_id');
    }


    public function get_plan(){
        return $this->hasMany('Modules\Msc\Entities\ResourcesOpenLabPlan','resources_openlab_calendar_id','id');
    }
    
    public function order_detail($pid=null){
        $pid =  empty($pid)?null:$pid;
        $where = array();
        if(!empty($pid)){
            $where['id'] = $pid;
        }
        $resources_lab_clender_builder = $this->where($where);
        $resources_lab_clender_builder = $resources_lab_clender_builder
            ->with(['get_lab'=>function($lab){
                $lab->where('status','<>',0);
            },'get_plan'=>function($plan){
                $plan->where('status','>=',0);
            }])->first();
        return $resources_lab_clender_builder;
    }

}