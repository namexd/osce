<?php
/**
 * Created by PhpStorm.
 * @author tangjun <tangjun@misrobot.com>
 * @date 2016-01-04 11:42
 * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
 */

namespace Modules\Msc\Entities;
use Illuminate\Database\Eloquent\Model;

class OpenPlan  extends  Model
{
    protected $connection	=	'msc_mis';
    protected $table 		= 	'open_plan';
    public $timestamps	=	true;
    protected $primaryKey	=	'id';
    public $incrementing	=	true;
    protected $fillable 	=	['name','level','year','month','week','day','begintime','endtime','status','created_user_id','lab_id'];

    /**
     * @param $DateTime
     * @return array
     * @author tangjun <tangjun@misrobot.com>
     * @date    2016年1月5日10:10:04
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function GetOpenPlanId($DateTime){
        $y='';$m='';$d='';
        if(!empty($DateTime)){
            $timeInt = strtotime($DateTime);
            $y = date('Y',$timeInt);
            $m = date('m',$timeInt);
            $d = date('d',$timeInt);
        }
        $LabIdRrr = $this->where('year','=',$y)->where('month','=',$m)->where('day','=',$d)->select('lab_id')->get();
        $IdRrr = [] ;
        //TODO 把所有实验室ID 存入一个数组，并且去除重复
        if(!empty($LabIdRrr) && is_array($LabIdRrr->toArray())){
            foreach($LabIdRrr as $v){
                if(!in_array($v['lab_id'],$IdRrr)){
                    $IdRrr[] = $v['lab_id'];
                }
            }
        }
        return $IdRrr;
    }
}