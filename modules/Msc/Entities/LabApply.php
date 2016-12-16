<?php
/**
 * Created by PhpStorm.
 * @author tangjun <tangjun@sulida.com>
 * @date 2016-01-07 11:44
 * @copyright  2013-2017 sulida.com  Inc. All Rights Reserved
 */

namespace Modules\Msc\Entities;
use Illuminate\Database\Eloquent\Model;


class LabApply  extends Model
{
    protected $connection	=	'msc_mis';
    protected $table 		= 	'lab_apply';
    public $timestamps	=	true;
    protected $primaryKey	=	'id';
    protected $fillable 	=	['lab_id', 'type','begintime','endtime','description','apply_user_id','course_name','apply_time','status','user_type'];

    /**
     * @param $where
     * @param $status
     * @author tangjun <tangjun@sulida.com>
     * @date    2016年1月11日14:03:25
     * @copyright  2013-2017 sulida.com  Inc. All Rights Reserved
     */
    public function GetLabApplyData($where,$status){

        //return
    }

    public function PlanApply(){
        return  $this->hasMany('Modules\Msc\Entities\PlanApply','apply_id','id');
    }

    //用户管理员
    public function user(){

        return $this->hasOne('App\Entities\User','id','apply_user_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     * @author tangjun <tangjun@sulida.com>
     * @date    2016年1月18日11:14:25
     * @copyright  2013-2017 sulida.com  Inc. All Rights Reserved
     */
    public function Laboratory(){
        return  $this->hasOne('Modules\Msc\Entities\Laboratory','id','lab_id');
    }


    //后台获取审核列表
    public function get_check_list($keyword="",$type=1,$id=''){
        $userDb    = config('database.connections.sys_mis.database');
        $userTable = $userDb.'.users';

        $plan_applyDb    = config('database.connections.msc_mis.database');
        $plan_applyTable = $plan_applyDb.'.plan_apply';

        $open_planDb    = config('database.connections.msc_mis.database');
        $open_planTable = $open_planDb.'.open_plan';

        $lab_applyDb    = config('database.connections.msc_mis.database');
        $lab_applyTable = $lab_applyDb.'.lab_apply';

        $labDb    = config('database.connections.msc_mis.database');
        $labTable = $labDb.'.lab';


        if(!empty($keyword)){
            $builder = $this->where($userTable.'.name','like','%'.$keyword.'%');
        }else{
            $builder = $this;
        }

        if($type == 1){
            $builder = $builder->where($lab_applyTable.'.status','=',1);
        }else{
            $builder = $builder->where($lab_applyTable.'.status','<>',1);
        }
        if(!empty($id)){
            $builder = $builder->where($labTable.'.manager_user_id','=',$id);
        }
        $builder = $builder->leftJoin($userTable, function($join) use($lab_applyTable) {
            $join->on($join->table.'.id', '=', $lab_applyTable.'.apply_user_id');
        })->leftJoin($labTable, function($join) use($lab_applyTable) {
            $join->on($join->table.'.id', '=', $lab_applyTable.'.lab_id');
        })->with(['PlanApply'=>function($PlanApply){
            $PlanApply->with('OpenPlan');
        }]);
        $data = $builder
            ->select($userTable.'.name',$lab_applyTable.'.*',$labTable.'.name as labname',$labTable.'.floor',$labTable.'.code')
            ->orderby($lab_applyTable.'.apply_time')->paginate(config('msc.page_size',10));
        //dd($data);
        return $data;

    }

    //后台获取审核列表单条信息
    public function getonelaborderdetail($id){
        $userDb    = config('database.connections.sys_mis.database');
        $userTable = $userDb.'.users';

        $plan_applyDb    = config('database.connections.msc_mis.database');
        $plan_applyTable = $plan_applyDb.'.plan_apply';

        $open_planDb    = config('database.connections.msc_mis.database');
        $open_planTable = $open_planDb.'.open_plan';

        $lab_applyDb    = config('database.connections.msc_mis.database');
        $lab_applyTable = $lab_applyDb.'.lab_apply';

        $labDb    = config('database.connections.msc_mis.database');
        $labTable = $labDb.'.lab';

        $builder = $this->where($lab_applyTable.'.id','=',$id);
        $builder = $builder->leftJoin($userTable, function($join) use($lab_applyTable) {
            $join->on($join->table.'.id', '=', $lab_applyTable.'.apply_user_id');
        })->leftJoin($labTable, function($join) use($lab_applyTable) {
            $join->on($join->table.'.id', '=', $lab_applyTable.'.lab_id');
        })->with(['PlanApply'=>function($PlanApply){
            $PlanApply->with('OpenPlan');
        }]);

        $data = $builder->select($userTable.'.name',$lab_applyTable.'.*',$labTable.'.name as labname',$labTable.'.total',$labTable.'.floor',$labTable.'.code')
            ->first();
        //,$open_planTable.'.year',$open_planTable.'.month',$open_planTable.'.day',$open_planTable.'.begintime',$open_planTable.'.endtime'
        return $data;

    }

    //根据预约ID查找批量选择的所有相关数据
    public function getonelaborderdata($arr=''){
        $userDb    = config('database.connections.sys_mis.database');
        $userTable = $userDb.'.users';

        $plan_applyDb    = config('database.connections.msc_mis.database');
        $plan_applyTable = $plan_applyDb.'.plan_apply';

        $open_planDb    = config('database.connections.msc_mis.database');
        $open_planTable = $open_planDb.'.open_plan';

        $lab_applyDb    = config('database.connections.msc_mis.database');
        $lab_applyTable = $lab_applyDb.'.lab_apply';

        $labDb    = config('database.connections.msc_mis.database');
        $labTable = $labDb.'.lab';


        if(!empty($arr)){
            $builder = $this->whereIn($lab_applyTable.'.id',$arr);
        }else{
            $builder = $this;
        }

        $builder = $builder->leftJoin($userTable, function($join) use($lab_applyTable) {
            $join->on($join->table.'.id', '=', $lab_applyTable.'.apply_user_id');
        })->leftJoin($labTable, function($join) use($lab_applyTable) {
            $join->on($join->table.'.id', '=', $lab_applyTable.'.lab_id');
        })->with(['PlanApply'=>function($PlanApply){
            $PlanApply->with('OpenPlan');
        }]);
        $data = $builder->select($userTable.'.name',$lab_applyTable.'.*',$labTable.'.name as labname',$labTable.'.total',$labTable.'.floor',$labTable.'.code')->get();
        return $data;

    }

    /**
     * @param $uid
     * @return mixed
     * @author tangjun <tangjun@sulida.com>
     * @date   2016年1月14日17:21:30
     * @copyright  2013-2017 sulida.com  Inc. All Rights Reserved
     */
    public function MyApplyList($status,$uid,$user_type){
        return  $this->where('status','=',$status)->where('apply_user_id','=',$uid)->where('user_type','=',$user_type)->with(['Laboratory'=>function($Laboratory){
            $Laboratory->with('FloorInfo');
        },'PlanApply'=>function($PlanApply){
            $PlanApply->with(['OpenPlan']);
        }])->get();
    }


    //根据预约ID查找实验室的total
    public function get_total($id){
        $builder = $this->where('id','=',$id);
        $builder = $builder->leftJoin('lab', function($join){
            $join->on('lab.id', '=', 'lab_apply.lab_id');
        })->first();
    }

    //老师
    public function Teacher(){
        return  $this->hasOne('Modules\Msc\Entities\Teacher','id','apply_user_id');
    }
    /**
     * 获取已经完成的历史预约的数据
     * @author tangjun <tangjun@sulida.com>
     * @date    2016年1月18日11:09:16
     * @copyright  2013-2017 sulida.com  Inc. All Rights Reserved
     */
    public function HistoryLaboratoryApplyList($uid){
        $builder = $this->where('apply_user_id','=',$uid)->whereNotIn('status',[1])->with(['Laboratory'=>function($Laboratory){
            $Laboratory->with('FloorInfo');
        },'PlanApply'=>function($PlanApply){
            $PlanApply->with('OpenPlan');
        }]);
        return  $builder->orderby('id','desc')->paginate(config('msc.page_size',10));
    }
 /**
     * @param $apply_id
     * @author tangjun <tangjun@sulida.com>
     * @date    2016年1月18日12:03:44
     * @copyright  2013-2017 sulida.com  Inc. All Rights Reserved
     */
    public function GetApplyDetails($apply_id){
        $builder = $this->where('id','=',$apply_id)->with(['Laboratory'=>function($Laboratory){
            $Laboratory->with('FloorInfo');
        },'PlanApply'=>function($PlanApply){
            $PlanApply->with('OpenPlan');
        }]);
        return  $builder->first();
    }

    //查找普通实验室详情
    public function getLabdetail($id){
        $labapply = 'lab_apply';
        $builder = $this->where('lab_apply.id','=',$id)->leftjoin('lab',function($lab) use($labapply){
            $lab->on('lab.id','=',$labapply.'.lab_id');
        })->leftjoin('location',function($local){
            $local->on('location.id','=','lab.location_id');
        })->leftjoin('teacher',function($teacher){
            $teacher->on('teacher.id','=','lab_apply.apply_user_id');
        })->leftjoin('teacher_dept',function($teacher_dept){
            $teacher_dept->on('teacher_dept.id','=','teacher.teacher_dept');
        })->with('user')->select('lab_apply.*','lab.floor','lab.code as lcode','lab.name as lname','location.name as localname','teacher.code','teacher_dept.name as dname')->first();
        //dd($builder);
        return $builder;
    }

    //查找开放实验室详情
    public function getStudentLabdetail($id){
        $labapply = 'lab_apply';
        $builder = $this->where('lab_apply.id','=',$id)->leftjoin('lab',function($lab) use($labapply){
            $lab->on('lab.id','=',$labapply.'.lab_id');
        })->leftjoin('location',function($local){
            $local->on('location.id','=','lab.location_id');
        })->leftjoin('student',function($student){
            $student->on('student.id','=','lab_apply.apply_user_id');
        })->with('user')->select('lab_apply.*','lab.floor','lab.code as lcode','lab.name as lname','location.name as localname','student.name','student.code as scode','student.grade','student.professional','student.name')->get();
        //dd($builder);
        return $builder;
    }
}