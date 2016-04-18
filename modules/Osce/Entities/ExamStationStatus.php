<?php
/**
 * Created by PhpStorm.
<<<<<<< HEAD
 * User: j5110
 * Date: 2016/4/14
 * Time: 11:24
=======
 * User: wangjiang
 * Date: 2016/4/6 0006
 * Time: 18:14
>>>>>>> c61231fdd209f9d0eedce3341c26db5fcded099f
 */

namespace Modules\Osce\Entities;

use Modules\Msc\Entities\Teacher;
use Modules\Osce\Entities\CommonModel;
use Auth;
use DB;

class ExamStationStatus extends CommonModel
{
    protected $connection = 'osce_mis';
    protected $table = 'exam_station_status';
    public $incrementing = true;
    public $timestamps = true;
    protected $fillable = [
        'exam_id',
        'exam_screening_id',
        'station_id',
        'status'
    ];
}