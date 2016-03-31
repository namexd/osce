<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/3/31 0031
 * Time: 17:34
 */

namespace Modules\Osce\Entities;


class Supplies extends CommonModel
{
    protected $connection   = 'osce_mis';
    protected $table        = 'supplies';
    public    $timestamps   = true;
    protected $primaryKey   = 'id';
    public    $incrementing = true;
    protected $guarded      = [];
    protected $hidden       = [];
    protected $fillable     = ['name', 'created_user_id', 'archived'];
    

}