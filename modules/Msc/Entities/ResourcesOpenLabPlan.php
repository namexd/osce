<?php
/**
 * Created by PhpStorm.
 * User: wangjiang
 * Date: 2015/12/3 0003
 * Time: 15:04
 */

namespace Modules\Msc\Entities;

use App\Repositories\Common;
use Illuminate\Database\Eloquent\Model;
use Modules\Msc\Entities\ResourcesOpenLabPlanTeacher;
use DB;

class ResourcesOpenLabPlan extends Model
{
    protected $connection	=	'msc_mis';
    protected $table 		= 	'resources_openlab_plan';
    protected $fillable 	=	['id', 'resources_openlab_id', 'resources_openlab_calendar_id', 'course_id','currentdate','begintime','endtime','type','status','apply_person_total','resorces_lab_person_total'];

    public function calendar(){
        return $this->belongsTo('\Modules\Msc\Entities\ResourcesOpenLabCalendar','resources_lab_calendar_id','id');
    }
    public function lab(){
        return $this->belongsTo('\Modules\Msc\Entities\ResourcesClassroom','resources_openlab_id','id');
    }
    public function course(){
        return $this->belongsTo('\Modules\Msc\Entities\Courses','course_id','id');
    }

    public function teachers(){
        return $this->hasMany('\Modules\Msc\Entities\ResourcesOpenLabPlanTeacher','resources_openlab_plan_id','id');
    }
    public function getConflicts($labId, $date, $beginTime, $endTime){
        $Conflicts  =   $this   ->  with(
            [
                'calendar'  =>  function($query) use ($beginTime,$endTime){
                    $query  -> whereRaw(
                        '
                        (unix_timestamp(begintime)< ? and unix_timestamp(endtime) >= ?) or
                        (unix_timestamp(begintime)< ? and unix_timestamp(endtime) >= ?) or
                        (unix_timestamp(begintime)> ? and unix_timestamp(endtime) <= ?)
                       ',
                        [
                            strtotime($beginTime),
                            strtotime($beginTime),
                            strtotime($endTime),
                            strtotime($endTime),
                            strtotime($beginTime),
                            strtotime($endTime),
                        ]
                    );
                }
            ]
        )
            ->  where('currentdate','=',$date)
            ->  where('resources_openlab_id','=',$labId)->get();
        return $Conflicts;
    }

    //检查紧急预约和开放实验室已有安排冲突  author :wangjiang
    public function checkConflicts ($labId, $date, $beginTime, $endTime)
    {
        $conflicts = $this->leftJoin('resources_lab', function($join) {
            $join->on('resources_lab.id', '=', 'resources_openlab_plan.resources_openlab_id');
        })->leftJoin('resources_openlab_plan_teacher', function ($join){
            $join->on('resources_openlab_plan_teacher.resources_openlab_plan_id','=','resources_openlab_plan.id');
        })->leftJoin('courses', function ($join){
            $join->on('courses.id','=','resources_openlab_plan.course_id');
        })->where(function ($query) use($labId, $date) {
            $query->whereIn('resources_openlab_plan.status', [0,1])
                ->where('resources_openlab_plan.resources_openlab_id', '=', $labId)
                ->whereRaw(
                    'unix_timestamp(resources_openlab_plan.currentdate)=? ',
                    [strtotime($date)]
                );
        })->where(function ($query) use($beginTime, $endTime) {
            $query->whereRaw(
                'unix_timestamp(resources_openlab_plan.begintime)>? and unix_timestamp(resources_openlab_plan.begintime)<? ',
                [strtotime($beginTime), strtotime($endTime)]
            );
        })->orWhere(function ($query) use($beginTime, $endTime) {
            $query->whereRaw(
                'unix_timestamp(resources_openlab_plan.endtime)>? and unix_timestamp(resources_openlab_plan.endtime)<? ',
                [strtotime($beginTime), strtotime($endTime)]
            );
        })->select([
            'resources_openlab_plan.id as id',
            'resources_openlab_plan_teacher.teacher_id as teacher_id',
            'courses.name as courseName',
            'resources_lab.name as labName',
            'resources_openlab_plan.currentdate as currentdate',
            'resources_openlab_plan.begintime as begintime.',
            'resources_openlab_plan.endtime as endtime.',
            'courses.id as courseId',
            'resources_lab.id as labId',
        ])->get();

        if ($conflicts)
        {
            foreach ($conflicts as $key => $item)
            {
                $user = User::findOrFail($item->teacher_id);
                $conflicts[$key]['teacherName'] = $user->name;
            }
        }

        return $conflicts;
    }

    // 紧急预约写入计划表     // author: wangjiang
    public function addTeacherPlan ($data, $teacherId)
    {
        DB::beginTransaction();

        $result = $this->create($data);
        if (!$result)
        {
            DB::rollback();
            throw new \Exception('写入计划表失败');
        }

        $data = [
            'resources_openlab_plan_id' => $result->id,
            'teacher_id'                => $teacherId,
        ];
        $result = ResourcesOpenLabPlanTeacher::create($data);
        if (!$result)
        {
            DB::rollback();
            throw new \Exception('写入计划教师关联表失败');
        }

        DB::commit();
    }

    // 延迟和紧急预约冲突的原计划
    public function chgPlans ($data)
    {
        DB::beginTransaction();

        foreach ($data as $item)
        {
            $dateArray   = explode(' ', $item['time']);
            $currentdate = date('Y-m-d', strtotime($dateArray['0']));

            $timeArray = explode('-', $dateArray['1']);
            $begintime = date('H:i:s', strtotime($timeArray['0']));
            $endtime   = date('H:i:s', strtotime($timeArray['1']));

            $result = $this->where('id', $item['id'])->update(['currentdate'=>$currentdate, 'begintime'=>$begintime, 'endtime'=>$endtime]);
            if (!$result)
            {
                DB::rollback();
                return false;
            }
        }

        DB::commit();
        return true;
    }
    
    public function cancelOldPlan($id,$notice){
        $connection =   DB::connection('msc_mis');
        $connection ->beginTransaction();
        $apply  =   ResourcesOpenLabApply::find($id);
        $list   =   $this ->getConflicts(
            $apply->resources_lab_id,
            $apply->apply_date,
            $apply->OpenLabCalendar->begintime,
            $apply->OpenLabCalendar->endtime
        );
        try{
            foreach($list as $oldPlan)
            {
                $oldPlan    ->  status  =   -1;
                $result =   $oldPlan    ->save();
                if(!$result)
                {
                    throw new \Exception('原计划状态变更失败');
                }
                else
                {
                    $teahcers       =  $oldPlan    ->  teachers    ->first();
                    $openid         =   '';
                    if($teahcers    ->  user        ->  id)
                    {
                        $openid     =   $teahcers   ->  user    ->openid;
                    }
                    if($openid)
                    {
                        Common::sendMsg($openid,$notice);
                    }
                    else
                    {
                        \Log::notice('id:'.$oldPlan ->  id.'旧计划,找不到相应老师发送报告\r\n');
                    }
                }
            }
            $newPlan    =   $this   ->  createPlanByApply($id);
            if(!$newPlan)
            {
                throw new \Exception('新建计划失败');
            }
            $connection ->  commit();
            return $newPlan;
        }
        catch(\Exception $ex)
        {
            $connection ->  rollback();
            throw $ex;
        }
    }
    public function createPlanByApply($id){
        $apply  =   ResourcesOpenLabApply::find($id);
        $apply  ->  status      =   1;
        $result =   $apply      ->  save();
        if(!$result)
        {
            throw new \Exception('申请状态变更失败');
        }

        $data   =   [
            'resources_openlab_id'          =>  $apply  ->  resources_lab_id,
            'resources_openlab_calendar_id' =>  $apply  ->  resources_lab_calendar_id,
            'course_id'                     =>  $apply  ->  course_id,
            'currentdate'                   =>  $apply  ->  apply_date,
            'begintime'                     =>  $apply  ->  OpenLabCalendar ->  begintime,
            'endtime'                       =>  $apply  ->  OpenLabCalendar ->  endtime,
            'type'                          =>  3,
            'status'                        =>  0
        ];
        return $this->create($data);
    }
}