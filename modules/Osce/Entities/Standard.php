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
        $prointList =   $this->where('subject_id','=',$subjectId)->where('pid','=',0)->get();
        $data       =   [];
        foreach($prointList as $proint)
        {
            $data[] =   $proint;
            foreach($proint->childrens as $option)
            {
                $data[]=$option;
            }
        }

//        dd($data);
//        dd($data[0]->childrens[0]['pid']);
        return $data;
    }



}