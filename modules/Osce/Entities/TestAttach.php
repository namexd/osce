<?php
/**
 * Created by PhpStorm.
 * User: j5110
 * Date: 2016/1/16
 * Time: 14:07
 */

namespace Modules\Osce\Entities;


class TestAttach extends CommonModel
{
    protected $connection = 'osce_mis';
    protected $table = 'exam_attach';
    public $timestamps = true;
    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $guarded = [];
    protected $hidden = [];
    protected $fillable = ['test_result_id', 'url', 'type', 'name', 'description','standard_id','student_id'];

}