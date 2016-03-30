<?php
/**
 * Created by PhpStorm.
 * User: j5110
 * Date: 2016/1/4
 * Time: 16:31
 */

namespace Modules\Osce\Entities;


class StationCase extends CommonModel
{
    protected $connection = 'osce_mis';
    protected $table = 'station_case';
    public $timestamps = true;
    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $guarded = [];
    protected $hidden = [];
    protected $fillable = ['case_id', 'station_id', 'create_user_id'];

    public function station(){
        return $this->belongsTo('\Modules\Osce\Entities\Station','station_id','id');
    }
}