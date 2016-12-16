<?php
/**
 * 监考老师模型
 * Created by PhpStorm.
 * User: fengyell <Zouyuchao@sulida.com>
 * Date: 2015/12/29
 * Time: 10:46
 */

namespace Modules\Osce\Entities;


class Machine extends CommonModel
{
    protected $connection	=	'osce_mis';
    protected $table 		= 	'machine';
    public $incrementing	=	true;
    public $timestamps	    =	true;
    protected $fillable 	=	['name'];
	
	
}