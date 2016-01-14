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
    protected $table 		= 	'bbs_topic';
    public $incrementing	=	true;
    public $timestamps	    =	true;
    protected   $fillable 	=	['title','content','pid','create_user_id'];
    public      $search    =   [];

    public function getAuthor(){
        return $this->belongsTo('App\Entities\User','create_user_id','id');
    }

    public function getDiscussionPagination(){
        return $this->where('pid',0)->paginate(config('msc.page_size'));
    }
}