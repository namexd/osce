<?php
/**
 * Created by PhpStorm.
 * User: fengyell <Luohaihua@misrobot.com>
 * Date: 2015年12月15日
 * Time: 11:18:06
 */

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;


class SysRoles extends Model
{
    protected $connection	=	'sys_mis';
    protected $table 		= 	'sys_roles';

    protected $fillable 	=	["name","slug","description"];
   	public function __construct(){
	}

	/**
	 * 获取角色列表
	 *时间2015年12月15日11:23:06、
	 *whg
	 */
	public function getRolesList(){
		$roleslist=$this->paginate(config('msc.page_size',10));
		return $roleslist;
	}


}