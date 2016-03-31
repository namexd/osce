<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/3/31 0031
 * Time: 17:15
 */

namespace Modules\Osce\Entities;


class SubjectSupplies extends CommonModel
{
    protected $connection   = 'osce_mis';
    protected $table        = 'subject_supplies';
    public    $timestamps   = true;
    protected $primaryKey   = 'id';
    public    $incrementing = true;
    protected $guarded      = [];
    protected $hidden       = [];
    protected $fillable     = ['subject_id', 'supplies_id', 'num', 'created_user_id'];


    public function subject(){
        return $this->hasOne('\Modules\Osce\Entities\Subject', 'id', 'subject_id');
    }

   public function supplies(){
       return $this->hasOne('\Modules\Osce\Entities\supplies', 'id', 'supplies_id');
   }

}