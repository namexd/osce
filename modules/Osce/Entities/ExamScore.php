<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/1/14 0014
 * Time: 14:41
 */

namespace Modules\Osce\Entities;


class ExamScore extends  CommonModel
{

    protected $connection = 'osce_mis';
    protected $table = 'exam_score';
    public $timestamps = true;
    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $guarded = [];
    protected $hidden = [];
    protected $fillable = ['exam_result_id', 'subject_id', 'standard_id', 'score', 'evaluate','create_user_id'];

}