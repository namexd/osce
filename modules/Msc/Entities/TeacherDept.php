<?php
/**
 * Created by PhpStorm.
 * 科室模型
 * User: fengyell <Luohaihua@misrobot.com>
 * Date: 2015/11/6
 * Time: 12:39
 */

namespace Modules\Msc\Entities;

use Illuminate\Database\Eloquent\Model;

class TeacherDept extends Model
{
    protected $connection	=	'msc_mis';
    protected $table 		= 	'teacher_dept';
    protected $fillable 	=	["id","name","code"];
}