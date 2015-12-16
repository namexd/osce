<?php
/**
 * Created by PhpStorm.
 * User: tangjun <tangjun@misrobot.com>
 * Date: 2015年12月15日
 * Time: 11:18:06
 */

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;


class SysElement extends Model
{
    protected $connection	=	'sys_mis';
    protected $table 		= 	'sys_element';


    public function getElementList($pid=0){
        return  $this->where('pid','=',$pid)->get();
    }




}