<?php
/**
 * Created by PhpStorm.
 * 教室使用计划
 * User: fengyell <Luohaihua@misrobot.com>
 * Date: 2015/11/27
 * Time: 19:58
 */

namespace Modules\Msc\Entities;

use App\Entities\User;
use App\Repositories\Common;
use Illuminate\Support\Facades\DB;
use Modules\Msc\Entities\CommonModel;
use Modules\Msc\Entities\ResourcesClassroomCourses;
use Modules\Msc\Entities\ResourcesClassroomPlanTeacher;

class ResourcesOpenLabApply extends CommonModel
{
    protected $connection	=	'msc_mis';
    protected $table 		= 	'resources_openlab_apply';

    protected $fillable 	=	['id', 'apply_type', 'apply_uid', 'resources_lab_id','resources_lab_calendar_id','detail','status','reject','opeation_uid','course_id','apply_date'];

    public    $timestamps	=	true;
    protected $primaryKey	=	'id';
    public    $incrementing	=	true;
    protected $guarded 		= 	[];
    protected $hidden 		= 	[];

    public function applyUser(){
        return $this->hasOne('App\Entities\User','id','apply_uid');
    }

    //获取突发事件使用列表
    public function getOpenLabApplyList($data){
        $thisBuilder = $this;
        $thisBuilder = $thisBuilder->where('apply_type','=',2);
        if(!empty($data)){
            $thisBuilder = $thisBuilder->where('apply_date','=',$data['dateTime']);
        }
        $result = $thisBuilder->with(['OpenLabCalendar'=>function($q){
            $q->with('resourcesClassroom');
        }])->where('status','=',0)->paginate(config('msc.page_size'));
        return  $result;
    }

    //与教室的课程日历表的关联
    public function OpenLabCalendar(){
        return  $this->hasOne('Modules\Msc\Entities\ResourcesOpenLabCalendar','id','resources_lab_calendar_id');
    }
    /**
     * 计划课程
     */
    public function course(){
        return $this    ->  belongsTo('Modules\Msc\Entities\Courses','course_id');
    }
    public function classroomCourses(){
        return $this    ->  hasOne('Modules\Msc\Entities\ResourcesClassroomCourses','id','resources_lab_id');
    }
    public function lab(){
        return $this    ->  hasOne('Modules\Msc\Entities\ResourcesClassroom','id','resources_lab_id');
    }
    public function getUrgentApplyList($date='',$keyword='',$order=[]){
        $builder    =   $this   ->  leftJoin(
            'resources_lab',
            function($join){
                $join   ->  on(
                   $this->table. '.resources_lab_id',
                    '=',
                    'resources_lab.id'
                );
            }
        )       ->  leftJoin(
            'teacher',
            function($join){
                $join   ->  on(
                    $this->table. '.apply_uid',
                    '=',
                    'teacher.id'
                );
            }
        )   ->  leftJoin(
            'courses',
            function($join){
                $join   ->  on(
                    $this->table. '.course_id',
                    '=',
                    'courses.id'
                );
            }
        )   ->  select(
            [
                'resources_lab.name as lab_name',
                'resources_lab.code as lab_code',
                'resources_lab.status as lab_status',
                'courses.name as courses_name',
                'teacher.name as teacher_name',
                $this   ->  table.'.apply_date as apply_date',
                $this   ->  table.'.detail as detail',
                $this   ->  table.'.resources_lab_calendar_id as resources_lab_calendar_id',
                $this   ->  table.'.apply_uid as apply_uid',
                $this   ->  table.'.resources_lab_id as resources_lab_id',
                $this   ->  table.'.course_id as course_id',
                $this   ->  table.'.status as status',
                $this   ->  table.'.id as id',
            ]
        );
        if(!empty($date))
        {
            $builder    =   $builder    -> where($this   ->  table.'.apply_date','=',$date);
        }
        else
        {
            $builder    =   $builder    -> whereRaw(
                'unix_timestamp('.$this   ->  table.'.apply_date) >= ?',
                [
                    strtotime(date('Y-m-d'))
                ]
            );
        }
        if(!empty($keyword))
        {
            $builder    =   $builder    ->  where('resources_lab.name','like','%'.$keyword.'%');
        }
        if(!empty($order[0]))
        {
            $builder    =   $builder    ->orderBy('resources_lab.name',$order[1]);
        }
        $builder    ->  where($this   ->  table.'.status','=',0)
                    ->  where($this   ->  table.'.apply_type','=',2);
        return $builder    ->  paginate(config('msc.page_size'));
    }

    // 获得开放实验室申请信息
    public function getApplyInfo ($applyId)
    {
        return $this->leftJoin('resources_openlab_calendar', function ($join){
                $join->on('resources_openlab_calendar.id','=','resources_openlab_apply.resources_lab_calendar_id');
            })->where('resources_openlab_apply.id', $applyId)
            ->select([
                'resources_openlab_apply.resources_lab_id as resources_lab_id',
                'resources_openlab_apply.course_id as course_id',
                'resources_openlab_apply.apply_uid as apply_uid',
                'resources_openlab_apply.resources_lab_calendar_id as resources_lab_calendar_id',
                'resources_openlab_apply.apply_date as apply_date',
                'resources_openlab_calendar.begintime as begintime',
                'resources_openlab_calendar.endtime as endtime',
            ])->firstOrFail();
    }
    public function refund($id,$reject){
        $apply  =   $this   ->  find($id);
        $apply  ->  status  =2;
        $apply  ->  reject  =$reject;
        $applyer    =   $apply  ->  applyUser;
        $this   ->  sendMsg($applyer,$reject);
        return $apply->save();
    }
    private function sendMsg(User $applyer,$reject){
        if($applyer ->  openid)
        {
            Common::sendMsg($applyer ->  openid,$reject);
        }
    }
}