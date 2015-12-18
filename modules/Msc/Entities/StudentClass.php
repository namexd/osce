<?php
/**
 * Created by PhpStorm.
 * User: wangjiang
 * Date: 2015/11/26 0026
 * Time: 11:34
 */

namespace Modules\Msc\Entities;

use Illuminate\Database\Eloquent\Model;

class StudentClass extends Model
{
    protected $connection	=	'msc_mis';
    protected $table 		= 	'student_class';
    public $incrementing	=	true;
    public $timestamps	=	false;
    protected $fillable 	=	['name', 'id', 'code'];


}