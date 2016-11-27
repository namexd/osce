<?php
/**
 * Created by PhpStorm.
 * 班级模型
 * User: fengyell <Luohaihua@163.com>
 * Date: 2015/11/6
 * Time: 11:02
 */

namespace Modules\Msc\Entities;

use Illuminate\Database\Eloquent\Model;

class StdClass extends Model
{
    protected $connection	=	'msc_mis';
    protected $table 		= 	'student_class';
    protected $fillable 	=	["id","name","code"];
}