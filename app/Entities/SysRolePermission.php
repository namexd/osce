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


    //定义和权限表的关系
    public function SysPermissions()
    {
        return $this->hasMany('App\Entities\SysPermissions','id','permission_id');
    }
    /**
     * 根据角色id获取 相关权限信息
     *
     * @version 0.8
     * @author tangjun <tangjun@misrobot.com>
     * @date 2015年12月15日13:59:39
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function getPermissionList($data){
        $thisBuilder = $this;
        if(!empty($data['roleId'])){
            $thisBuilder = $thisBuilder->where('role_id','=',$data['roleId']);
        }
        return $thisBuilder->with('SysPermissions')->first();
    }

}