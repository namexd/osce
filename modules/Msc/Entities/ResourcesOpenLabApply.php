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
    protected $statusValues =   [
        0   =>'待审核',
        1   =>'已通过',
        2   =>'不通过'
    ];

    public function getStatusValues(){
        return $this->statusValues;
    }
    public function applyUser(){
        return $this->hasOne('App\Entities\User','id','apply_uid');
    }
    public function student(){
        return $this->hasOne('Modules\Msc\Entities\Student','id','apply_uid');
    }
    public function teacher(){
        return $this->hasOne('Modules\Msc\Entities\Teacher','id','apply_uid');
    }
    public function calendar(){
        return $this->belongsTo('Modules\Msc\Entities\ResourcesOpenLabCalendar','resources_lab_calendar_id','id');
    }
    public function plan(){
        return $this->belongsTo('Modules\Msc\Entities\ResourcesOpenLabPlan','id','resources_openlab_apply_id');
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
    //获取申请的组列表
    public function labApplyGroups(){
        return $this->hasMany('Modules\Msc\Entities\ResourcesOpenLabAppGroup','resources_openlab_apply_id','id');
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
    /**
     * 获取普通预约待审审核列表
     * @return pagenation
     *
     * @version 1.0
     * @author Luohaihua <Luohaihua@misrobot.com>
     * @date 2015-12-16 13:24
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     *
     */
    public function getWaitExamineList($classroomName,$date, $order){
        return  $this   -> with([
            'classroomCourses'  =>  function($qurey) use ($classroomName){
                if(!is_null($classroomName))
                {
                    $qurey  ->with([
                        'classroom'=> function($qurey) use ($classroomName){
                            if(!is_null($classroomName))
                            {
                                $qurey  ->  where('name','like',$classroomName);
                            }
                        }
                    ]);
                }
            }
        ])  ->  where('status','=',0)
            ->  whereRaw(
            'unix_timestamp(apply_date) > ?',
            [
                strtotime($date),
            ]
        ) -> paginate(config('msc.page_size'));
    }

    //处理开放实验室审核
    public function dealApply($id, $status, $reject){
        if($status==1)
        {
            $result =   $this   ->agreeApply($id);
        }
        else
        {
            $result =   $this   ->refundApply($id,$reject);
        }
        return  $result;
    }
    public function agreeApply($id){
        $apply      =   $this   ->  find($id);
        $connection =   DB::connection($this->connection);
        $connection ->  beginTransaction();
        try
        {
            $newPlan    =   '';
            if($this        ->  checkApplyerIsTeacher($apply))
            {
                $newPlan    =   $this       ->  agreeTeacherApply($apply);
            }
            else
            {
                $newPlan    =   $this       ->  agreeStudentApply($apply);
            }
            $result =   false;
            if($newPlan)
            {
                $apply  ->  status  =   1;
                $result =   $apply  ->  save();
            }
            else
            {
                throw   new \Exception('计划创建失败');
            }
            if(!$result)
            {
                throw   new \Exception('申请状态变更失败');
            }
            $connection     ->  commit();
            return  $newPlan;
        }
        catch(\Exception $ex)
        {
            $connection     ->  rollback();
            throw $ex;
        }
    }
    public function refundApply(){

    }
    public function agreeTeacherApply($apply){
        $calendar   =   $apply  ->  calendar;
        $planData   =   [
            'resources_openlab_id'              =>  $apply  ->  resources_lab_id,
            'resources_openlab_calendar_id'     =>  $apply  ->  resources_lab_calendar_id,
            'course_id'                         =>  $apply  ->  course_id,
            'currentdate'                       =>  date('Y-m-d',strtotime($apply  ->  apply_date)),
            'begintime'                         =>  $calendar   ->  begintime,
            'endtime'                           =>  $calendar   ->  endtime,
            'type'                              =>  $apply  ->  course_id,
            'status'                            =>  0,
            'resources_openlab_apply_id'        =>  $apply  ->  id,
        ];
        try
        {
            //如果当前预约 的时间和 教室 没有冲突的突发事件（紧急预约） 以及 教师预约  则取消所有 学生计划 并且创建 新的 计划
            if(
                !$this   ->  checkSameUrgent($planData['resources_openlab_calendar_id'],$planData['currentdate'])&&
                !$this   ->  checkTeacherApply($planData['resources_openlab_calendar_id'],$planData['currentdate'])
            )
            {
                //取消所有 学生计划
                if($this   ->  cancelStudentPlan($planData['resources_openlab_calendar_id'],$planData['currentdate']))
                {
                    //创建教师预约的新计划
                    $newPlan    =   ResourcesOpenLabPlan::create($planData);
                    if(!$newPlan)
                    {
                        throw new \Exception('创建计划失败');
                    }
                    $teacherPlan    =   [
                        'resources_openlab_plan_id' =>  $apply  ->  resources_lab_id,
                        'teacher_id'                =>  $apply  ->  apply_uid,
                    ];
                    //创建 计划 的教师信息
                    $teacherPlan    =   ResourcesOpenLabPlanTeacher::create($teacherPlan);
                    if(!$teacherPlan)
                    {
                        throw new \Exception('计划负责老师信息保存失败');
                    }
                    //获取申请的分组列表
                    $labApplyGroups =   $apply          ->  labApplyGroups;
                    foreach($labApplyGroups as $item)
                    {
                        $groupData  =   [
                            'resources_openlab_plan_id' =>  $newPlan->  id,
                            'student_class_id'          =>  $item   ->  student_class_id,
                            'student_group_id'          =>  $item   ->  student_group_id,
                        ];
                        //创建 计划 的分组信息
                        $group      =   ResourcesOpenLabAppGroup::create($groupData);
                        if(!$group)
                        {
                            throw new \Exception('计划课程分组信息保存失败');
                        }
                    }
                    return $newPlan;
                }
                else
                {
                    throw new \Exception('预约计划有冲突');
                }
            }
            else
            {
                throw new \Exception('计划与已有的教师计划或突发事件有冲突');
            }
        }
        catch(\Exception $ex)
        {
            throw   $ex;
        }
    }

    /**
     * 通过学生开放实验室申请
     * @access public
     * * string        参数英文名        参数中文名(必须的)
     *
     * @return object
     *
     * @version 1.0
     * @author Luohaihua <Luohaihua@misrobot.com>
     * @date 2015-12-17 16:25
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     *
     */
    public function agreeStudentApply($apply){
        $calendar   =   $apply  ->  calendar;
        $planData   =   [
            'resources_openlab_id'              =>  $apply  ->  resources_lab_id,
            'resources_openlab_calendar_id'     =>  $apply  ->  resources_lab_calendar_id,
            'course_id'                         =>  $apply  ->  course_id,
            'currentdate'                       =>  date('Y-m-d',strtotime($apply  ->  apply_date)),
            'begintime'                         =>  $calendar   ->  begintime,
            'endtime'                           =>  $calendar   ->  endtime,
            'type'                              =>  $apply  ->  course_id,
            'resources_openlab_apply_id'        =>  $apply  ->  id,
            'status'                            =>  0,
        ];
        try
        {
            //如果当前预约 的时间和 教室 没有冲突的突发事件（紧急预约） 并且  没有教师预约
            if(
                !$this   ->  checkSameUrgent($planData['resources_openlab_calendar_id'],$planData['currentdate'])&&
                !$this   ->  checkTeacherApply($planData['resources_openlab_calendar_id'],$planData['currentdate'])
            )
            {
                //计划人数加1
                $totalPerson    =   $this   ->  addSamePlanPersonTotal($planData['resources_openlab_calendar_id'],$planData['currentdate']);
                $planData['apply_person_total']     =   $totalPerson;
                $newPlan    =   ResourcesOpenLabPlan::create($planData);
                if(!$newPlan)
                {
                    throw new \Exception('创建计划失败');
                }
                else
                {
                    return $newPlan;
                }
            }
            else
            {
                throw new \Exception('申请和已有计划冲突');
            }
        }
        catch(\Exception $ex)
        {
            throw   $ex;
        }
    }
    /**
     * 判断申请的申请人是否为教师
     * * string        参数英文名        参数中文名(必须的)
     *
     * @return booler
     *
     * @version 1.0
     * @author Luohaihua <Luohaihua@misrobot.com>
     * @date 2015-12-17
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     *
     */
    public function checkApplyerIsTeacher($apply){
        $isTeacher  =   false;
        if(!is_null($apply   ->  student))
        {
            $isTeacher  =   false;
        }
        if(!is_null($apply   ->  teacher))
        {
            $isTeacher  =   true;
        }
        return $isTeacher;
    }

    /**
     * 增加相同 开放实验室 计划的总人数
     * @access public
     * * string        resources_openlab_calendar_id        开放实验室开放时间段(必须的)
     * * string        currentdate                          查询的日期(必须的)
     *
     * @return booler
     *
     * @version 1.0
     * @author Luohaihua <Luohaihua@misrobot.com>
     * @date 2015-12-17 16:16
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     *
     */
    public function addSamePlanPersonTotal($resources_openlab_calendar_id,$currentdate){
        $sameList   =   $this   ->  getSamePlanList($resources_openlab_calendar_id,$currentdate);
        try{
            $plan   =   0;
            foreach($sameList as $plan)
            {
                $plan   ->  resorces_lab_person_total   =   intval($plan   ->  resorces_lab_person_total)   +   1;
                $plan   ->  save();
            }
            if($plan==0)
            {
                return 0;
            }
            else
            {
                return $plan    ->  resorces_lab_person_total;
            }
        }
        catch(\Exception $ex)
        {
            throw   $ex;
        }
    }
    /**
     * 获取指定日期，指定时间段内 相同的计划列表
     * @access public
     * * string        resources_openlab_calendar_id        开放实验室开放时间段(必须的)
     * * string        currentdate                          查询的日期(必须的)
     *
     * @return collect
     *
     * @version 1.0
     * @author Luohaihua <Luohaihua@misrobot.com>
     * @date 2015-12-17 16:15
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     *
     */
    public function getSamePlanList($resources_openlab_calendar_id,$currentdate){
        return ResourcesOpenLabPlan::where('resources_openlab_calendar_id','=',$resources_openlab_calendar_id)
            ->  where('currentdate','=',$currentdate)
            ->  get();
    }

    /**
     * 获取实验室时段，日期相同情况下是否有紧急预约
     * @access public
     *
     * * string        resources_openlab_calendar_id        开放实验室开放时间段(必须的)
     * * string        currentdate                          查询的日期(必须的)
     *
     * @return booler
     *
     * @version 1.0
     * @author Luohaihua <Luohaihua@misrobot.com>
     * @date 2015-12-17 17:21
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     *
     */
    public function checkSameUrgent($resources_openlab_calendar_id,$currentdate){
        $ResourcesOpenLabPlan   =  new ResourcesOpenLabPlan();
        if($ResourcesOpenLabPlan   ->  checkSameUrgent($resources_openlab_calendar_id,$currentdate))
        {
            return true;
        }
        return false;
    }
    /**
     * 获取实验室时段，日期相同情况下是否有教师已经预约
     * @access public
     *
     * * string        resources_openlab_calendar_id        开放实验室开放时间段(必须的)
     * * string        currentdate                          查询的日期(必须的)
     *
     * @return booler
     *
     * @version 1.0
     * @author Luohaihua <Luohaihua@misrobot.com>
     * @date 2015-12-17 17:21
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     *
     */
    public function checkTeacherApply($resources_openlab_calendar_id,$currentdate){
        $sameList   =   $this   ->  getSamePlanList($resources_openlab_calendar_id,$currentdate);
        if(count($sameList))
        {
            $ids    =   array_pluck($sameList,'id');
            $ResourcesOpenLabPlanTeacher    =   new ResourcesOpenLabPlanTeacher();
            if(empty($ids))
            {
                $total   =   $ResourcesOpenLabPlanTeacher    ->  whereIn('resources_openlab_plan_id',$ids)  ->count();
                if($total>0)
                {
                    return true;
                }
            }
            return  false;
        }
        else
        {
            return  false;
        }
    }

    public function cancelStudentPlan($resources_openlab_calendar_id,$currentdate)
    {
        $sameList = $this->getSamePlanList($resources_openlab_calendar_id, $currentdate);
        try {
            foreach ($sameList as $plan) {
                $plan->status = -1;
                $plan->save();
            }
            return true;
        } catch (\Exception $ex) {
            throw   $ex;
        }
    }


    /**
     * 已审核申请列表
     * @param string $courseName
     * @param string $date
     * @param array $order
     * @return mixed
     */
    public function getExaminedList ($courseName, $date, $order) {
        $builder = $this->leftJoin (
            'resources_lab',
            function ($join) {
                $join->on ($this->table.'.resources_lab_id', '=', 'resources_lab.id');
            }
        )->leftJoin (
            'student',
            function ($join) {
                $join->on ($this->table.'.apply_uid', '=', 'student.id');
            }
        )->leftJoin (
            'teacher',
            function ($join) {
                $join->on ($this->table.'.apply_uid', '=', 'teacher.id');
            }
        )   ->leftJoin (
            'resources_openlab_calendar',
            function ($join) {
                $join->on ($this->table.'.resources_lab_calendar_id','=','resources_openlab_calendar.id');
            }
        )
            ->where ($this->table.'.status', '<>', '0');
        if ($courseName) {
            $builder = $builder->where ('resources_lab.name', 'like', '%'.$courseName.'%');
        }
        if ($date) {
            $builder->whereRaw ('unix_timestamp(resources_openlab_apply.apply_date)>= ? ', [strtotime ($date)]);
        }
        $builder->select (
            [
                'resources_lab.name as name',
                $this->table.'.apply_date as apply_date',
                'resources_openlab_calendar.begintime as begintime',
                'resources_openlab_calendar.endtime as endtime',
                'resources_lab.code as code',
                'student.name as student_name',
                'teacher.name as teacher_name',
                $this->table.'.detail as detail',
                'resources_lab.status as status',
                $this->table.'.id as id',
                $this->table.'.apply_uid as apply_uid',

            ]
        );

        if($order[0]=='created_at')
        {
            $order[0]   =   $this->table.'.created_at';
        }

        return $builder->orderBy ($order[0][0], $order[1])->orderBy($order[0][1],$order[1])->paginate (config ('msc.page_size'));
    }
}