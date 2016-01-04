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

}