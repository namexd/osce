<?php
/**
 * 通知收件人关联模型
 * Created by PhpStorm.
 * User: fengyell <Zouyuchao@sulida.com>
 * Date: 2016/1/6
 * Time: 14:17
 */

namespace Modules\Osce\Entities;

use Modules\Osce\Entities\CommonModel;


class NoticeTo extends CommonModel
{
    protected $connection	=	'osce_mis';
    protected $table 		= 	'notice_to';
    public $incrementing	=	true;
    public $timestamps	    =	true;
    public $fillable    =   ['notice_id','uid'];

    public function addNoticeTo(array $list){
        return  $this   ->  insert($list);
    }
}