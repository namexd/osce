<?php
/**
 * Created by PhpStorm.
 * User: j5110
 * Date: 2016/2/15
 * Time: 17:00
 */

namespace Modules\Osce\Entities;


class ExamPlanRecord extends CommonModel
{
    protected $connection = 'osce_mis';
    protected $table = 'exam_queue';
    public $timestamps = true;
    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $guarded = [];
    protected $hidden = [];
    protected $fillable = ['room_id','student_id','station_id','exam_id','exam_screening_id','end_dt'];


}