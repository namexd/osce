<?php
/**
 * Created by PhpStorm.
 * User: wangjiang
 * Date: 2015/12/1 0001
 * Time: 10:09
 */

namespace Modules\Msc\Entities;

use Illuminate\Database\Eloquent\Model;

class StdGroup extends Model
{
    protected $connection	=	'msc_mis';
    protected $table 		= 	'student_group';
	public $timestamps	    =	false;
    protected $fillable 	=	['id', 'group_id', 'student_id'];
}