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


    //后台获取审核列表
    public function get_check_list($keyword="",$status=0){
        $userDb    = config('database.connections.sys_mis.database');
        $userTable = $userDb.'.users';

        $plan_applyDb    = config('database.connections.msc_mis.database');
        $plan_applyTable = $plan_applyDb.'.plan_apply';

        $open_planDb    = config('database.connections.msc_mis.database');
        $open_planTable = $open_planDb.'.open_plan';

        $lab_applyDb    = config('database.connections.msc_mis.database');
        $lab_applyTable = $lab_applyDb.'.lab_apply';

        $labDb    = config('database.connections.msc_mis.database');
        $labTable = $labDb.'.lab_apply';

//        $builder = $this
//                    ->leftJoin($userTable, $userTable.'.id', '=', $lab_applyTable.'.apply_user_id')
//                    ->get();
//
//        dd($builder);

        $builder = $this->leftJoin($userTable, function($join) use($userTable, $lab_applyTable) {
            $join->on($userTable.'.id', '=', $lab_applyTable.'.apply_user_id');
        });

//        $builder = $this->leftJoin($open_planTable, function($join) use($open_planTable, $lab_applyTable) {
//            $join->on($open_planTable.'.id', '=', $lab_applyTable.'.apply_id');
//        });
//
//        $builder = $this->leftJoin($labTable, function($join) use($labTable, $open_planTable) {
//            $join->on($labTable.'.id', '=', $open_planTable.'.lab_id');
//        });
//        if($keyword){
//
//        }
        return $builder//->select([
//            $plan_applyTable.'.id as id',
//            $studentTable.'.name as name',
//            $studentTable.'.code as code',
//            $studentTable.'.grade as grade',
//            $studentTable.'.student_type as student_type',
//            $studentTable.'.professional as professional',
//            //$userTable.'.mobile as mobile',
//            //$userTable.'.idcard as idcard',
//            //$userTable.'.gender as gender',
//            //$userTable.'.status as status',
//        ])
           // ->orderBy($studentTable.'.id')
            ->paginate(config('msc.page_size',10));
        dd($builder);

    }
}