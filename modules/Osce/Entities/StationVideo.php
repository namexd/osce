<?php
/**
 * Created by PhpStorm.
 * User: j5110
 * Date: 2016/1/27
 * Time: 16:34
 */

namespace Modules\Osce\Entities;


class StationVideo extends CommonModel
{
    protected $connection = 'osce_mis';
    protected $table = 'station_video';
    public $timestamps = true;
    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $guarded = [];
    protected $hidden = [];
    protected $fillable = ['exam_id', 'station_vcr_id', 'begin_dt', 'end_dt', 'created_user_id', 'student_id'];
}