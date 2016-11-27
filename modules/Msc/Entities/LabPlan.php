<?php
/**
 * Created by PhpStorm.
 * @author tangjun <tangjun@163.com>
 * @date 2016-01-07 11:44
 * @copyright 2013-2015 MIS 163.com Inc. All Rights Reserved
 */

namespace Modules\Msc\Entities;
use Illuminate\Database\Eloquent\Model;


class LabPlan  extends Model
{
    protected $connection	=	'msc_mis';
    protected $table 		= 	'lab_plan';
    public $timestamps	=	true;
    protected $primaryKey	=	'id';
    protected $fillable 	=	['begin_endtime', 'user_id','type','lab_id','course_name','plan_time','lab_apply_id'];


}