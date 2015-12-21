<?php
/**
 * 资源-教室
 * author Luohaihua
 * date 2015-11-24
 */
namespace Modules\Msc\Entities;

use Illuminate\Database\Eloquent\Collection;
use Modules\Msc\Entities\CommonModel;
use Modules\Msc\Entities\ResourcesLabCalendar;
use DB;
class ResourcesClassroom extends  CommonModel {
    protected $connection	=	'msc_mis';
    protected $table 		= 	'resources_lab';
    public $timestamps	=	true;
    protected $primaryKey	=	'id';
    public $incrementing	=	true;
    protected $guarded 		= 	[];
    protected $hidden 		= 	[];

    protected $fillable 	=	['name', 'code', 'location', 'begintime', 'endtime', 'manager_id', 'manager_name', 'manager_mobile', 'detail', 'status', 'opened','person_total'];
    public $search          =   ['manager_name','manager_mobile','detail','code'];

    protected $statusAttrName =   [
        0   =>  '不允许预约使用',
        1   =>  '可预约使用',
        2   =>  '已预约'
    ];
    /**
     * 所属资源
     */
    public function resources(){
        return $this->belongsTo('Modules\Msc\Entities\Resources','id','item_id')->where('type','=','CLASSROOM');
    }

/*    public function getStatusAttribute($value){
        return $this->statusAttrName[$value];
    }
*/
    public function getstatusAttrName(){
        return $this->statusAttrName;
    }

    /**
     * 已分配到该资源的课程
     */
    public function getCourses(){
        $relations=$this->courseClassroomRelations;
        $coursesCollect=[];
        foreach($relations as $relation)
        {
            $course=$relation->courses;
            if(!in_array($course,$coursesCollect))
            {
                $coursesCollect[]=$course;
            }
        }
        $coursesCollect=Collection::make($coursesCollect);
        return $coursesCollect;
    }

    /*
     * 已分配 课程与教室的关系 集合
     */
    public function courseClassroomRelations(){
        return $this->hasMany('Modules\Msc\Entities\ResourcesClassroomCourses','resources_lab_id','id');
    }


    //获取教室资源 列表 （唐俊）
    public function getClassroomList(){
        return $this->get();
    }

    public function resourcesLabCalendar(){
        return $this->hasMany('Modules\Msc\Entities\ResourcesLabCalendar','resources_lab_id','id');
    }


    public function resourcesClassroomApply(){
        return $this->hasMany('Modules\Msc\Entities\ResourcesClassroomApply','resources_lab_id','id');
    }

    //获取实验室资源列表
    public function getLaboratoryClassroomList($data){

        $result = $this->where('opened','=',1)->with(['resourcesLabCalendar' => function($query)
        {
            if(!empty($data['month']) && !empty($data['days'])){
                $query->where('resources_lab_calendar.month', 'like', '%'.$data['month'].'%')->where('resources_lab_calendar.days','like','%'.$data['days'].'%');
            }

        },'resourcesClassroomApply'=>function($q){

            $q->where('resources_lab_apply.apply_user_type','=',0) ;

        }])->paginate(7);

        return $result;
    }
    //新增 实验室
    public function addLabResource($input){
        $data   =   [
            'name'  =>  $input['name'],
            'code'  =>  $input['code'],
            'location'  =>  $input['location'],
            'begintime'  =>  $input['begintime'],
            'endtime'  =>  $input['endtime'],
            'opened'  =>  $input['opened'],
            'manager_id'  =>  $input['manager_id'],
            'manager_name'  =>  $input['manager_name'],
            'manager_mobile'  =>  $input['manager_mobile'],
            'detail'  =>  $input['detail'],
            'status'  =>  1,
           // 'resources_type'  =>  1,
        ];
        return $this->create($data);
	}
    //新增教室
    public function addClassRommResources($request){
        $imagesArray = $request->get('images_path');

        $formData    = $request->only(['name', 'code', 'location', 'begintime', 'endtime', 'manager_name', 'manager_mobile','detail','person_total']);

        $formData['opened'] = empty($formData['opened']) ? 0 : $formData['opened'];
        $formData['manager_id'] = empty($formData['manager_id']) ? 0 : $formData['manager_id'];
        $connection = DB::connection('msc_mis');
        try{
            $connection->beginTransaction();
            $resources =$this->create($formData);
            if(!$resources){
                throw new \Exception('新增教室失败！');
            }

            $_formData = [
                'type'        => 'CLASSROOM',
                'item_id'     => $resources->id,
                'description' => '',
            ];
            $_resources = Resources::create($_formData);

            if(!$_resources)
            {
                throw new \Exception('新增教室失败！');
            }
            if (!empty($imagesArray))
            {
                foreach($imagesArray as $item)
                {
                    $data=[
                        'resources_id' => $_resources->id,
                        'url'          => $item,
                        'order'        => 0,
                        'descrption'   => '',
                    ];
                    $result = ResourcesImage::create($data);
                    if(!$result)
                    {
                        throw new \Exception('图片保存失败！');
                    }
                }
            }
            $connection->commit();
            return true;
        }catch (\Exception $ex){
            $connection->rollback();
            return $ex;
        }
    }

    //给教室选择下拉列表提供数据
    public function getClassroomName($keyword = '') {

        $result = $this->select([
            "$this->table" . '.id as id',
            "$this->table" . '.name as name',
            "$this->table" . '.code as code'
        ]);
        if ($keyword !== null) {
            $result->where($this->table.'.name','like','%'.$keyword.'%');
        }
        return $result->get();
    }

    //给教室的具体监控界面提供数据
    public function getClassroomDetails ($id) {

//        $builder = $this->where($this->table.'.id','=',$id)->with(['courseClassroomCourses' => function ($q) {
//            $q->with(['resourcesLabPlan' => function ($q) {
//                $q->where('resources_lab_plan.begintime','<',strtotime(date('Y-m-d')))->where('resources_lab_plan.endtime','>',strtotime(date('Y-m-d')))
//                    ->with('course');
//            }]);
//        }]);
        $builder = $this->leftJoin (
            'resources_lab_courses',
            function ($join) {
                $join->on('resources_lab_courses.resources_lab_id','=',$this->table.'.id');

            }
        )   ->  leftJoin (
            'resources_lab_plan',
            function ($join) {
                $join->on('resources_lab_plan.course_id','=','resources_lab_courses.course_id');
            }
        )   ->  leftJoin (
            'courses',
            function ($join) {
                $join->on('resources_lab_courses.course_id','=','courses.id');
            }
        )   ->  leftJoin (
            'teacher_courses',
            function ($join) {
                $join->on('courses.id','=','teacher_courses.course_id');
            }
        )   ->  leftJoin (
            'teacher',
            function ($join) {
                $join->on('teacher_courses.teacher_id','=','teacher.id');
            }
        )
            ->  whereRaw ('unix_timestamp(resources_lab_plan.currentdate) = ?',[strtotime(date('Y-m-d'))])
            ->  whereRaw ('unix_timestamp(resources_lab_plan.begintime) < ?',[strtotime(date('H:i:s'))])
            ->  whereRaw ('unix_timestamp(resources_lab_plan.endtime) > ?',[strtotime(date('H:i:s'))])
            ->  where($this->table.'.id','=',$id)
            ->  select([
                'courses.name as courses_name',
                'teacher.name as teacher_name',
                $this->table.'.name as lab_name',
                'resources_lab_plan.id as pid'
            ]);
        return $builder->get();
    }
    
    //根据计划id获取课程视频信息
    public function getCourseVcrByPlanId($id){

        $plan = ResourcesClassroomPlan::find($id);
        $teachers = $plan->teachersRelation;
        $teacher_name = $teachers->first()->teacher->name;
        $classroom_id = $plan -> classroomCourses -> classroom -> id;
        $resourceslab = ResourcesLabVcr::where('resources_lab_id',$classroom_id) -> get();
        foreach($resourceslab as $item){
            $vcr = array();
            $vcr['vcr_id']   = Vcr::find($item -> vcr_id)->id;
            $vcr['vcr_name'] = Vcr::find($item -> vcr_id)->name;
            $vcrs[] = $vcr;
        }
        $unabsence = ResourcesClassroomCourseSign::where('resources_lab_plan_id','=',$id)->count();
        $ResourcesClassroomPlanGroup = new ResourcesClassroomPlanGroup();
        $total = $ResourcesClassroomPlanGroup->getTotal($id);
        $data=[
            "currentdate"       =>    $plan->currentdate,
            "begintime"         =>    $plan->begintime,
            "endtime"           =>    $plan->endtime,
            "courses_name"      =>    $plan->course->name,
            "lab_ame"           =>    $plan->classroomCourses->classroom->name,
            "teacher_name"      =>    $teacher_name,
            'vcrs'              =>    $vcrs,
            'unabsence'         =>    $unabsence,
            'total'             =>    $total,
        ];
        return $data;
	}

//$id为教室ID
    public function getClassroomVideo($id) {
        $builder = $this->leftJoin(
            'resources_lab_vcr',
            function ($join) {
                $join->on('resources_lab_vcr.resources_lab_id','=',$this->table.'.id');
            }
        )   ->leftJoin(
            'vcr',
            function ($join) {
                $join->on('vcr.id','=','resources_lab_vcr.vcr_id');
            }
        )   ->where($this->table.'.id','=',$id)
            ->select([
                'vcr.id as vid',
                'vcr.name as vname',
        ]);

        return $builder->get();

    }

    //获取实验室
    // 获得pc端开放实验室使用历史记录列表
    public function getPcList ($where)
    {
        $search = empty($where['keyword']) ? null : $where['keyword'];
        $builder = $this;
        if(!empty($seach)){
            $builder = $builder->where('name','like',$search,'like');
        }
        $pagination = $builder->orderBy('id','desc')->paginate(config('msc.page_size',10));
        return $pagination;
    }
}