<?php
/**
 * Created by PhpStorm.
 * User: j5110
 * Date: 2016/1/26
 * Time: 17:48
 */

namespace Modules\Osce\Entities;

use DB;
class ExamStation extends CommonModel
{
    protected $connection	=	'osce_mis';
    protected $table 		= 	'exam_station';
    public $incrementing	=	true;
    public $timestamps	    =	true;
    protected $fillable 	=	['exam_id','station_id','create_user_id'];

    public function station(){
        return $this->hasMany('Modules\Osce\Entities\Station','id','station_id');
    }

}