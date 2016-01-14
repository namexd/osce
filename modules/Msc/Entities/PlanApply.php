<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/1/4 0004
 * Time: 11:52
 */

namespace Modules\Msc\Entities;




use Illuminate\Database\Eloquent\Model;

class PlanApply extends Model
{
    public $timestamps	=	true;
    public $incrementing	=	true;
    protected $connection	=	'msc_mis';
    protected $table 		= 	'plan_apply';
    protected $primaryKey	=	'id';
    protected $guarded 		= 	[];
    protected $hidden 		= 	[];
    protected $fillable 	=	['id','apply_id','open_plan_id'];

    /**
     * @content
     * @author
     * @createDate
     */
    public function OpenPlan(){
        return  $this->hasOne('Modules\Msc\Entities\OpenPlan','id','open_plan_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     * @author tangjun <tangjun@misrobot.com>
     * @date    2016年1月13日11:29:19
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function LabApply(){
        return  $this->hasOne('Modules\Msc\Entities\LabApply','id','apply_id');
    }

}