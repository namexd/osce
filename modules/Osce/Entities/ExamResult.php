<?php
/**
 * Created by PhpStorm.
 * User: j5110
 * Date: 2016/1/26
 * Time: 19:22
 */

namespace Modules\Osce\Entities;


class ExamResult extends CommonModel
{
    protected $connection = 'osce_mis';
    protected $table = 'exam_result';
    public $timestamps = true;
    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $guarded = [];
    protected $hidden = [];
    protected $fillable = ['student_id', 'exam_screening_id', 'station_id', 'end_dt', 'begin_dt', 'time',
        'create_user_id', 'score', 'score_dt', 'teacher_id'];
}