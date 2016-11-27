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
    protected $table = 'room_vcr';
    public $timestamps = true;
    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $guarded = [];
    protected $hidden = [];
    protected $fillable = ['id','room_id', 'vcr_id', 'created_user_id'];

    public function getVcr(){
        return $this->hasOne('\Modules\Osce\Entities\Vcr','id','vcr_id');
    }
}