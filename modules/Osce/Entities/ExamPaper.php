<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/3/22 0022
 * Time: 10:59
 */

namespace Modules\Osce\Entities;


class ExamPaper extends CommonModel
{
    protected $connection   = 'osce_mis';
    protected $table        = 'exam_paper';
    public    $timestamps   = true;
    protected $primaryKey   = 'id';
    public    $incrementing = true;
    protected $guarded      = [];
    protected $hidden       = [];
    protected $fillable     = ['name', 'status', 'mode', 'type', 'length', 'created_user_id'];



}