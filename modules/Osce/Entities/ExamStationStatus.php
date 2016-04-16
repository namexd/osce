<?php
/**
 * Created by PhpStorm.
 * User: j5110
 * Date: 2016/4/14
 * Time: 11:24
 */

namespace Modules\Osce\Entities;


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