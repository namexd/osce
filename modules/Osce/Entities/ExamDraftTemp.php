<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/4/6 0006
 * Time: 16:31
 */
namespace Modules\Osce\Entities;
class ExamDraftTemp extends CommonModel{
    protected $connection = 'osce_mis';
    protected $table = 'exam_draft_temp';
    public $timestamps = true;
    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $guarded = [];
    protected $hidden = [];
    protected $fillable = ['id','exam_id', 'room_id', 'subject_id',  'ctrl_type',  'old_draft_flow_id', 'station_id','old_draft_id','used','user_id'];
    public $search = [];
}