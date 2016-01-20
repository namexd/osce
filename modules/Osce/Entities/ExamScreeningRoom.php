<?php
/**
 * Created by PhpStorm.
 * User: j5110
 * Date: 2016/1/8
 * Time: 9:55
 */

namespace modules\Osce\Entities;


class ExamScreeningRoom
{
    protected $connection = 'osce_mis';
    protected $table = 'exam_screening_room';
    public $timestamps = true;
    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $guarded = [];
    protected $hidden = [];
    protected $fillable = ['exam_id', 'room_id', 'begin_dt', 'end_dt', 'create_user_id', 'status', 'sort', 'total', 'nfc_tag'];
}