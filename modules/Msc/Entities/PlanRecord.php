<?php
/**
 * Created by PhpStorm.
 * @author tangjun <tangjun@misrobot.com>
 * @date 2016-01-06 10:26
 * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
 */

namespace Modules\Msc\Entities;
use Illuminate\Database\Eloquent\Model;

class PlanRecord extends Model
{
    protected $connection	=	'msc_mis';
    protected $table 		= 	'plan_record';
    public $timestamps	=	true;
    protected $primaryKey	=	'id';
    public $incrementing	=	true;
    protected $fillable 	=	['name','begintime','endtime','status','created_user_id','type','open_plan_id','persons','lab_id','course_id','open_apply_id','plan_time','lab_apply_id','plan_id','video_id','video_starttime','video_endtime'];

}