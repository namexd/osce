<?php
/**
 * Created by PhpStorm.
 * User: wangjiang
 * Date: 2016/4/6 0006
 * Time: 18:14
 */

namespace Modules\Osce\Entities;

use Modules\Msc\Entities\Teacher;
use Modules\Osce\Entities\CommonModel;
use Auth;
use DB;

class ExamStationStatus extends CommonModel
{
    protected $connection	=	'osce_mis';
    protected $table 		= 	'exam_station_status';
    public $incrementing	=	true;
    public $timestamps	    =	true;
    protected $fillable 	=   ['exam_id', 'station_id', 'status'];


}