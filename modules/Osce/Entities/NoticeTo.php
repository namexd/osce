<?php
/**
 * 通知收件人关联模型
 * Created by PhpStorm.
 * User: fengyell <Luohaihua@misrobot.com>
 * Date: 2016/1/6
 * Time: 14:17
 */

namespace Modules\Osce\Entities;

use Modules\Osce\Entities\CommonModel;


class NoticeTo extends CommonModel
{
    protected $connection	=	'osce_mis';
    protected $table 		= 	'notice';
    public $incrementing	=	true;
    public $timestamps	    =	true;
    public $fillable    =   ['notice_id','uid'];
}