<?php
/**
 * Created by PhpStorm.
 * User: zhouchong
 * Date: 2016/1/11 0011
 * Time: 10:03
 */
namespace Modules\Osce\Entities;
class InformTrain extends CommonModel{
    protected $connection	=	'osce_mis';
    protected $table 		= 	'inform_training';
    public $incrementing	=	true;
    public $timestamps	    =	true;
    protected   $fillable 	=	[ 'name', 'address','begin_dt','end_dt','teacher','content','attachments','status','create_user_id'];
    public      $search     =   [];

    public function getPaginate(){
        return $this->paginate(config('msc.page_size'));
    }
}