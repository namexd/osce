<?php
/**
 * Created by PhpStorm.
 * User: wangjiang
 * Date: 2015/12/15 0015
 * Time: 13:55
 */

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;

class SysRoles extends Model
{
    public $timestamps	    =	true;
    public $incrementing	=	true;

    protected $connection	=	'sys_mis';
    protected $table 		= 	'sys_roles';
    protected $primaryKey	=	'id';
    protected $fillable 	=	['name', 'slug', 'description'];
}