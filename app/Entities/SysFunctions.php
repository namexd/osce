<?php
/**
 * Created by PhpStorm.
 * User: tangjun <tangjun@misrobot.com>
 * Date: 2015年12月15日
 * Time: 11:18:06
 */

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;


class SysFunctions extends Model
{
    protected $connection	=	'sys_mis';
    protected $table 		= 	'sys_functions';


    public function getFunctionsList(){
        return  $thisBuilder = $this->with('SysPermissionFunction')->get();
    }

    public function SysPermissionFunction(){
        return  $this->hasOne('App\Entities\SysPermissionFunction','function_id','id');
    }




}