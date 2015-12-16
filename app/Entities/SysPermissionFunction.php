<?php
/**
 * Created by PhpStorm.
 * User: tangjun <tangjun@misrobot.com>
 * Date: 2015年12月15日
 * Time: 11:18:06
 */

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;


class SysPermissionFunction extends Model
{
    protected $connection	=	'sys_mis';
    protected $table 		= 	'sys_permission_function';


    public function getPermissionFunctionsList($PermissionFunctionsArr){
        $thisBuilder = $this;
        if(!empty($PermissionMenuArr) && is_array($PermissionMenuArr)){
            $thisBuilder = $thisBuilder->whereIn('permission_id',$PermissionMenuArr);
        }
        return  $thisBuilder->with('SysFunctions')->first();
    }

    public function SysFunctions(){
        return $this->hasMany('App\Entities\SysFunctions','id','function_id');
    }


}