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

    protected $fillable 	=	['name', 'code', 'location', 'begintime', 'endtime', 'manager_id', 'manager_name', 'manager_mobile', 'detail', 'status', 'opened'];
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
            'resources_type'  =>  1,
        ];
        return $this->create($data);
	}

    //给教室选择下拉列表提供数据
    public function getClassroomName($keyword = '') {
        if ($keyword !== '') {
            $result = $this->where($this->table.'.code','like','%'.$keyword.'%')->where($this->table.'.name','like','%'.$keyword.'%');
        }
        $result = select([
            "$this->table" . '.id as id',
            "$this->table" . '.name as name'
        ])->get();

        return $result;
    }

    //给教室的具体监控界面提供数据
	//TODO: 罗海华 修改 public 为 protected 禁用外部调用 2015-12-15 20:15
    protected function getClassroomDetails($id) {
        $builder = $this->where($this->table.'.id','=',$id)->with(['courseClassroomCourses' => function ($q) {
            $q->with(['resourcesLabPlan' => function ($q) {
                $q->where('resources_lab_plan.begintime','<',strtotime(date('Y-m-d')))->where('resources_lab_plan.endtime','>',strtotime(date('Y-m-d')))
                    ->with('course');
            }]);
        }]);
        return $builder;
    }
}