<?php
/**
 * Created by PhpStorm.
 * @author tangjun <tangjun@misrobot.com>
 * @date 2016-01-07 11:44
 * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
 */

namespace Modules\Msc\Entities;
use Illuminate\Database\Eloquent\Model;


class LabApply  extends Model
{
    protected $connection	=	'msc_mis';
    protected $table 		= 	'lab_apply';
    public $timestamps	=	true;
    protected $primaryKey	=	'id';
    protected $fillable 	=	['lab_id', 'type','status','apply_user_id','description','apply_time','course_name'];

    /**
     * @param $where
     * @param $status
     * @author tangjun <tangjun@misrobot.com>
     * @date    2016年1月11日14:03:25
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function GetLabApplyData($where,$status){

        //return
    }

    /**
     * @access public
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     * @author tangjun <tangjun@misrobot.com>
     * @date    2016年1月12日16:37:15
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function PlanApply(){
        return  $this->hasMany('Modules\Msc\Entities\PlanApply','apply_id','id');
    }


    //后台获取审核列表
    public function get_check_list($keyword="",$type=0){
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

        $plan_applyDb    = config('database.connections.msc_mis.database');
        $plan_applyTable = $plan_applyDb.'.plan_apply';
        if($type == 1){
            $builder = $this->where($lab_applyTable.'.status','=',1);
        }else{
            $builder = $this->where($lab_applyTable.'.status','>',1);
        }
        //dd($builder);

        $builder = $builder->leftJoin($userTable, function($join) use($lab_applyTable) {
            $join->on($join->table.'.id', '=', $lab_applyTable.'.apply_user_id');
        })->leftJoin($labTable, function($join) use($lab_applyTable) {
            $join->on($join->table.'.id', '=', $lab_applyTable.'.lab_id');
        })->leftJoin($plan_applyTable, function($join) use($lab_applyTable) {
            $join->on($join->table.'.apply_id', '=', $lab_applyTable.'.id');
        })->leftJoin($open_planTable, function($join) use($plan_applyTable) {
            $join->on($join->table . '.id', '=', $plan_applyTable . '.open_plan_id');
        });
        $data = $builder->select($userTable.'.name',$lab_applyTable.'.*',$labTable.'.name as labname',$plan_applyTable.'.open_plan_id',$open_planTable.'.begintime as obegintime',$open_planTable.'.endtime as oendtime')
            ->orderby($lab_applyTable.'.apply_time')->paginate(config('msc.page_size',10));
        dd($data);
        return $data;

    }
}