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

    protected $fillable 	=	['permission_id', 'role_id'];

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
        return $thisBuilder->select('permission_id')->get();
    }

    public function DelRolePermission($role_id){

        return  $this->where('role_id','=',$role_id)->delete();
    }

    public function AddRolePermission($permissionIdArr,$role_id){

        $return = true;
        if(!empty($permissionIdArr) && is_array($permissionIdArr)){
            foreach($permissionIdArr as $v){
                $data = [
                    'permission_id' => $v,
                    'role_id' => $role_id
                ];
                $rew = $this->forceCreate($data);
                if(empty($rew)){
                    $return = false;
                    break;
                }

            }
        }
        return  $return;

    }

}