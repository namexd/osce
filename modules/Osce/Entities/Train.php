<?php
/**
 * Created by PhpStorm.
 * User: zhouchong
 * Date: 2016/1/11 0011
 * Time: 10:03
 */
namespace Modules\Osce\Entities;
class Train extends CommonModel{
    protected $connection	=	'osce_mis';
    protected $table 		= 	'train';
    public $incrementing	=	true;
    public $timestamps	    =	true;
    protected   $fillable 	=	[ 'name', 'code','status','create_user_id'];
    public      $search     =   [];
}