<?php
/**
 * Created by PhpStorm.
 * User: wangjiang
 * Date: 2015/12/3 0003
 * Time: 15:04
 */

namespace Modules\Msc\Entities;

use Illuminate\Database\Eloquent\Model;

class ResourcesOpenLabAppGroup extends Model
{
    protected $connection	=	'msc_mis';
    protected $table 		= 	'resources_openlab_apply_group';
    protected $fillable 	=	['resources_openlab_apply_id', 'student_class_id', 'student_group_id'];
    public $timestamps	=	false;
    public $incrementing	=	true;
}