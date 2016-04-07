<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/4/7 0007
 * Time: 15:36
 */

namespace Modules\Osce\Entities;


class ExamDraft extends CommonModel
{
    protected $connection   = 'osce_mis';
    protected $table        = 'exam_draft';
    public    $timestamps   = true;
    protected $primaryKey   = 'id';
    public    $incrementing = true;
    protected $guarded      = [];
    protected $hidden       = [];
    protected $fillable     = ['station_id', 'room_id', 'subject_id',  'exam_draft_flow_id', 'effected'];



}