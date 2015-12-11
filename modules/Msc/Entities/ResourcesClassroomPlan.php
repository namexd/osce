<?php
/**
 * Created by PhpStorm.
 * 教室使用计划
 * User: fengyell <Luohaihua@misrobot.com>
 * Date: 2015/11/27
 * Time: 19:58
 */

namespace Modules\Msc\Entities;

use Illuminate\Support\Facades\DB;
use Modules\Msc\Entities\CommonModel;
use Modules\Msc\Entities\ResourcesClassroomCourses;
use Modules\Msc\Entities\ResourcesClassroomPlanTeacher;

class ResourcesClassroomPlan extends CommonModel
{
    protected $connection	=	'msc_mis';
    protected $table 		= 	'resources_openlab_plan';
    public    $timestamps	=	true;
    protected $primaryKey	=	'id';
    public    $incrementing	=	true;
    protected $guarded 		= 	[];
    protected $hidden 		= 	[];

    protected $fillable 	=	['resources_lab_course_id', 'course_id', 'currentdate', 'begintime', 'endtime', 'group_id','status'];
    public $search          =   [];
    public $builder         =   '';
    public $arr             =   '';
    /**
     * 计划课程
     */
    public function course(){
        return $this->belongsTo('Modules\Msc\Entities\Courses','course_id');
    }
    public function classroomCourses(){
        return $this->hasOne('Modules\Msc\Entities\ResourcesClassroomCourses','id','resources_lab_course_id');
    }

    public function group(){
        return $this->belongsTo('Modules\Msc\Entities\Groups','group_id','id');
    }
    //public function
    //获取相关课程信息 (唐俊)
    public function getClassroomPlanList($orderBy='id',$pageNum=20){
        return $this->builder->orderBy($orderBy)->paginate($pageNum);
    }
    //教师关系
    public function teachersRelation(){
        return $this->hasMany('Modules\Msc\Entities\ResourcesClassroomPlanTeacher','resources_lab_plan_id','id');
    }
    //组关系
    public function groupRelation(){
        return $this->hasMany('Modules\Msc\Entities\ResourcesClassroomPlanGroup','resources_lab_plan_id','id');
    }

    //开放实验室预约 计划
    public function getLaboratoryList($data = null){
        $dateTime = empty($data['dateTime'])?null:$data['dateTime'];
        $resources_lab_plan_builder = $this;
        $where = array();
        if(!empty($dateTime)){
            $where['currentdate'] = $dateTime;
        }
        if(!empty($resources_lab_id)){
            $where['resources_lab_id'] = $resources_lab_id;
        }
        //dd($where);
        $resources_lab_plan_builder = $resources_lab_plan_builder->where($where);
/*        if(!empty($resources_lab_id)){
            $ClassroomCoursesList = ResourcesClassroomCourses::where('resources_lab_id','=',$resources_lab_id)->get();
            $ClassroomCoursesIdArr = [];
            foreach($ClassroomCoursesList as $val){
                $ClassroomCoursesIdArr[] = $val['id'];
            }
            $resources_lab_plan_builder = $resources_lab_plan_builder->whereIn('resources_lab_plan.resources_lab_course_id',$ClassroomCoursesIdArr);
        }*/
       // $resources_lab_plan_builder = $resources_lab_plan_builder
/*        if(!empty($resources_lab_id)){
            $ClassroomCoursesList = ResourcesClassroomCourses::where('resources_lab_id','=',$resources_lab_id)->get();
            $ClassroomCoursesIdArr = [];
            foreach($ClassroomCoursesList as $val){
                $ClassroomCoursesIdArr[] = $val['id'];
            }
            $resources_lab_plan_builder = $resources_lab_plan_builder->whereIn('resources_lab_plan.resources_lab_course_id',$ClassroomCoursesIdArr);
        }*/
        
        $resources_lab_plan_builder = $resources_lab_plan_builder
            ->where('resources_lab.opened','=','1')
            ->join('resources_lab_courses', 'resources_lab_plan.resources_lab_course_id', '=', 'resources_lab_courses.id')
            ->join('resources_lab', 'resources_lab_courses.resources_lab_id', '=', 'resources_lab.id')
            ->select('resources_lab_plan.id','resources_lab_plan.currentdate','resources_lab_plan.begintime', 'resources_lab_plan.endtime','resources_lab.status','resources_lab.name','resources_lab_plan.resorces_lab_person_total','resources_lab_plan.apply_person_total');
       return $resources_lab_plan_builder->orderBy('resources_lab_plan.id')->paginate(7);
    }


    //新建一个预约课程计划
    public function createApplyPlan($data){
        try{
            //获取一个教室和课程的关联
            $labCoursesRelation   =   $this->getLabCoursesRelation($data['course_id'],$data['resources_lab_id']);
            //新建一个计划
            $planData   =   [
                'resources_classroom_course_id' =>  $labCoursesRelation->id,
                'course_id'                     =>  $data['course_id'],
                'currentdate'                   =>  $data['currentdate'],
                'begintime'                     =>  $data['begintime'],
                'endtime'                       =>  $data['endtime'],
                'type'                          =>  $data['type'],
                'status'                        =>  0,
            ];
            $plan   =   $this   ->  create($planData);
            if(!$plan)
            {
                throw new \Exception('新建课程计划失败');
            }
            //新建一个组信息
            $this->createGroupsRelation($data['groups'],$plan->id);
        }
        catch(\Exception $ex)
        {
            throw $ex;
        }
        return $plan;
    }
    public function createGroupsRelation($groups,$id){
        try
        {
            foreach($groups as $group)
            {
                $groupData   =   [
                    'resources_lab_plan_id' =>  $id,
                    'student_class_id'      =>  $group  -> student_class_id,
                    'student_group_id'      =>  $group  -> student_group_id,
                ];
                $result =   ResourcesClassroomPlanGroup::create($groupData);
                if(!$result)
                {
                    throw new \Exception('用户组信息添加失败');
                }
            }
        }
        catch(\Exception $ex)
        {
            throw $ex;
        }
    }
    //获取一个教室和课程的关联
    public function getLabCoursesRelation($courseId,$labId){
        return ResourcesClassroomCourses::firstOrCreate(
            [
                'resources_lab_id'    =>  $labId,
                'course_id'           =>  $courseId
            ]
        );
    }
    /*
     * 获取冲突 课程安排
     */
    public function getConflictList($resources_lab_id,$original_begin_datetime,$original_end_datetime){
        $day=date('Y-m-d',strtotime($original_begin_datetime));
        $start=date('H:i:s',strtotime($original_begin_datetime));
        $end=date('Y-m-d H:i:s',strtotime($original_end_datetime));
        
        return  $this  -> leftJoin(
            'resources_lab_courses',
            function( $join ) {
                $join   ->  on('resources_lab_courses.id','=',$this->table.'.resources_lab_course_id');
            }
        )   ->  where(
            $this ->  table.'.status','=',0
        )   ->  where(
            'resources_lab_courses.resources_lab_id','=',$resources_lab_id
        )
            ->  where(
            $this ->  table.'.type','=',3
       )   ->  whereRaw(
           'unix_timestamp('.$this->table.'.currentdate)= ? and(
               (unix_timestamp('.$this->table.'.begintime)< ? and unix_timestamp('.$this->table.'.endtime) >= ?) or
               (unix_timestamp('.$this->table.'.begintime)< ? and unix_timestamp('.$this->table.'.endtime) >= ?) or
               (unix_timestamp('.$this->table.'.begintime)> ? and unix_timestamp('.$this->table.'.endtime) <= ?)
           )',
           [
               strtotime($day),
               //begintime<$original_begin_datetime<endtime
               strtotime($start),
               strtotime($start),
               //begintime<$original_end_datetime<endtime
               strtotime($end),
               strtotime($end),
               //$original_begin_datetime<begintime and $original_end_datetime>endtime
               strtotime($start),
               strtotime($end),
           ]
       )
        // ) ->  whereRaw(
                // 'unix_timestamp('.$this->table.'.endtime) <= ?',
                // [
                    // strtotime($end),
                // ]
        // ) 
		->select(
            [
                $this->table.'.id as id',
                $this->table.'.status as status',
                $this->table.'.resources_lab_course_id as resources_lab_course_id',
                $this->table.'.course_id as course_id',
            ]
        )   ->get();
    }

    public function cancelOldPlan($id,$start,$end){
        $list       =   $this->getConflictList($id,$start,$end);

        $teacherList=   [];

        foreach($list as $item)
        {
            $item   ->  status  =   -1;
            $result  =   $item  ->  save();

            if(!$result)
            {
                throw new \Exception('取消计划失败');
            }
            $teachers   =   $item   ->  teachersRelation;
            foreach($teachers as $teacher)
            {
                $teacherList[]  =   $teacher;
            }
        }
        return $teacherList;
    }
	
	public function getConflicts ($classroomCourseId, $currentdate, $begintime, $endtime)
	{
		return $this->where('resources_lab_course_id', $classroomCourseId)
		            ->whereIn('status', [0, 1])
					->whereRaw(
						'unix_timestamp(currentdate)=? ',
        				[strtotime($currentdate)]
					)->where(function($query)use($begintime, $endtime){
						$query->whereRaw(
						'unix_timestamp(begintime)>? and unix_timestamp(begintime)<? ',
        				[strtotime($begintime), strtotime($endtime)]
						);
					})->where(function($query)use($begintime, $endtime){
						$query->whereRaw(
						'uunix_timestamp(endtime)>? and unix_timestamp(endtime)<? ',
        				[strtotime($begintime), strtotime($endtime)]
						);
					})->get();				
	}

}