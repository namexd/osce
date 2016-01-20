<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/1/12 0012
 * Time: 14:52
 */

namespace Modules\Osce\Entities;


class StationTeacher extends CommonModel
{
    protected $connection = 'osce_mis';
    protected $table = 'station_teacher';
    public $timestamps = true;
    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $guarded = [];
    protected $hidden = [];
    protected $fillable = ['station_id', 'user_id', 'case_id', 'created_user_id', 'type', 'exam_id'];

    public function station()
    {
        return $this->belongsTo('\Modules\Osce\Entities\Station','station_id','id');
    }
}