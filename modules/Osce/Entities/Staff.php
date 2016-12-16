<?php
/**
 * Created by PhpStorm.
 * User: fengyell <Zouyuchao@sulida.com>
 * Date: 2015/12/31
 * Time: 15:10
 */

namespace Modules\Osce\Entities;


class Staff extends CommonModel
{
    protected $connection = 'osce_mis';
    protected $table = 'teacher';
    public $timestamps = true;
    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $guarded = [];
    protected $hidden = [];
    protected $fillable = ['id', 'code','name','validated'];
    public $search = [];


    public function user(){
        return $this->hasOne('App\Entities\User','id','id');
    }

    public function roles(){
        $this->hasMany();
    }
    /**
     * 获取用户列表
     * @access public
     *
     * @return view
     *
     * @version 1.0
     * @author Zouyuchao <Zouyuchao@sulida.com>
     * @date 2016-01-03
     * @copyright  2013-2017 sulida.com  Inc. All Rights Reserved
     *
     */
    public function getList(){

        return $this    ->  paginate(config('osce.page_size'));
    }
}