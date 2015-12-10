<?php
/**
 * Created by PhpStorm.
 * User: wangjiang
 * Date: 2015/12/1 0001
 * Time: 9:45
 */

namespace Modules\Msc\Entities;

use Illuminate\Database\Eloquent\Model;

class TrainingGroup extends Model
{
    protected $connection	=	'msc_mis';
    protected $table 		= 	'training_group';
	public $timestamps	    =	false;
    protected $fillable 	=	['training_id', 'group_id'];
}