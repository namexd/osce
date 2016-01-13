<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/1/8 0008
 * Time: 17:36
 */

namespace Modules\Osce\Entities;


use Pingpong\Presenters\Model;

class StationSp extends Model
{
    protected $connection = 'osce_mis';
    protected $table = 'station_sp';
    public $timestamps = true;
    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $guarded = [];
    protected $hidden = [];
    protected $fillable = ['station_id', 'user_id', 'case_id', 'end_dt', 'created_user_id'];
}