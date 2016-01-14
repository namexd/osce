<?php
/**
 * Created by PhpStorm.
 * User: j5110
 * Date: 2016/1/14
 * Time: 11:04
 */

namespace Modules\Osce\Entities;


class ExamFlowStation extends CommonModel
{
    protected $connection = 'osce_mis';
    protected $table = 'exam_flow_station';
    public $timestamps = true;
    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $guarded = [];
    protected $hidden = [];
    protected $fillable = ['serialnumber', 'station_id', 'flow_id', 'created_user_id'];
}