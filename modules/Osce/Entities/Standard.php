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


    //¸¸¼¶¿¼ºËµã
    public function parent(){
        return $this->hasOne('Modules\Osce\Entities\Subject','id','pid');
    }


    public function ItmeList(){


    }



}