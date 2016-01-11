<?php
/**
 * Created by PhpStorm.
 * User: zhouchong
 * Date: 2016/1/9 0009
 * Time: 15:20
 */
namespace Modules\Osce\Entities;

use Modules\Msc\Entities\CommonModel;

class Discussion extends CommonModel{
    protected $connection	=	'osce_mis';
    protected $table 		= 	'discussion';
    public $incrementing	=	true;
    public $timestamps	    =	true;
    protected   $fillable 	=	[];
    public      $search    =   [];

    public function getList(){
        return $this->hasMany('Modules\Osce\Entities','discussion_id','id');
    }
    public function getAuthor(){
        return $this->hasMany('Modules\Msc\Entities\Users','id','create_user_id');
    }

    public function getDiscussionPagination(){
        return $this->paginate(config('msc.page_size'));
    }
}