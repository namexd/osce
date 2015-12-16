<?php
/**
 * Created by PhpStorm.
 * User: fengyell <Luohaihua@misrobot.com>
 * Date: 2015/11/26
 * Time: 18:07
 */

namespace Modules\Msc\Http\Controllers\Admin;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Modules\Msc\Entities\Courses;
use Modules\Msc\Entities\Resources;
use Modules\Msc\Entities\ResourcesClassroom;
use Modules\Msc\Entities\ResourcesClassroomApply;
use Modules\Msc\Entities\ResourcesClassroomCourses;
use Modules\Msc\Entities\ResourcesClassroomPlan;
use Modules\Msc\Entities\ResourcesClassroomPlanAlter;
use Modules\Msc\Entities\ResourcesClassroomPlanTeacher;
use Modules\Msc\Entities\ResourcesLabVcr;
use Modules\Msc\Entities\Student;
use Modules\Msc\Entities\Training;
use Modules\Msc\Entities\TrainingCourse;
use Modules\Msc\Http\Controllers\MscController;
use App\Repositories\Common;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\DB;
use Modules\Msc\Repositories\Common as MscCommon;


class CoursesController extends MscController
{

    /*public function getTest(){

        return view('msc::admin.coursemanage.course_observe_detail');
    }*/
    /**
     * 导入课程
     * @api POST /msc/admin/courses/import-courses
     * @access public
     *
     * @param Request $request post请求<br><br>
     * <b>post请求字段：</b>
     * * string        courses        课程文件的excl(必须的)
     *
     * @return object
     *
     * @version 1.0
     * @author Luohaihua <Luohaihua@misrobot.com>
     * @date 2015-11-27 10:24
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     *
     */
    public function postImportCourses(Request $request){
        try{
            $data=Common::getExclData($request,'courses');
            $coursesList= array_shift($data);
            //将中文表头 按照配置 翻译成 英文字段名
            $data=Common::arrayChTOEn($coursesList,'msc.importForCnToEn.courses');

            //已经存在的数据
            $dataHaven=[];
            //添加失败的数据
            $dataFalse=[];
            foreach($data as $coursesData)
            {
                if($coursesData['code']&&$coursesData['name'])
                {
                    //判断课程编码是否存在
                    if(Courses::where('code','=',$coursesData['code'])->count()==0)
                    {
                        $courses=Courses::create($coursesData);
                        if($courses==false)
                        {
                            $dataFalse[]=$coursesData;
                        }
                    }
                    else
                    {
                        $dataHaven[]=$coursesData;
                    }
                }
            }
            return response()->json(
                $this->success_data(['result'=>true,'dataFalse'=>$dataFalse,'dataHaven'=>$dataHaven])
            );
        }
        catch(\Exception $ex)
        {
            return response()->json($this->fail($ex));
        }
    }
    /**
     * 导入课程计划
     * @api GET /msc/admin/courses/import-courses-plan
     * @access public
     *
     * @param Request $request post请求<br><br>
     * <b>post请求字段：</b>
     * * string        courses-plan        课程文件的excl(必须的)
     *
     * @return object
     *
     * @version 1.0
     * @author Luohaihua <Luohaihua@misrobot.com>
     * @date 2015-11-27 10:24
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     *
     */
    public function postImportCoursesPlan(Request $request){
        $connection = DB::connection('msc_mis');
        $connection->beginTransaction();
        try{
            $data=Common::getExclData($request,'plan');
            $coursesList= array_shift($data);
            //将中文表头 按照配置 翻译成 英文字段名
            $data=Common::arrayChTOEn($coursesList,'msc.importForCnToEn.coursesPlan');
            //失败原因 包
            $falseData=[];
            $dataConflict=[];
            foreach($data as $item)
            {
                //失败原因
                $falseInfo=[];


                $classroom=ResourcesClassroom::where('code','=',$item['calss_room_code'])->first();
                if(empty($classroom))
                {
                    $falseInfo=[
                        'data'=>$item,
                        'desc'=>'没有找到相应教室',
                    ];
                    $falseData[]=$falseInfo;
                    continue;
                }
                $course=Courses::where('code','=',$item['course_code'])->first();
                if(empty($course))
                {
                    $falseInfo=[
                        'data'=>$item,
                        'desc'=>'没有找到相应课程',
                    ];
                    $falseData[]=$falseInfo;
                    continue;
                }
                $coursesClassroomRelation=[
                    'resources_lab_id'=>$classroom->id,
                    'course_id'=>$course->id,
                ];
                $reletion=ResourcesClassroomCourses::firstOrCreate($coursesClassroomRelation);
                $plan=[
                    'resources_lab_course_id'=>$reletion->id,
                    'course_id'=>$reletion->course_id,
                    'currentdate'=>$item['currentdate']->format('Y-m-d'),
                    'begintime'=>$item['begintime']->format('H:i'),
                    'endtime'=>$item['endtime']->format('H:i'),
                    'status'=>0
                ];

                if(ResourcesClassroomPlan::where('currentdate','<',$plan['endtime'])->where('endtime','>',$plan['currentdate'])->count()==0)
                {
                    $plan=ResourcesClassroomPlan::firstOrCreate($plan);
                    if(!$plan)
                    {
                        $falseInfo[]=[
                            'data'=>$item,
                            'desc'=>'添加失败，稍后再试',
                        ];
                        $connection->rollback();
                        continue;
                    }
                }
                else
                {
                    $dataConflict[]=[
                        'data'=>$item,
                        'desc'=>'课程时间冲突',
                    ];
                }
                $connection->commit();
            }
            return response()->json(
                $this->success_data(['result'=>true,'falseInfo'=>$falseInfo,'dataConflict'=>$dataConflict])
            );
        }
        catch(\Exception $ex)
        {
            $connection->rollback();
            return response()->json($this->fail($ex));
        }
    }

    /**
     * 获取基础课程列表
     * @api GET /msc/admin/courses/normal-courses-plan
     * @access public
     *
     * @param Request $request get请求<br><br>
     * <b>get请求字段：</b>
     * * string        begindate        查询开始时间 e.g:2015-08-05
     * * string        enddate          查询结束时间 e.g:2015-08-06
     * * string        field            查询字段(关键字搜索时必须)(枚举：''、'classroom'、'course') e.g:classroom
     * * string        keyword          被搜索字段内容全称 (关键字搜索时必须)e.g:测试教室001
     *
     * @return View
     *
     * @version 1.0
     * @author Luohaihua <Luohaihua@misrobot.com>
     * @date 2015-11-28 14:28
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     *
     */
    public function getNormalCoursesPlan(Request $request){
        $pagination=$this->NormalCoursesPlanlist($request);
        return view('msc::admin.coursemanage.coursebase',['pagination'=>$pagination]);
    }

    /**
     *
     * @api GET /msc/admin/courses/normal-courses-plan-data
     * @access public
     *
     * @param Request $request get请求<br><br>
     * <b>get请求字段：</b>
     * * string        begindate        查询开始时间 e.g:2015-08-05
     * * string        enddate          查询结束时间 e.g:2015-08-06
     * * string        field            查询字段(关键字搜索时必须)(枚举：''、'classroom'、'course') e.g:classroom
     * * string        keyword          被搜索字段内容全称 (关键字搜索时必须)e.g:测试教室001
     * * string        order            排序字段 (排序时必须) 枚举 classroom,courses,status
     * * string        orderby          排序方式 (排序时必须) 枚举：desc,asc
     *
     * @return json {id：ID,courses:课程名称，currentdate：课程日期，begintime：开始时间，endtime：结束时间，group：小组名称，classroom：教室名称，teacher：教师名，mobile：教师联系电话，status：状态}
     *
     * @version 1.0
     * @author Luohaihua <Luohaihua@misrobot.com>
     * @date 2015-11-28 14:28
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     *
     */
    public function getNormalCoursesPlanData(Request $request){

        $pagination=$this->NormalCoursesPlanlist($request);
        $data=[];
        $a=DB::connection('msc_mis');
        foreach($pagination as $item)
        {
            $teachers=$item->teachersRelation;
            $teacherData=[];
            $groupsData=[];
            $mobileData=[];
//            foreach($teachers as $teacher){
//                if(is_null($teacher->teacher))
//                {
//                    throw new \Exception('没有找到数据，请检查'.$teacher->id.'数据');
//                }
//                $teacherUser=$teacher->teacher;
//                $teacherUserInfo=$teacherUser->userInfo;
//
//                $teacherData[]=$teacherUserInfo->name;
//                $mobileData[]=$teacherUserInfo->mobile;
//            }
            foreach($teachers as $teacher){
                $teacherUser=$teacher->teacher;
                $teacherUserInfo=$teacherUser->userInfo;
                $teacherData[]=$teacherUserInfo->name;
                //自获取第一个
                break;
            }
            $groups=$item->groupRelation;
            foreach($groups as $group){
                if(empty($group->group))
                {
                    continue;
                }
                $groupsData[]=$group->group->name;
                //自获取第一个
                break;
            }

            $itemData=[
                'id'            =>  $item->id,
                'courses'       =>  is_null($item->course_name)? '-':$item->course_name,
                'currentdate'   =>  date('Y-m-d',strtotime($item->currentdate)),
                'begintime'     =>  date('H:i',strtotime($item->begintime)),
                'endtime'       =>  date('H:i',strtotime($item->endtime)),
                'group'         =>  implode(',',$groupsData),
                'classroom'     =>  is_null($item->classroom_name)? '-':$item->classroom_name,//$item->classroomCourses->classroom->name,
                'teacher'       =>  implode(',',$teacherData),
                'mobile'        =>  implode(',',$mobileData),
                'status'        =>  $item->status,
            ];
            $data[]=$itemData;
        }
        if(count($data)==0)
        {
            return response()->json(
                $this->success_rows(1,'获取成功',0,20,1,$data)
            );
        }
        return response()->json(
            $this->success_rows(1,'获取成功',$pagination->lastPage(),20,$pagination->currentPage(),$data)
        );
    }
    private function NormalCoursesPlanlist(Request $request){

        $this->validate($request,[
            'begindate'     =>  'sometimes|date_format:Y-m-d H:i:s',
            'enddate'       =>  'sometimes|date_format:Y-m-d H:i:s',
            'field'         =>  'sometimes',
            'keyword'       =>  'sometimes',
            'order'         =>  'sometimes',
            'orderby'       =>  'sometimes',
        ]);
        if(strtotime($request->get('begindate'))>strtotime($request->get('enddate')))
        {
            throw new \Exception('开始时间不能小于结束时间');
        }

        $begindate=$request->get('begindate');
        $enddate=$request->get('enddate');
        $field=e($request->get('field'));
        $keyword=e($request->get('keyword'));
        $order=e($request->get('order'));
        $order_type=e($request->get('orderby'));
        $order_type=empty($order_type)? 'desc':$order_type;
        $where=[];
        $whereIn=[];
        //如果不传日期参数 默认查询当日往后的数据
        if(empty($begindate))
        {
            $begindate=date('Y-m-d');
        }
        //如果搜索字段为教室名称
        if($field=='classroom')
        {
            if(strlen($keyword)==0)
            {
                throw new \Exception('搜索关键字不能为空');
            }
            $where[]=['resources_lab.name','=',$keyword];
        }
        //如果搜索字段为课程名称
        if($field=='course')
        {
            if(strlen($keyword)==0)
            {
                throw new \Exception('搜索关键字不能为空');
            }
            $where[]=['courses.name','=',$keyword];
        }

        $builder=ResourcesClassroomPlan::leftJoin('resources_lab_courses',function($join){
            $join->on('resources_lab_plan.resources_lab_course_id','=','resources_lab_courses.id');
        })->leftJoin('resources_lab',function($join){
            $join->on('resources_lab_courses.resources_lab_id','=','resources_lab.id');
        })->leftJoin('courses',function($join){
            $join->on('resources_lab_courses.course_id','=','courses.id');
        })->select([
            'resources_lab_plan.id as id',
            'courses.name as course_name',
            'resources_lab_plan.currentdate as currentdate',
            'resources_lab_plan.begintime as begintime',
            'resources_lab_plan.endtime as endtime',
            'resources_lab_plan.status as status',
            'resources_lab_plan.type as type',
            'resources_lab.name as classroom_name'
        ]);
        if(!empty($where))
        {
            foreach($where as $param)
            {
                $builder=$builder->where($param[0],$param[1],$param[2]);
            }
        }
        $where[]=['resources_lab_plan.type','=',1];
        if(count($whereIn)>0)
        {
            foreach($whereIn as $param)
            {
                $builder=$builder->whereIn($param[0],$param[1]);
            }
        }
       // $a=DB::connection('msc_mis');
       // $a->enableQueryLog();
        if(empty($enddate))
        {
            $builder=$builder->whereRaw('unix_timestamp(resources_lab_plan.currentdate)>= ? ',[strtotime($begindate)]);
        }
        else
        {
            $builder=$builder->whereRaw('unix_timestamp(resources_lab_plan.currentdate)<= ? and unix_timestamp(resources_lab_plan.currentdate)>= ?',[strtotime($enddate),strtotime($begindate)]);
        }

        if(empty($order))
        {
            $pagination=$builder->paginate(config('msc.page_size'));
        }
        else
        {
            if( in_array($order,['status','currentdate','begintime','endtime']))
            {
                $order='resources_lab_plan.'.$order;
            }
            if( in_array($order,['classroom']))
            {
                $order='resources_lab.'.'name';
            }
            if( in_array($order,['courses']))
            {
                $order='courses.'.'name';
            }
            $pagination=$builder->orderBy($order,$order_type)->paginate(config('msc.page_size'));
        }
        //$c=$a->getQueryLog();
       //dd($c);
        //dd($pagination);
        return $pagination;
    }
    /**
     * 获取紧急课程列表
     * @api GET /msc/admin/courses/provisional-courses-plan
     * @access public
     *
     * @param Request $request get请求<br><br>
     * <b>get请求字段：</b>
     * * string        begindate        查询开始时间 e.g:2015-08-05
     * * string        enddate          查询结束时间 e.g:2015-08-06
     * * string        field            查询字段(关键字搜索时必须)(枚举：''、'classroom'、'course') e.g:classroom
     * * string        keyword          被搜索字段内容全称 (关键字搜索时必须)e.g:测试教室001
     * @return view
     *
     * @version 1.0
     * @author Luohaihua <Luohaihua@misrobot.com>
     * @date 2015-11-29 20:20
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     *
     */
    public function getProvisionalCoursesPlan(Request $request){
        $pagination=$this->ProvisionalCoursesPlanList($request);
        //return view('',['pagination'=>$pagination]);
    }
    /**
     * 获取紧急课程列表
     * @api GET /msc/admin/courses/provisional-courses-plan-data
     * @access public
     *
     * @param Request $request get请求<br><br>
     * <b>get请求字段：</b>
     * * string        begindate        查询开始时间 e.g:2015-08-05
     * * string        enddate          查询结束时间 e.g:2015-08-06
     * * string        field            查询字段(关键字搜索时必须)(枚举：''、'classroom'、'course') e.g:classroom
     * * string        keyword          被搜索字段内容全称 (关键字搜索时必须)e.g:测试教室001
     * * string        order            排序字段 (排序时必须) 枚举 classroom,courses,status
     * * string        orderby          排序方式 (排序时必须) 枚举：desc,asc
     * @return {'classroom':教室名称,applyer:'教室申请人',date:'日期','start':上课时间,'end':下课时间,'id':'课程计划ID'}
     *
     * @version 1.0
     * @author Luohaihua <Luohaihua@misrobot.com>
     * @date 2015-11-29 20:20
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     *
     */
    public function getProvisionalCoursesPlanData(Request $request){
        $pagination=$this->ProvisionalCoursesPlanList($request);
        $data=[];
        foreach($pagination as $item){
            $teachers=$item->teachersRelation;
            $teacherData=[];
            $groupsData=[];
            $mobileData=[];
            foreach($teachers as $teacher){
                if(is_null($teacher->teacher))
                {
                    throw new \Exception('没有找到数据，请检查'.$item->id.'数据');
                }
                $teacherData[]  =$teacher->teacher->userInfo->name;
                $mobileData[]   =$teacher->teacher->userInfo->mobile;
            }
            $groups=$item->groupRelation;
            foreach($groups as $group){
                if(empty($group->group))
                {
                    continue;
                }
                $groupsData[]   =$group->group->name;
            }
            $itemData=[
                'id'            =>  $item->id,
                'courses'       =>  is_null($item->course_name)? '-':$item->course_name,
                'currentdate'   =>  date('Y-m-d',strtotime($item->currentdate)),
                'begintime'     =>  date('H:i',strtotime($item->begintime)),
                'endtime'       =>  date('H:i',strtotime($item->endtime)),
                'group'         =>  implode(',',$groupsData),
                'classroom'     =>  is_null($item->classroom_name)? '-':$item->classroom_name,//$item->classroomCourses->classroom->name,
                'teacher'       =>  implode(',',$teacherData),
                'mobile'        =>  implode(',',$mobileData),
                'status'        =>  $item->status,
            ];
            $data[]=$itemData;
        }
        if(count($data)==0)
        {
            return response()->json(
                $this->success_rows(1,'获取成功',0,config('msc.page_size'),1,$data)
            );
        }
        return response()->json(
            $this->success_rows(1,'获取成功',$pagination->lastPage(),config('msc.page_size'),$pagination->currentPage(),$data)
        );
    }
    private function ProvisionalCoursesPlanList(Request $request){
        $this->validate($request,[
            'begindate'     =>  'sometimes|date_format:Y-m-d H:i:s',
            'enddate'       =>  'sometimes|date_format:Y-m-d H:i:s',
            'field'         =>  'sometimes',
            'keyword'       =>  'sometimes',
        ]);
        if(strtotime($request->get('begindate'))>strtotime($request->get('enddate')))
        {
            throw new \Exception('开始时间不能小于结束时间');
        }
        $begindate=$request->get('bagindate');
        $enddate=$request->get('enddate');
        $field=e($request->get('field'));
        $keyword=e($request->get('keyword'));

        $order_type=e($request->get('orderby'));
        $order_type=empty($order_type)? 'desc':$order_type;
        //如果搜索字段为教室名称
        $where=[];
        $whereIn=[];
        //如果不传日期参数 默认查询当日往后的数据
        if(empty($begindate))
        {
            $begindate=date('Y-m-d');
        }
        //如果搜索字段为教室名称
        if($field=='classroom')
        {
            if(strlen($keyword)==0)
            {
                throw new \Exception('搜索关键字不能为空');
            }
            $where[]=['resources_lab.name','=',$keyword];
        }
        //如果搜索字段为课程名称
        if($field=='course')
        {
            if(strlen($keyword)==0)
            {
                throw new \Exception('搜索关键字不能为空');
            }
            $where[]=['courses.name','=',$keyword];
        }
        $where[]=['resources_lab_plan.type','=',3];
        $builder=ResourcesClassroomPlan::leftJoin('resources_lab_courses',function($join){
            $join->on('resources_lab_plan.resources_lab_course_id','=','resources_lab_courses.id');
        })->leftJoin('resources_lab',function($join){
            $join->on('resources_lab_courses.resources_lab_id','=','resources_lab.id');
        })->leftJoin('courses',function($join){
            $join->on('resources_lab_courses.course_id','=','courses.id');
        })->select([ 'resources_lab_plan.id as id',
            'courses.name as course_name',
            'resources_lab_plan.currentdate as currentdate',
            'resources_lab_plan.begintime as begintime',
            'resources_lab_plan.endtime as endtime',
            'resources_lab_plan.status as status',
            'resources_lab_plan.type as type',
            'resources_lab.name as classroom_name']);

        if(!empty($where))
        {
            foreach($where as $param)
            {
                $builder=$builder->where($param[0],$param[1],$param[2]);
            }
        }
        if(!empty($whereIn))
        {
            foreach($whereIn as $param)
            {
                $builder=$builder->whereIn($param[0],$param[1]);
            }
        }
        if(empty($enddate))
        {
            $builder=$builder->whereRaw('unix_timestamp(currentdate)>= ? ',[strtotime($begindate)]);
        }
        else
        {
            $builder=$builder->whereRaw('unix_timestamp(currentdate)<= ? and unix_timestamp(currentdate)>=?',[strtotime($enddate),strtotime($begindate)]);
        }
//        $a=DB::connection('msc_mis');
//        $a->enableQueryLog();
        if(empty($order))
        {
            $pagination=$builder->paginate(config('msc.page_size'));
        }
        else
        {
            if( in_array($order,['status','currentdate','begintime','endtime']))
            {
                $order='resources_lab_plan.'.$order;
            }
            if( in_array($order,['classroom']))
            {
                $order='resources_lab.'.'name';
            }
            if( in_array($order,['courses']))
            {
                $order='courses.'.'name';
            }
            $pagination=$builder->orderBy($order,$order_type)->paginate(config('msc.page_size'));
        }
//        $c=$a->getQueryLog();
//        dd($c);
        return $pagination;
    }

    /**
     * 获取岗前培训列表
     * @api GET /msc/admin/courses/training-courses-plan
     * @access public
     *
     * @param Request $request get请求<br><br>
     * <b>get请求字段：</b>
     * * string        begindate        查询开始时间 e.g:2015-08-05
     * * string        enddate          查询结束时间 e.g:2015-08-06
     * * string        field            查询字段(关键字搜索时必须)(枚举：''、'classroom'、'course'、'training') e.g:classroom
     * * string        keyword          被搜索字段内容全称 (关键字搜索时必须)e.g:测试教室001
     * @return view
     *
     * @version 1.0
     * @author Luohaihua <Luohaihua@misrobot.com>
     * @date 2015-11-29 20:20
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     *
     */
    public function getTrainingCoursesPlan(Request $request){
        $pagination=$this->TrainingCoursesPlanList($request);
        //return view('',['pagination'=>$pagination]);
    }
    /**
     * 获取岗前培训列表
     * @api GET /msc/admin/courses/training-courses-plan-list
     * @access public
     *
     * @param Request $request get请求<br><br>
     * <b>get请求字段：</b>
     * * string        begindate        查询开始时间 e.g:2015-08-05
     * * string        enddate          查询结束时间 e.g:2015-08-06
     * * string        field            查询字段(关键字搜索时必须)(枚举：''、'classroom'、'course'、'training') e.g:classroom
     * * string        keyword          被搜索字段内容全称 (关键字搜索时必须)e.g:测试教室001
     * * string        order            排序字段 (排序时必须) 枚举 classroom,courses,status
     * * string        orderby          排序方式 (排序时必须) 枚举：desc,asc
     * @return json {id：ID,courses:课程名称，currentdate：课程日期，begintime：开始时间，endtime：结束时间，group：小组名称，classroom：教室名称，teacher：教师名，mobile：教师联系电话，status：状态}
     *
     * @version 1.0
     * @author Luohaihua <Luohaihua@misrobot.com>
     * @date 2015-11-29 20:20
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     *
     */
    public function getTrainingCoursesPlanList(Request $request){
        $pagination=$this->TrainingCoursesPlanList($request);
        $data=[];
        foreach($pagination as $item){
            $teachers=$item->teachersRelation;
            $teacherData=[];
            $groupsData=[];
            $mobileData=[];
            foreach($teachers as $teacher){
                if(is_null($teacher->teacher))
                {
                    throw new \Exception('没有找到数据，请检查'.$item->id.'数据');
                }
                $teacherData[]=$teacher->teacher->userInfo->name;
                $mobileData[]=$teacher->teacher->userInfo->mobile;
            }
            $groups=$item->groupRelation;
            foreach($groups as $group){
                if(empty($group->group))
                {
                    continue;
                }
                $groupsData[]=$group->group->name;
            }
            $itemData=[
                'id'            =>  $item->id,
                'courses'       =>  is_null($item->course_name)? '-':$item->course_name,
                'currentdate'   =>  date('Y-m-d',strtotime($item->currentdate)),
                'begintime'     =>  date('H:i',strtotime($item->begintime)),
                'endtime'       =>  date('H:i',strtotime($item->endtime)),
                'group'         =>  implode(',',$groupsData),
                'classroom'     =>  is_null($item->classroom_name)? '-':$item->classroom_name,//$item->classroomCourses->classroom->name,
                'teacher'       =>  implode(',',$teacherData),
                'mobile'        =>  implode(',',$mobileData),
                'status'        =>  $item->status,
            ];
            $data[]=$itemData;
        }
        if(count($data)==0)
        {
            return response()->json(
                $this->success_rows(1,'获取成功',0,config('msc.page_size'),1,$data)
            );
        }
        return response()->json(
            $this->success_rows(1,'获取成功',$pagination->lastPage(),20,$pagination->currentPage(),$data)
        );
    }
    private function TrainingCoursesPlanList(Request $request){
        $this->validate($request,[
            'begindate'     =>  'sometimes|date_format:Y-m-d H:i:s',
            'enddate'       =>  'sometimes|date_format:Y-m-d H:i:s',
            'field'         =>  'sometimes',
            'keyword'       =>  'sometimes',
        ]);
        if(strtotime($request->get('begindate'))>strtotime($request->get('enddate')))
        {
            throw new \Exception('开始时间不能小于结束时间');
        }

        $begindate=$request->get('begindate');
        if(is_null($begindate))
        {
            $begindate=date('Y-m-d');
        }
        $enddate=$request->get('enddate');
        $field=e($request->get('field'));
        $keyword=e($request->get('keyword'));

        $order_type=e($request->get('orderby'));
        $order_type=empty($order_type)? 'desc':$order_type;
        //如果搜索字段为教室名称
        $where=[];
        //如果搜索字段为教室名称
        if($field=='classroom')
        {
            if(strlen($keyword)==0)
            {
                throw new \Exception('搜索关键字不能为空');
            }
            $where[]=['resources_lab.name','=',$keyword];
        }
        //如果搜索字段为课程名称
        if($field=='course')
        {
            if(strlen($keyword)==0)
            {
                throw new \Exception('搜索关键字不能为空');
            }
            $where[]=['courses.name','=',$keyword];
        }
        $builder=ResourcesClassroomPlan::leftJoin('resources_lab_courses',function($join){
            $join->on('resources_lab_plan.resources_lab_course_id','=','resources_lab_courses.id');
        })->leftJoin('resources_lab',function($join){
            $join->on('resources_lab_courses.resources_lab_id','=','resources_lab.id');
        })->leftJoin('courses',function($join){
            $join->on('resources_lab_courses.course_id','=','courses.id');
        })->select([ 'resources_lab_plan.id as id',
            'courses.name as course_name',
            'resources_lab_plan.currentdate as currentdate',
            'resources_lab_plan.begintime as begintime',
            'resources_lab_plan.endtime as endtime',
            'resources_lab_plan.status as status',
            'resources_lab_plan.type as type',
            'resources_lab.name as classroom_name']);
        $where[]=['resources_lab_plan.type','=',2];
        if(!empty($where))
        {
            foreach($where as $param)
            {
                $builder=$builder->where($param[0],$param[1],$param[2]);
            }
        }
        if(!empty($whereIn))
        {
            foreach($whereIn as $param)
            {
                $builder=$builder->whereIn($param[0],$param[1]);
            }
        }
        //$connection = DB::connection('msc_mis');
//        $connection->enableQueryLog();
        if(empty($enddate))
        {
            $builder=$builder->whereRaw('unix_timestamp(currentdate)>= ? ',[strtotime($begindate)]);
        }
        else
        {
            $builder=$builder->whereRaw('unix_timestamp(currentdate)<= ? and unix_timestamp(currentdate)>=?',[strtotime($enddate),strtotime($begindate)]);
        }
        if(empty($order))
        {
            $pagination=$builder->paginate(config('msc.page_size'));
        }
        else
        {
            if( in_array($order,['status','currentdate','begintime','endtime']))
            {
                $order='resources_lab_plan.'.$order;
            }
            if( in_array($order,['classroom']))
            {
                $order='resources_lab.'.'name';
            }
            if( in_array($order,['courses']))
            {
                $order='courses.'.'name';
            }
            $pagination=$builder->orderBy($order,$order_type)->paginate(config('msc.page_size'));
        }
//        $a=$connection->getQueryLog();
        return $pagination;
    }

    /**
     *
     * @api GET /msc/admin/courses/wait-examine-provisional-courses
     * @access public
     *
     *
     * @return json {}
     *
     * @version 1.0
     * @author Luohaihua <Luohaihua@misrobot.com>
     * @date 2015-11-30 10:45
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     *
     */
    public function getWaitExamineProvisionalCourses(){
        $builder=ResourcesClassroomApply::where('status','=',0);
        $builder=$builder->whereRaw('unix_timestamp(begin_datetime)> ?',[time()]);
        $pagination=$builder->orderBy('begin_datetime','asc')->paginate(config('msc.page_size'));
        $data=[];
        foreach($pagination as $item)
        {
            $dateItem=[
                'id'=>$item->id,
                'title'=>$item->detail,
                'classroom'=>is_null($item->classroom)? '-':$item->classroom->name,
                'time'=>date('Y-m-d H:i',strtotime($item->begin_datetime)).'-'.date('H:i',strtotime($item->end_datetime)),
                'applyer'=>$item->applyer->name,
                'moblie'=>$item->applyer->mobile,
                'total'=>60,//测试数据
                'apply_time'=>$item->created_dt,//测试数据
            ];
            $data[]=$dateItem;
        }
        return response()->json(
            $this->success_rows(1,'获取成功',$pagination->lastPage(),20,$pagination->currentPage(),$data)
        );
    }

    /**
     * 普通课程调课编辑
     * @api GET /msc/admin/courses/courses-edit
     * @access public
     *
     * @param Request $request post请求<br><br>
     * <b>post请求字段：</b>
     * * string        id        课程计划ID(必须的)
     *
     * @return view name:姓名
     *
     * @version 1.0
     * @author Luohaihua <Luohaihua@misrobot.com>
     * @date 2015-11-30 11:39
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     *
     */
    public function getCoursesEdit(Request $request){
        $this->validate($request,[
            'id'            =>  'required|integer',
        ]);
        $id=$request->get('id');
        $plan=ResourcesClassroomPlan::find($id);
        $classroomCourses=$plan->classroomCourses;
        if(!empty($classroomCourses))
        {
            $classroom=$classroomCourses->classroom->name;
        }
        else
        {
            $classroom='-';
        }
        $groupRelations=$plan->groupRelation;
        $group=[];
        if(empty($groupRelations))
        {
            foreach($groupRelations as $groupRelation)
            {
                $group[]=$groupRelation->groups->name;
            }
        }
        else
        {
            $group=['-'];
        }
        $teachersRelations=$plan->teachersRelation;
        $teachers=[];
        $mobiles=[];
        if(empty($teachersRelations))
        {
            foreach($teachersRelations as $teachersRelation)
            {
                $teachers[]=$teachersRelation->teacher->name;
                $mobiles[]=$teachersRelation->teacher->name;
            }

        }
        else
        {
            $teachers=['-'];
        }
        $classroomRelations=ResourcesClassroomCourses::where('course_id','=',$plan->course_id)->get();
        return view('msc::admin.coursemanage.courseedit',[
            'data'      =>$plan,
            'classroom' =>$classroom,
            'groups'    =>$group,
            'teachers'  =>$teachers,
            'mobiles'   =>$mobiles,
            'classroomRelations'=>$classroomRelations
        ]);
    }
    /**
     * 修改基础课程计划
     * @api POST /msc/admin/courses/courses-edit
     * @access public
     *
     * @param Request $request post请求<br><br>
     * <b>post请求字段：</b>
     * * int        id        参数中文名(必须的)e.g:2
     * * int        resources_lab_id        参数中文名(必须的)e.g:1
     * * int        course_id        参数中文名(必须的) e.g:5
     * * datetime        begindate        参数中文名(必须的) e.g:2015-12-01 13:00:00
     * * datetime        enddate        参数中文名(必须的)e.g:2015-12-01 16:00:00
     *
     * @return redirect 页面跳转 到 常规课程
     *
     * @version 1.0
     * @author Luohaihua <Luohaihua@misrobot.com>
     * @date 2015-11-30 16:38
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     *
     */
    public function postCoursesEdit(Request $request){
        $this->validate($request,[
            'id'            =>  'required|integer',
            'resources_lab_id'            =>  'required|integer',
            'course_id'            =>  'required|integer',
            'begindate'     =>  'required|date_format:Y-m-d H:s:i',
            'enddate'       =>  'required|date_format:Y-m-d H:s:i',
        ]);
        $formData=$request->only(['id','resources_lab_id','course_id','begindate','enddate']);
        $id=$formData['id'];
        $currentdate=date('Y-m-d',strtotime($formData['begindate']));
        $begintime=date('H:i:s',strtotime($formData['begindate']));
        $endtime=date('H:i:s',strtotime($formData['enddate']));

        try{
            $plan=$this->changeOldPlan($id,$formData['resources_lab_id'],$begintime,$endtime,$currentdate);
            if($plan)
            {
                 return redirect()->back();
            }
        }catch (\Exception $ex)
        {
            return redirect()->back()->withErrors($ex);
        }
    }

    /**
     * 课程详情
     * @api GET /msc/admin/courses/courses
     * @access public
     *
     * @param Request $request get请求<br><br>
     * <b>get请求字段：</b>
     * * string        id        课程安排ID(必须的)
     *
     * @return object
     *
     * @version 1.0
     * @author Luohaihua <Luohaihua@misrobot.com>
     * @date 2015-11-30 11:40
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     *
     */
    public function getCourses(Request $request){
        $this->validate($request,[
            'id'            =>  'required|integer',
        ]);
        $id=$request->get('id');
        $plan=ResourcesClassroomPlan::find($id);
        $classroomCourses=$plan->classroomCourses;
        if(!empty($classroomCourses))
        {
            $classroom=$classroomCourses->classroom->name;
        }
        else
        {
            $classroom='-';
        }
        $groupRelations=$plan->groupRelation;
        $group=[];
        if(empty($groupRelations))
        {
            foreach($groupRelations as $groupRelation)
            {
                $group[]=$groupRelation->groups->name;
            }
        }
        else
        {
            $classroom='-';
        }
        $teachersRelations=$plan->teachersRelation;
        $teachers=[];
        $mobiles=[];
        if(empty($teachersRelations))
        {
            foreach($teachersRelations as $teachersRelation)
            {
                $teachers[]=$teachersRelation->teacher->name;
                $mobiles[]=$teachersRelation->teacher->name;
            }

        }
        else
        {
            $classroom='-';
        }

        return view('msc::admin.coursemanage.course_detail',[
                'data'      =>$plan,
                'classroom' =>$classroom,
                'groups'    =>$group,
                'teachers'  =>$teachers,
                'mobiles'   =>$mobiles
            ]
        );
    }

    /**
     * 获取教室空闲时间
     * @api GET /msc/admin/courses/classroom-time
     * @access public
     *
     * @param Request $request post请求<br><br>
     * <b>post请求字段：</b>
     * * int        id               教室ID(必须的)
     * * int        plan_id          原计划ID(必须的)
     *
     * @return ['空闲日期1'，'空闲日期2']
     *
     * @version 1.0
     * @author Luohaihua <Luohaihua@misrobot.com>
     * @date 2015-11-30 17:58
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     *
     */
    public function getClassroomTime(Request $request){
        $this->validate($request,[
            'id'            =>  'required|integer',
            'plan_id'            =>  'required|integer'
        ]);
        $id     =$request->get('id');
        $plan_id=$request->get('plan_id');

        $oldPlan=ResourcesClassroomPlan::find($plan_id);
        $time   =strtotime($oldPlan->endtime)-strtotime($oldPlan->begintime);

        $list   =ResourcesClassroomPlan::leftJoin(
            'resources_lab_courses',
            function($join){
                $join->on('resources_lab_courses.id','=','resources_lab_plan.resources_lab_course_id');
        })  ->where('resources_lab_courses.resources_lab_id','=',$id)
            ->whereRaw(
                'unix_timestamp(currentdate)> ?  ',
                [strtotime(date('Y-m-d'))]
            )->get();
        $lastTime   =time();
        $emptyTime  =[];
        foreach($list as $item){
            $startDateTime  =strtotime($item->currentdate.' '.$item->begintime);
            $endDateTime    =strtotime($item->currentdate.' '.$item->endtime);

            if($startDateTime-$lastTime>=$time&&date('H',$lastTime+$time)<=22)
            {
                $emptyTime[]=[
                    'start'=>$lastTime,
                    'end'=>$startDateTime,
                ];
            }
            if(date('H',$lastTime+$time)<=22)
            {
                //如果结束时间为 当晚最后一节课，那么开始时间为第二天早上8点
                $lastTime=strtotime($item->currentdate)+115200;
            }
            else
            {
                $lastTime=$endDateTime;
            }

        }
        $emptyTime[]=[
            'start'=>$lastTime,
            'end'=>$lastTime+$time,
        ];
        foreach($emptyTime as $thisTime)
        {
            $timeEmpty[]=date('Y-m-d H:i',$thisTime['start']).'-'.date('H:i',$thisTime['end']);
        }
        return response()->json(
            $this->success_rows(1,'获取成功',1,count($timeEmpty),1,$timeEmpty)
        );
    }
    public function postExamineProvisionalCourses(Request $request){
        $id=$request->get('id');
        $apply=ResourcesClassroomApply::find($id);


    }
    /**
     * 检查是否有冲突
     * @api GET /msc/admin/courses/examine-provisional-courses-check
     * @access public
     *
     * @param Request $request get请求<br><br>
     * <b>get请求字段：</b>
     * * string        id        申请ID(必须的)
     *
     * @return object
     *
     * @version 1.0
     * @author Luohaihua <Luohaihua@misrobot.com>
     * @date 2015-11-30-19:02
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     *
     */
    public function getExamineProvisionalCoursesCheck(Request $request){
        $this->validate($request,[
            'id'            =>  'required|integer',
        ]);
        $id=$request->get('id');
        $apply=ResourcesClassroomApply::find($id);
        $currentdate=date('Y-m-d',strtotime($apply->begin_datetime));
        $begintime=date('H:i',strtotime($apply->begin_datetime));
        $endtime=date('H:i',strtotime($apply->end_datetime));

        $list=ResourcesClassroomPlan::leftJoin(
            'resources_lab_courses',
            function($join){
                $join->on('resources_lab_courses.id','=','resources_lab_plan.resources_lab_course_id');
            })  ->where('resources_lab_courses.resources_lab_id','=',$apply->resources_lab_id)
            ->whereRaw(
                'unix_timestamp(currentdate)= ? and unix_timestamp(begintime)<=? or unix_timestamp(endtime)>=? ',
                [strtotime($currentdate),strtotime($endtime),strtotime($begintime)]
            )->get();
        if(count($list)>0)
        {
            foreach($list as $item)
            {
                $itemData=[
                    'id'=>$item->id,
                    'course_id'=>$item->course_id,
                    'name'=>is_null($item->course)? '-':$item->course->name,
                    'currentdate'=>$item->currentdate,
                    'begintime'=>$item->begintime,
                    'endtime'=>$item->endtime,
                ];
                $data[]=$itemData;
            }
            return response()->json(
                $this->success_data(['result'=>true,'rows'=>$itemData])
            );
        }
        else
        {
            return response()->json(
                $this->success_data(['result'=>false])
            );
        }
    }
    public function getCheckEmpty(Request $request){
        $this->validate($request,[
            'id'            =>  'required|integer',
        ]);
        $id=$request->get('id');
        $apply=ResourcesClassroomApply::find($id);
        $time=strtotime($apply->endtime)-strtotime($apply->begintime);
        dd('没得教室');
        $list=ResourcesClassroomCourses::where('course_id','=',$apply->course_id)->get();
        $classroomIdsArray=array_pluck($list,'resources_lab_id');
        dd($list);
        if(!empty($classroomIdsArray))
        {
            $data=[];
            foreach($classroomIdsArray as $classroomId){
                $timeList=$this->classroomEmptyTime($classroomId,$time);
                foreach($timeList as $timeData)
                {
                    $data[$timeData[0]]=$timeData;
                }
            }
        }
        dd($data);
    }

    /**
     * 取消课程计划
     * @api GET /msc/admin/courses/cancel-plan
     * @access public
     *
     * @param Request $request get请求<br><br>
     * <b>get请求字段：</b>
     * * string        id        被取消的计划ID(必须的)
     * * string        type       被取消的 计划类型(必须的)  枚举：training，provisional
     *
     * @return object
     *
     * @version 1.0
     * @author Luohaihua <Luohaihua@misrobot.com>
     * @date 2015-12-01 10:15
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     *
     */
    public function getCancelPlan(Request $request){
        $this->validate($request,[
            'id'            =>  'required|integer',
            'type'          =>  'required',
        ]);
        $id     =   $request->get('id');
        $type   =   e($request->get('type'));
        $connection = DB::connection('msc_mis');
        $connection->beginTransaction();
        try
        {
            $apply=ResourcesClassroomPlan::find($id);
            if($type=='training')
            {
                if(!is_null($apply)&&$apply->type==2)
                {
                    $apply  ->status=-1;
                    $result =$apply ->save();
                    if($result)
                    {
                        $connection ->commit();

                    }
                    else
                    {
                        throw new \Exception('修改失败,请稍后再试');
                    }
                }
                else
                {
                    throw new \Exception('没有找到相关培训安排');
                }
            }
            elseif($type=='provisional')
            {
                if(!is_null($apply)&&$apply->type==3)
                {
                    $apply  ->status    =-1;
                    $result =$apply ->  save();
                    if($result)
                    {
                        $connection->commit();
                    }
                    else
                    {
                        throw new \Exception('修改失败,请稍后再试');
                    }
                }
                else
                {
                    throw new \Exception('没有找到相关课程安排');
                }
            }
            return redirect()->back();
        }
        catch(\Exception $ex){
            $connection->rollback();
            return redirect()->back()->withErrors($ex);
        }
    }
    private function classroomEmptyTime($roomId,$time){

        $list=ResourcesClassroomPlan::leftJoin(
            'resources_lab_courses',
            function($join){
                $join->on('resources_lab_courses.id','=','resources_lab_plan.resources_lab_course_id');
            })  ->where('resources_lab_courses.resources_lab_id','=',$roomId)
            ->whereRaw(
                'unix_timestamp(currentdate)> ?  ',
                [strtotime(date('Y-m-d'))]
            )->get();
        $lastTime=time();
        $emptyTime=[];
        foreach($list as $item){
            $startDateTime  =strtotime($item->currentdate.' '.$item->begintime);
            $endDateTime    =strtotime($item->currentdate.' '.$item->endtime);

            if($startDateTime-$lastTime>=$time&&date('H',$lastTime+$time)<=22)
            {
                $emptyTime[]=[
                    'start' =>$lastTime,
                    'end'   =>$startDateTime,
                ];
            }
            if(date('H',$lastTime+$time)<=22)
            {
                //如果结束时间为 当晚最后一节课，那么开始时间为第二天早上8点
                $lastTime=strtotime($item->currentdate)+115200;
            }
            else
            {
                $lastTime=$endDateTime;
            }

        }
        $emptyTime[]=[
            'start'     =>$lastTime,
            'end'       =>$lastTime+$time,
        ];
        $timeEmpty=[];
        foreach($emptyTime as $thisTime)
        {
            $timeEmpty[]    =[
                $thisTime['start'],
                $thisTime['end']
            ];
        }
        return $timeEmpty;
    }

    /**
     * 获取申请 最近安排时间
     * @api GET /msc/admin/courses/best-time
     * @access public
     *
     * @param Request $request get请求<br><br>
     * <b>get请求字段：</b>
     * * string        id        申请ID(必须的)
     *
     * @return json {"code":1,"message":"success","data":{"time":{"day":"日期","start":"开始时间","end":"结束时间"}}}
     *
     * @version 1.0
     * @author Luohaihua <Luohaihua@misrobot.com>
     * @date 2015-12-11 16:50
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     *
     */
    public function getBestTime(Request $request){
        $this->validate($request,[
            'id'    =>  'required|integer',
        ]);
        $applyId    =$request->get('id');
        $apply      =ResourcesClassroomApply::find($applyId);
        $time       =strtotime($apply->begin_datetime)-strtotime($apply->end_datetime);
        $timeList   =MscCommon::classroomEmptyTime($apply->resources_lab_id,$time);
        $timeData   =array_shift($timeList);
        $timeResult =[
            'day'   =>date('Y-m-d',$timeData[0]),
            'start' =>date('H:i',$timeData[0]),
            'end'   =>date('H:i',$timeData[1])
        ];
        return response()->json(
            $this->success_data(['time'=>$timeResult])
        );
    }

    /**
     *
     * @api POST /msc/admin/courses/change-old-plan
     * @access public
     *
     * @param Request $request post请求<br><br>
     * <b>post请求字段：</b>
     * * int         id           id(必须的)
     * * int          classroom    教室ID(必须的)
     * * datetime        start        开始时间(必须的)e.g:2015-11-11 11:11:11
     * * datetime        end          结束时间(必须的)e.g:2015-11-11 11:11:11
     *
     * @return object
     *
     * @version 1.0
     * @author Luohaihua <Luohaihua@misrobot.com>
     * @date 2015-12-01 19:22
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     *
     */
    public function postChangeOldPlan(Request $request){
        $this->validate($request,[
            'id'        =>  'required|integer',
            'classroom' =>  'required|integer',
            'start'     =>  'required|date_format:Y-m-d H:s:i',
            'end'       =>  'required|date_format:Y-m-d H:s:i',
            'apply_id'  =>  'required|integer',
        ]);
        $id         =$request->get('id');
        $classroom  =$request->get('classroom');
        $start      =$request->get('start');
        $end        =$request->get('end');
        $apply_id   =$request->get('apply_id');
        $day        =date('Y-m-d',$start);
        $start      =date('H:i',$start);
        $end        =date('H:i',$end);
        try{
            $plan       =$this->changeOldPlan($id,$classroom,$start,$end,$day);
            if($plan)
            {
                $apply=ResourcesClassroomApply::find($apply_id);
                $newPlan=$this->dealNewPlan($apply->id,$apply->begin_datetime,$apply->end_datetime);
                if(!$newPlan)
                {
                    throw new \Exception('新计划创建失败');
                }
                return response()->json(
                    $this->success_data(['id'=>$newPlan->id])
                );
            }
            else
            {
                throw new \Exception('通过失败');
            }
        }catch (\Exception $ex)
        {
            return response()->json($this->fail($ex));
        }
    }

    /**
     *
     * @access private
     *
     * @param Request $request post请求<br><br>
     * <b>post请求字段：</b>
     * * string        $oldPlanId       原计划ID(必须的)
     * * string        $classroomId     新计划教室ID(必须的)
     * * string        $start           新计划上课时间(必须的)
     * * string        $end             新计划下课时间(必须的)
     * * string        $day             新计划上课日期(必须的)
     *
     * @return object
     *
     * @version 1.0
     * @author Luohaihua <Luohaihua@misrobot.com>
     * @date ${DATE} ${TIME}
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     *
     */
    private function changeOldPlan($oldPlanId,$classroomId,$start,$end,$day){
        $plan   =ResourcesClassroomPlan::find($oldPlanId);

        $currentdate    =date('Y-m-d',strtotime($day));
        $endtime        =date('H:i:s',strtotime($end));
        $begintime      =date('H:i:s',strtotime($start));

        //教室 课程关联 数据
        $relativeData   =[
            'resources_lab_id'=>$classroomId,
            'course_id'=>$plan->course_id,
        ];
        $connection     = DB::connection('msc_mis');
        $connection     ->beginTransaction();
        try {
            //创建或获取 新的课程与教师的关联
            //$relation=ResourcesClassroomCourses::firstOrCreate($relativeData);
            $ResourcesClassroomCourses      = new ResourcesClassroomCourses();
            foreach ($relativeData as $key => $rel_where) {
                $ResourcesClassroomCourses  = $ResourcesClassroomCourses->where($key, '=', $rel_where);
            }
            $relation   = $ResourcesClassroomCourses->first();
            if (empty($relation)) {
                throw new \Exception('没有找到有此功能的教室');
            }
            if (!$relation) {
                $connection->rollback();
            }
            $data       = [
                'course_id'     => $plan->course_id,
                'resources_lab_course_id' => $relation->id,
                'currentdate'   => $currentdate,
                'begintime'     => $begintime,
                'endtime'       => $endtime,
            ];
            //检查课程时间冲突
            //$connection->enableQueryLog();
            $conflicts  = ResourcesClassroomPlan::leftJoin(
                'resources_lab_courses',
                function ($join) {
                    $join->on('resources_lab_courses.id', '=', 'resources_lab_plan.resources_lab_course_id');
                })->where('resources_lab_courses.resources_lab_id', '=', $relation->resources_lab_id)
                ->whereRaw(
                    'unix_timestamp(currentdate)= ? and unix_timestamp(begintime)<=? or unix_timestamp(endtime)>=? ',
                    [strtotime($currentdate), strtotime($endtime), strtotime($begintime)]
                )->get();
            //如果没有冲突
            if (count($conflicts) == 0) {
                //修改计划
                foreach ($data as $field => $value) {
                    $plan   ->  $field = $value;
                }
                $result     =   $plan->save();
                if (!$result) {
                    $connection ->rollback();
                }
                $result     =   $plan;
            }
            else //如果有冲突
            {
                //新增新计划
                $newPlan    =   ResourcesClassroomPlan::create($data);
                //取消原计划
                $result     =   false;
                foreach ($conflicts as $conflict) {
                    $conflict   ->  status   = -1;
                    $result     =   $conflict->save();
                    if (!$result) {
                        $connection->rollback();
                        break;
                    }
                    $data   = [
                        'original_plan_id' => $conflict->id,
                        'new_plan_id' => $newPlan->id,
                        'description' => '',
                    ];
                    //创建变更记录
                    $result = ResourcesClassroomPlanAlter::firstOrCreate($data);
                    if (!$result) {
                        $connection ->rollback();
                        break;
                    }
                }
            }
            $connection ->commit();
            return  $result;
        }
        catch(\Exception $ex)
        {
            throw $ex;
        }
    }

    /**
     *  变更并通过紧急预约
     * @api GET /msc/admin/courses/change-provisional
     * @access public
     *
     * @param Request $request post请求<br><br>
     * <b>post请求字段：</b>
     * * string        id        申请ID(必须的)
     * * string        start        变更后的上课时间(必须的) e.g:2015-11-11 11:11:12
     * * string        end        变更后的下课时间(必须的) e.g:2015-11-11 11:11:12
     *
     * @return json {id:安排后的计划ID}
     *
     * @version 1.0
     * @author Luohaihua <Luohaihua@misrobot.com>
     * @date 2015-12-01 23:00
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     *
     */
    public function postChangeProvisional(Request $request){
        $this->validate($request,[
            'id'    =>  'required|integer',
            'start' =>  'required|date_format:Y-m-d H:s:i',
            'end'   =>  'required|date_format:Y-m-d H:s:i',
        ]);
        $id     =   $request->get('id');
        $start  =   $request->get('start');
        $end    =   $request->get('end');

        try{
            $newPlan=$this->dealNewPlan($id,$start,$end);
            if($newPlan)
            {
                return response()->json(
                    $this->success_data(['id'=>$newPlan->id])
                );
            }
            else
            {
                throw new \Exception('操作失败');
            }
        }
        catch (\Exception $ex)
        {
            return response()->json($this->fail($ex));
        }
    }

    /**
     * 通过紧急课程预约
     * @api POST /msc/admin/courses/marlboro-provisional
     * @access public
     *
     * @param Request $request post请求<br><br>
     * <b>post请求字段：</b>
     * * string        id        申请ID(必须的)
     *
     * @return JSON {id:安排后的计划ID}
     *
     * @version 1.0
     * @author Luohaihua <Luohaihua@misrobot.com>
     * @date 2015-12-02 10:44
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     *
     */
    public function postMarlboroProvisional(Request $request){
        $this->validate($request,[
            'id'            =>  'required|integer',
        ]);
        $id=$request->get('id');
        $apply  =ResourcesClassroomApply::find($id);
        try
        {
            $newPlan=$this->dealNewPlan($id,$apply->begin_datetime,$apply->end_datetime);
            if($newPlan)
            {
                return response()->json(
                    $this->success_data(['id'=>$newPlan->id])
                );
            }
            else
            {
                throw new \Exception('操作失败');
            }
        }
        catch(\Exception $ex)
        {
            return response()->json($this->fail($ex));
        }
    }

    /**
     * 处理新增紧急课程计划
     * @access private
     * * int            id          申请ID(必须的)
     * * datetime       start       上课时间(必须的)
     * * datetime       end         下课时间(必须的)
     *
     * @return object 计划数据对象
     *
     * @version 1.0
     * @author Luohaihua <Luohaihua@misrobot.com>
     * @date 2015-12-02 10:45
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     *
     */
    private function dealNewPlan($id,$start,$end){
        $connection     = DB::connection('msc_mis');
        $connection     ->beginTransaction();
        try{
            $apply  =ResourcesClassroomApply::find($id);
            if(empty($apply))
            {
                throw new \Exception('没有找到该申请');
            }
            //制作课程与教室关系
            $relationData=[
                'resources_lab_id'=>$apply->resources_lab_id,
                'course_id'=>0,
            ];
            $day    =date('Y-m-d',strtotime($start));
            $start  =date('H:i',strtotime($start));
            $end    =date('H:i',strtotime($end));
            //获取课程与教室关系 对象
            $relation   =ResourcesClassroomCourses::firstOrCreate($relationData);
            if(empty($relation))
            {
                throw new \Exception('课程教室关联失败');
            }
            $data       =[
                'resources_lab_course_id' =>$relation->id,
                'course_id'                     =>0,
                'currentdate'                   =>$day,
                'begintime'                     =>$start,
                'endtime'                       =>$end,
                'type'                          =>3,
                'status'                        =>0,
            ];
            //检查是否冲突
            $conflicts  = ResourcesClassroomPlan::leftJoin(
                'resources_lab_courses',
                function ($join) {
                    $join->on('resources_lab_courses.id', '=', 'resources_lab_plan.resources_lab_course_id');
                })->where('resources_lab_courses.resources_lab_id', '=', $relation->resources_lab_id)
                ->whereRaw(
                    'unix_timestamp(currentdate)= ? and unix_timestamp(begintime)<=? or unix_timestamp(endtime)>=? ',
                    [strtotime($day), strtotime($end), strtotime($start)]
                )->get();
            //新建计划
            $newPlan    =   ResourcesClassroomPlan::create($data);
            //如果有冲突 将冲突写入记录表，并且取消原计划
            if (count($conflicts) != 0) {
                //取消原计划
                $result     =   false;
                foreach ($conflicts as $conflict) {
                    $conflict   ->  status   = -1;
                    $result     =   $conflict->save();
                    if (!$result) {
                        throw new \Exception('变更原计划状态失败');
                        break;
                    }
                    $data   = [
                        'original_plan_id'  => $conflict->id,
                        'new_plan_id'       => $newPlan->id,
                        'description'       => '',
                    ];
                    //创建变更记录
                    $result = ResourcesClassroomPlanAlter::firstOrCreate($data);
                    if (!$result)
                    {
                        throw new \Exception('变更记录创建失败');
                        break;
                    }
                }
            }
            //创建计划和教师的关联
            $data_teacher_relation  =[
                'teacher_id'                    =>  $apply->apply_uid,
                'resources_lab_plan_id'   =>  $newPlan->id
            ];
            $result =   ResourcesClassroomPlanTeacher::create($data_teacher_relation);
            if (!$result) {
                throw new \Exception('负责教室信息保存失败');
            }
            $connection ->commit();
            $content='恭喜你，你的课程预约审核通过!'.'\n';
            $content.='课程名称：'.$apply->detail.'\n';
            $content.='授课老师：'.$apply->applyer->name.'\n';
            $content.='上课时间：'.$day.' '.$start.'\n';
            $content.='教室：'.$apply->classroom->name.'\n';
            $this->sendMsg($content,$apply->applyer->openid);
            return $newPlan;
        }
        catch (\Exception $ex)
        {
            $connection->rollback();
            throw $ex;
        }
    }

    /**
     * 拒绝申请
     * @api POST /msc/admin/courses/refuse-provisional-apply
     * @access public
     *
     * @param Request $request post请求<br><br>
     * <b>post请求字段：</b>
     * * int        id        申请ID(必须的)
     * * string     reject    拒绝理由(必须的)
     *
     * @return json {id:被拒绝申请id}
     *
     * @version 1.0
     * @author Luohaihua <Luohaihua@misrobot.com>
     * @date 2015-12-02 14:42
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     *
     */
    public function postRefuseProvisionalApply(Request $request){
        $id=$request->get('id');
        $reject=e($request->get('reject'));

        try{
            $apply=ResourcesClassroomApply::find($id);
            if(empty($apply))
            {
                throw new \Exception('没有找到该申请');
            }
            $apply  ->status    =   2;
            $apply  ->reject    =   $reject;
            if($apply->save())
            {
                $this->sendMsg('抱歉，你的课程预约审核未通过。请你咨询管理员后再次进行课程预约申请',$apply->applyer->openid);
                return response()->json(
                    $this->success_data(['id'=>$id])
                );
            }
            else
            {
                throw new \Exception('变更失败');
            }
        }catch (\Exception $ex)
        {
            return response()   ->json($this->fail($ex));
        }
    }

    /**
     * 下载课程清单模板excl
     * @api GET /msc/wechat/resources-manager/download-courses-list-tpl
     * @access public
     *
     *
     * @return 下载文件流
     *
     * @version 1.0
     * @author Luohaihua <Luohaihua@misrobot.com>
     * @date 2015-12-02 15:26
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     *
     */
    public function getDownloadCoursesListTpl(){
        $this->downloadfile('coursesList.xlsx',public_path('download').'/coursesList.xlsx');
    }
    /**
     * 下载课程计划导入清单模板excl
     * @api GET /msc/wechat/resources-manager/download-courses-list-tpl
     * @access public
     *
     *
     * @return 下载文件流
     *
     * @version 1.0
     * @author Luohaihua <Luohaihua@misrobot.com>
     * @date 2015-12-02 15:26
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     *
     */
    public function getDownloadCoursesPlanTpl(){
        $this->downloadfile('coursesPlan.xlsx',public_path('download').'/coursesPlan.xlsx');
    }
    private function downloadfile($filename,$filepath){
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename='.basename($filename));
        header('Content-Transfer-Encoding: binary');
        header('Expires: 0');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Pragma: public');
        header('Content-Length: ' . filesize($filepath));
        readfile($filepath);
    }
    //像微信用户发送普通文本消息
    private function sendMsg($msg,$openid){
        if(empty($openid))
        {
            throw new \Exception('没有找到用户的微信OpenID');
        }
        $userService = new \Overtrue\Wechat\Staff(config('wechat.app_id'), config('wechat.secret'));
        return $userService->send($msg)->to($openid);
    }

    /**
     * 获取课程信息和摄像机信息
     * @api GET /msc/admin/courses/courses-vcr
     * @access public
     *
     * @param Request $request get请求<br><br>
     * <b>get请求字段：</b>
     * * int           lab_id               教室
     *
     *
     * @return view  courses_name:课程名称 teacher_name：老师名称  lab_name：教师名称  total：应到人数   unabsence：实到人数  vcrs:摄像机信息
     *
     * @version 1.0
     * @author  gaoshichong
     * @date 2015-12-15 11:04:22
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     *
     */
    public function getCoursesVcr(Request $request){
        $lab_id=$request->get("lab_id");
        try{
            $model=new ResourcesClassroom();
            $rst=$model->getClassroomDetails($lab_id)->first();

            $vcrs=$model->getClassroomVideo($lab_id);

            $data    =      [
                'courses_name'           =>    $rst->courses_name,
                'teacher_name'           =>    $rst->teacher_name,
                'lab_name'               =>    $rst->lab_name,
                'vcrs'                   =>    $vcrs,
                'total'                  =>    40,
                'unabsence'              =>    39,
            ];
            dd($data);
            //PC-Admin-002-课程监管.png
            return view('msc::admin.coursemanage.course_observe_detail',$data);
        }catch (\Exception $ex){
            $this->fail($ex);
        }
    }
    /**
     * 获取下载课程信息
     * @api GET /msc/admin/courses/download-course
     * @access public
     *
     * @param Request $request get请求<br><br>
     * <b>get请求字段：</b>
     * * int           plan_id               计划id
     *
     *
     * @return view
     *
     * @version 1.0
     * @author  gaoshichong
     * @date 2015-12-16 15:39:50
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     *
     */
    public function getDownloadCourse(Request $request){
        $plan_id=$request->get("plan_id");
        try{
            $model = new ResourcesClassroom();
            $data = $model -> getCourseVcrByPlanId($plan_id);
            dd($data);
            return view('',$data);
        }catch (\Exception $ex){
            $this -> fail($ex);
        }
    }
	/**
     *  下载视频前检查
     * @api GET /msc/admin/courses/video-check
     * @access public
     *
     * @param Request $request post请求<br><br>
     * <b>post请求字段：</b>
     * * string        id        摄像头ID(必须的)
     * * string        start     视频开始时间(必须的) e.g:
     * * string        end       视频结束时间(必须的) e.g:
     *
     * @return json {url:下载视频文件的地址}
     *
     * @version 1.0
     * @author Luohaihua <Luohaihua@misrobot.com>
     * @date 2015-12-15
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     *  //http://hx.mis_api.local/msc/admin/courses/video-check?id=1&start=2015-12-14%2008:00:00&end=2015-12-14%2009:00:00
     */
    public function getVideoCheck(Request $request){
        $this->validate($request,[
            'id'            =>  'required|integer',
            'start'         =>  'required|date_format:Y-m-d H:i:s',
            'end'           =>  'required|date_format:Y-m-d H:i:s',
        ]);
        $id     =   $request    ->  get('id');
        $start  =   $request    ->  get('start');
        $end    =   $request    ->  get('end');

        $host   =   config('msc.video_host');
        $port   =   config('msc.video_port');
        $param    =   [
            'channel'   =>  $id,
            'start'     =>  $start,
            'stop'      =>  $end,
        ];
        try{
            $jsonData   =   $this   ->  socket($host,$port,json_encode($param),1);
            if($jsonData)
            {
                if($json    =   json_decode($jsonData))
                {
                    $url    =   '';
                    //请求成功
                    if($json->code  ==  2000)
                    {
                        $url    =   $json   ->  path;
                    }
                    else
                    {
                        throw new \Exception($json    ->  msg);
                    }
                    if(empty($url))
                    {
                        throw new \Exception('没有获取到源文件路径');
                    }
                    response()->json(
                        $this   ->  success_data(['url' =>  $url,1,'获取成功'])
                    );
                }
                else
                {
                    throw new \Exception('数据源解析错误，请联系管理员');
                }
            }
            else
            {
                throw new \Exception('获取视频源地址失败');
            }
        }
        catch(\Exception $ex)
        {
            response()->json(
                $this->fail($ex)
            );
        }
    }
    /**
     * 根据get请求获取对应教室
     * @api GET /msc/admin/courses/class-observe
     * @access public
     * @return array
     * @version 1.0
     * @author Jiangzhiheng <jiangzhiheng@misrobot.com>
     * @date 2015-12-15 14:50
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     * @param Request $request
     */
    public function getClassObserve(Request $request)
    {
        $this->validate($request, [
            'keyword' => 'sometimes',
        ]);

        $keyword = e(urldecode($request->get('keyword')));
        $ResourcesClassroom = new ResourcesClassroom();
        $data = $ResourcesClassroom->getClassroomName($keyword);
        return view('msc::admin.coursemanage.course_observe', ['data' => $data]);
    }

    /**
     * 根据ajax请求获取对应教室的详情
     * @api GET /msc/admin/courses/class-observe-video
     * @access public
     * @return json teacher_name:老师名字,courses_name:课程名字,id:教室id,video:摄像头id和名字,video_count:摄像头数量
     * @version 1.0
     * @author Jiangzhiheng <jiangzhiheng@misrobot.com>
     * @date 2015-12-15 14:50
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     * @param Request $request
     */
    public function getClassObserveVideo(Request $request) {
        $id = $request->get('id');
        $ResourcesClassroom = new ResourcesClassroom();
        $data = $ResourcesClassroom->getClassroomDetails($id);
        $data = $data->toArray();
        //通过教室ID查询摄像头ID和摄像头名字
        $video = $ResourcesClassroom->getClassroomVideo($id);
        $videoCount = count($video);
        $video = $video->toArray();
        if (count($data) === 0) {
            $data = [
                [
                    'teacher_name' => '目前未有老师上课',
                    'courses_name' => '目前无课',
                ],
            ];
        }

        foreach ($data as $item) {
            $item['id']         = $id;
            $item['video']      = $video;
            $item['video_count'] = $videoCount;
        }

        return response()->json($item);
    }

    /*socket收发数据
        @host(string) socket服务器IP
        @post(int) 端口
        @str(string) 要发送的数据
        @back 1|0 socket端是否有数据返回
        返回true|false|服务端数据
    */
    protected function socket($host,$port,$str,$back=0){
        $socket = socket_create(AF_INET,SOCK_STREAM,0);
        if ($socket < 0) return false;
        $result = @socket_connect($socket,$host,$port);
        if ($result == false)return false;
        socket_write($socket,$str,strlen($str));

        if($back!=0){
            $input = socket_read($socket,1024);
            socket_close ($socket);
            return $input;
        }else{
            socket_close ($socket);
            return true;
        }
    }

    /**
     * 单个视频
     * @api GET /msc/admin/courses/classroom-vcr
     * @access public
     *
     * @param Request $request post请求<br><br>
     * <b>post请求字段：</b>
     * * string        参数英文名        参数中文名(必须的)
     *
     * @return view {摄像头ID：id，'教室ID':$vcrRelation->resources_lab_id}
     *
     * @version 1.0
     * @author Luohaihua <Luohaihua@misrobot.com>
     * @date 2015-12-16 15:44
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     *
     */
    public function getClassroomVcr(Request $request){
        $id =   intval( $request    ->  id);
        if(empty($id))
        {
            abort(404);
        }
        $vcrRelation    =   ResourcesLabVcr::where('vcr_id','=',$id)->first();
        if(empty($vcrRelation))
        {
            abort(404);
        }
        return view('msc::admin.coursemanage.course_vcr',['id'=>$id,'vcrRelation'=>$vcrRelation]);
    }

}