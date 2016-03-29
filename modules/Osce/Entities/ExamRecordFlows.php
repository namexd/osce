<?php
/**
 * Created by PhpStorm.
 * User: j5110
 * Date: 2016/3/22
 * Time: 19:00
 */

namespace Modules\Osce\Entities;

class ExamRecordFlows extends CommonModel
{
    protected $connection = 'osce_mis';
    protected $table = 'exam_record_flows';
    public $timestamps = true;
    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $guarded = [];
    protected $hidden = [];
    protected $fillable = [
        'exam_queue_id',
        'before_status',
        'before_begin_dt',
        'before_end_dt',
        'after_status',
        'after_begin_dt',
        'after_end_dt',
        'before_room_id',
        'before_station_id',
        'after_room_id',
        'after_station_id',
        'ctrl_desc',
        'created_user_id'
    ];


}