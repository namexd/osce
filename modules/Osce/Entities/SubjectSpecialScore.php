<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/5/7 0007
 * Time: 14:00
 */

namespace Modules\Osce\Entities;


class SubjectSpecialScore extends CommonModel
{
    protected $connection   = 'osce_mis';
    protected $table        = 'subject_special_score';
    public    $timestamps   = true;
    protected $primaryKey   = 'id';
    public    $incrementing = true;
    protected $guarded      = [];
    protected $hidden       = [];
    protected $fillable     = ['subject_id', 'title', 'score', 'created_user_id'];


    public function subject(){
        return $this->hasOne('\Modules\Osce\Entities\Subject', 'id', 'subject_id');
    }

}