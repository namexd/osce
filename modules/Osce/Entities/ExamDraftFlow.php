<?php
/**
 * Created by PhpStorm.
 * User: j5110
 * Date: 2016/4/7
 * Time: 17:29
 */

namespace Modules\Osce\Entities;


class ExamDraftFlow extends CommonModel
{
    protected $connection = 'osce_mis';
    protected $table = 'exam_draft_flow';
    public $timestamps = true;
    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $guarded = [];
    protected $hidden = [];
    protected $fillable = ['id','exam_id', 'exam_screening_id', 'exam_gradation_id',  'name',  'order'];
    public $search = [];
}