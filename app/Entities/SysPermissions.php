<?php
/**
 * Created by PhpStorm.
 * User: tangjun <tangjun@misrobot.com>
 * Date: 2015年12月15日
 * Time: 11:18:06
 */

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;


class SysPermissions extends Model
{
    protected $connection	=	'sys_mis';
    protected $table 		= 	'sys_permissions';

    protected $fillable 	=	['moduleid', 'type', 'itemid','name','description'];
    public function getPermissionsAll(){
        $thisBuilder = $this;
        return  $thisBuilder->get();
    }

    public function SysMenus(){

    }

    //添加基础权限信息
    public function AddPermissions($data){

    }


}