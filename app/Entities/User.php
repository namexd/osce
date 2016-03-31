<?php

namespace App\Entities;

use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Foundation\Auth\Access\Authorizable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;

class User extends Model implements AuthenticatableContract,
    AuthorizableContract,
    CanResetPasswordContract
{
    use Authenticatable, Authorizable, CanResetPassword;

    public  $incrementing	=	true;
    public  $timestamps	=	true;
    protected $connection	=	'sys_mis';
    protected $table 		= 	'users';
    protected $primaryKey	=	'id';
    protected $guarded 		= 	[];
//    protected $hidden 		= 	['password','openid'];
    protected $fillable 	=	['username','name','code','password','openid','mobile','nickname','gender','qq','weixinnickname','country','province','city','adress','avatar','email','lastlogindate','idcard_type','idcard','status',]  ;

    // 用户性别获取器
    public function getGenderAttribute ($value)
    {
        switch ($value) {
            case 1:
                $gender = '男';
                break;

            case 2:
                $gender = '女';
                break;

            case 0:
                $gender = '未知';
                break;

            default:
                $gender = '-';
        }

        return $gender;
    }

    // 用户状态获取器
    public function getStatusAttribute ($value)
    {
        switch ($value) {
            case 1:
                $status = '正常';
                break;

            case 2:
                $status = '禁用';
                break;

            case 3:
                $status = '删除';
                break;

            default:
                $status = '-';
        }

        return $status;
    }

    // 用户相关角色
    public function roles()
    {
        return $this->belongsToMany('App\Entities\SysRoles', 'sys_user_role', 'user_id', 'role_id');
    }

    //获取当前登录用户的角色
    public function getUserRoleName($username){
        $builder = $this->where('users.username','=',$username)->leftjoin('sys_user_role',function($join){
            $join->on('sys_user_role.user_id','=','users.id');
        })->leftjoin('sys_roles',function($join){
            $join->on('sys_roles.id','=','sys_user_role.role_id');
        })->select('sys_roles.name')->first();

        return $builder;
    }


}
