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
    protected $connection = 'osce_mis';
    protected $table = 'standard';
    public $timestamps = true;
    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $guarded = [];
    protected $hidden = [];
    protected $fillable = ['subject_id', 'content', 'sort', 'score', 'created_user_id','pid','level','answer'];
    public $search = [];

    //创建人用户关联
    public function user(){
        return $this->hasOne('App\Entities\User','created_user_id','id');
    }

    //父级考核点
    public function parent(){
        return $this->hasOne('Modules\Osce\Entities\Standard','id','pid');
    }

    public function childrens(){
        return $this->hasMany('Modules\Osce\Entities\Standard','pid','id');
    }

    public function ItmeList($subjectId){
        $prointList =   $this->where('subject_id','=',$subjectId)->get();
        $data       =   [];
        foreach($prointList as $item)
        {
            $data[$item->pid][] =   $item;
        }
        $return =   [];
        foreach($data[0] as $proint)
        {
            $prointData['test_point'] =   $proint;
            if(array_key_exists($proint->id,$data))
            {
                $prointData['test_term']    =   $data[$proint->id];
            }
            else
            {
                $prointData['options']    =   [];
            }
            $return[]=$prointData;
        }
        return $return;
    }



}