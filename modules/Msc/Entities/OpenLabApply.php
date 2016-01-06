<?php
/**
 * Created by PhpStorm.
 * @author tangjun <tangjun@misrobot.com>
 * @date 2016-01-06 10:05
 * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
 */

namespace Modules\Msc\Entities;

use Illuminate\Database\Eloquent\Model;

class OpenLabApply extends Model
{
    protected $connection	=	'msc_mis';
    protected $table 		= 	'open_lab_apply';
    public $timestamps	=	true;
    protected $primaryKey	=	'id';
    protected $fillable 	=	['lab_id', 'type','lab_plan_id','status','created_user_id'];
}