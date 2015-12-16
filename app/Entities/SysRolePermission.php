<?php
/**
 * Created by PhpStorm.
 * User: tangjun <tangjun@misrobot.com>
 * Date: 2015年12月15日
 * Time: 11:18:06
 */

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;


class SysRolePermission extends Model
{
    protected $connection	=	'sys_mis';
    protected $table 		= 	'sys_role_permission';




}