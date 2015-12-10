<?php
/**
 * Created by PhpStorm.
 * User: wangjiang
 * Date: 2015/12/3 0003
 * Time: 15:08
 */

namespace Modules\Msc\Entities;

use Illuminate\Database\Eloquent\Model;

class ResourcesLabDevices extends Model
{
    protected $connection	=	'msc_mis';
    protected $table 		= 	'resources_device';
    protected $fillable 	=	['id', 'name', 'code'];
}