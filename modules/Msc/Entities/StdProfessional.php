<?php
/**
 * Created by PhpStorm.
 * 专业模型
 * User: fengyell <Luohaihua@misrobot.com>
 * Date: 2015/11/6
 * Time: 11:02
 */

namespace Modules\Msc\Entities;

use Illuminate\Database\Eloquent\Model;

class StdProfessional extends Model
{
    protected $connection	=	'msc_mis';
    protected $table 		= 	'student_professional';
    protected $fillable 	=	["id","name","code"];
}