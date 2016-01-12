<?php
/**
 * 实验室预约和日历的关联模型
 * Created by PhpStorm.
 * @author tangjun <tangjun@misrobot.com>
 * @date 2016-01-06 10:05
 * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
 */

namespace Modules\Msc\Entities;

use Illuminate\Database\Eloquent\Model;

class PlanApply extends Model
{
    protected $connection	=	'msc_mis';
    protected $table 		= 	'plan_apply';
    public $timestamps	=	true;
    protected $primaryKey	=	'id';
    protected $fillable 	=	['lab_id', 'apply_id','open_plan_id'];
}