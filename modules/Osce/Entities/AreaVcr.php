<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/1/20 0020
 * Time: 14:53
 */

namespace Modules\Osce\Entities;


class AreaVcr extends CommonModel
{
    protected $connection = 'osce_mis';
    protected $table = 'area_vcr';
    public $timestamps = true;
    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $guarded = [];
    protected $hidden = [];
    protected $fillable = ['area_id', 'vcr_id', 'created_user_id'];

}