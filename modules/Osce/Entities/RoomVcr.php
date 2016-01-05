<?php
/**
 * Created by PhpStorm.
 * User: j5110
 * Date: 2016/1/4
 * Time: 16:36
 */

namespace Modules\Osce\Entities;


class RoomVcr extends CommonModel
{
    protected $connection = 'osce_mis';
    protected $table = 'station_case';
    public $timestamps = true;
    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $guarded = [];
    protected $hidden = [];
    protected $fillable = ['room_id', 'vcr_id', 'create_user_id'];
}