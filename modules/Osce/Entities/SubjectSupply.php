<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/3/31 0031
 * Time: 17:15
 */

namespace Modules\Osce\Entities;


class SubjectSupply extends CommonModel
{
    protected $connection   = 'osce_mis';
    protected $table        = 'subject_supply';
    public    $timestamps   = true;
    protected $primaryKey   = 'id';
    public    $incrementing = true;
    protected $guarded      = [];
    protected $hidden       = [];
    protected $fillable     = ['subject_id', 'supply_id', 'num', 'unit', 'created_user_id'];


    public function subject(){
        return $this->hasOne('\Modules\Osce\Entities\Subject', 'id', 'subject_id');
    }

   public function supply(){
       return $this->hasOne('\Modules\Osce\Entities\supply', 'id', 'supply_id');
   }

}