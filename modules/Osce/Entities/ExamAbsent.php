<?php
/**
 * Created by PhpStorm.
 * User: zhouchong
 * Date: 2016/1/22 0022
 * Time: 15:41
 */
namespace Modules\Osce\Entities;

class ExamAbsent extends  CommonModel{
    protected $connection = 'osce_mis';
    protected $table = 'exam_absent';
    public $timestamps = true;
    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $guarded = [];
    protected $hidden = [];
    protected $fillable = ['exam_id', 'exam_screening_id', 'student_id',  'begin_dt',  'status', 'created_user_id'];
    public $search = [];

}