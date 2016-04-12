<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/1/13 0013
 * Time: 20:54
 */

namespace Modules\Osce\Entities;


class Standard extends CommonModel
{
    protected $connection   = 'osce_mis';
    protected $table        = 'standard';
    public    $timestamps   = false;
    protected $primaryKey   = 'id';
    public    $incrementing = true;
    protected $guarded      = [];
    protected $hidden       = [];
    protected $fillable     = ['title'];
    public    $search       = [];


    public function user(){
        return $this->hasOne('App\Entities\User','created_user_id','id');
    }

    //获取考核项
    public function parent(){
        return $this->hasOne('Modules\Osce\Entities\Standard','id','pid');
    }

    public function childrens(){
        return $this->hasMany('Modules\Osce\Entities\Standard','pid','id');
    }

    public function standardItem(){
        return $this->hasMany('Modules\Osce\Entities\StandardItem','standard_id','id');
    }

    /**
     * 创建考核点、考核项
     * @param $point
     * @version 3.4
     * @author Zhoufuxiang <Zhoufuxiang@misrobot.com>
     * @date 2016-04-12 12:55
     * @return static
     */
    public function addStandard($standard_name){
        $result = $this->where('title','=',$standard_name)->first();
        if (is_null($result)){
            $data = ['title'=>$standard_name];
            $result = $this->create($data);
        }
        return $result;
    }


}