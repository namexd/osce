<?php
/**
 * Created by PhpStorm.
 * User: tangjun <tangjun@misrobot.com>
 * Date: 2015年12月15日
 * Time: 11:18:06
 */

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;


class SysPermissionMenu extends Model
{
    protected $connection	=	'sys_mis';
    protected $table 		= 	'sys_permission_menu';
    protected $fillable 	=	['permission_id', 'menu_id'];


    public function getPermissionMenuList($PermissionMenuArr){
        $thisBuilder = $this;
        if(!empty($PermissionMenuArr) && is_array($PermissionMenuArr)){
            $thisBuilder = $thisBuilder->whereIn('permission_id',$PermissionMenuArr);
        }
        return  $thisBuilder->with('SysMenus')->get();
    }

    public function SysMenus(){
        return  $this->hasMany('App\Entities\SysMenus','id','menu_id');
    }




}