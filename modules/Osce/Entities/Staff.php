<?php
/**
 * Created by PhpStorm.
 * User: fengyell <Luohaihua@misrobot.com>
 * Date: 2015/12/31
 * Time: 15:10
 */

namespace Modules\Osce\Entities;


class Staff extends CommonModel
{
    protected $connection = 'osce_mis';
    protected $table = 'staff';
    public $timestamps = true;
    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $guarded = [];
    protected $hidden = [];
    protected $fillable = ['name', 'idcard'];
    public $search = [];
    function asdf(){
        return $this->where();
    }
    public function user(){
        return $this->hasOne(' Modules\Osce\Entities\user','uid','id');
    }

}