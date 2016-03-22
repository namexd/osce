<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/3/22 0022
 * Time: 15:44
 */

namespace Modules\Osce\Entities;


class ExamPaperStation extends CommonModel
{
    protected $connection   = 'osce_mis';
    protected $table        = 'exam_paper_exam_station';
    public    $timestamps   = true;
    protected $primaryKey   = 'id';
    public    $incrementing = true;
    protected $guarded      = [];
    protected $hidden       = [];
    protected $fillable     = ['exam_id', 'exam_paper_id', 'station_id'];


}