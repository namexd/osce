<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/4/6 0006
 * Time: 15:30
 */
namespace Modules\Osce\Entities;

class ExamDraftFlowTemp extends CommonModel{
    protected $connection = 'osce_mis';
    protected $table = 'exam_draft_flow_temp';
    public $timestamps = true;
    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $guarded = [];
    protected $hidden = [];
    protected $fillable = ['id','exam_id', 'exam_screening_id', 'exam_gradation_id',  'name',  'order', 'created_at','ctrl_type','exam_draft_flow_id'];
    public $search = [];
}